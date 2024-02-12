<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\PendingRegistration;
use Yajra\Datatables\Datatables;
use App\Role;
use App\User;
use App\System;
use App\Passwords;
use App\UserRoleSystem;
use Hash;
use PHPMailer\PHPMailer;
use Entrust;
use DB;

class PendingRegistrationController extends Controller
{
    public function __construct() {
        $this->middleware('permission:view_pending_registration')->only(['index', 'datatable']);
        $this->middleware('permission:approve_pending_registration')->only(['edit', 'updated', 'disapprove', 'update_disapprove']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('pending_registrations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Get data
        $pending_registration = new PendingRegistration();
        $user_data = $pending_registration->pending_registration($id);

        // User affiliation
        $user_model = new User();
        $user_affiliation = $user_model->get_user_affiliation($id);

        // User country
        $user_country = "";
        $countries = $this->countries();
        foreach ($countries as $key => $value) {
            if ($key == str_replace(' ', '', $user_data->country)) {
                $user_country = $value;
            }
        }

        // User province
        $user_province = ($user_data->province != NULL) ? $user_model->get_user_province($user_data->province) : '';

        // User municipality 
        $user_municipality = ($user_data->municipality != NULL) ? $user_model->get_user_municipality($user_data->municipality): '';

        // User region
        $user_region = ($user_data->region != NULL) ? $user_model->get_user_region($user_data->region) : '';

        // User created
        $user_created = $user_model->get_date_created($id);

        // Get roles
        $role = new Role();
        $roles = $role->getRoles2();

        // Get systems list
        $system_model = new System();
        $systems = $system_model->systems();

        return view('pending_registrations.edit')
        ->with(compact('user_data'))
        ->with(compact('roles'))
        ->with(compact('systems'))
        ->with(compact('user_affiliation'))
        ->with(compact('user_country'))
        ->with(compact('user_province'))
        ->with(compact('user_municipality'))
        ->with(compact('user_region'))
        ->with(compact('user_created'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'system' => 'required',
            'role' => 'required'
        ]);

        $data = array(
            'user_id' => $id,
            'role_id' => $request->role,
            'system_id' => $request->system
        );

        $user_model = new User;

        DB::beginTransaction();
        try {
            // Add user role
            $res = UserRoleSystem::create($data);

            // Update user to approved
            $user = User::find($data['user_id']);
            $user->isapproved = 1;
            $user->save();

            if ($request->with_email == NULL) {
                // Add generated password
                $password = Hash::make("rsis2019"); // default password is rsis2019
                $password_data = array(
                    'password' => $password,
                    'user_id' => $data['user_id'],
                    'system_id' => 0
                );

                Passwords::create($password_data);

                // Activate user
                $user = User::find($data['user_id']);
                $user->isactive = 1;
                $user->save();
            }

            // Add log
            $log = array(
                'activity_id' => 12,
                'user_id' => $data['user_id'],
                'browser' => $this->browser(),
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $user_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        // Add user roles
        $pending_registration_model = new PendingRegistration();

        if ($res2 == "success") {
            if ($request->with_email == "yes") {
                // Get user details for email
                $user = new User;
                $userdata = $user_model->getUser($data['user_id']);

                // Create link for email
                // Email link is user_id, timestamp and RSIS suffix encrypted to md5
                $link_id = $data['user_id'] . '' . time() . 'RSIS';
                $link_id = md5($link_id);
                $link = $link_id;

                // Save to password_links table
                $pass = $pending_registration_model->add_password_link($data['user_id'], $link);

                // Email transaction id is user_id, activity_id and timestamp encrypted to md5
                $transaction_id = $data['user_id'] . '' . 12 . '' . time();
                $transaction_id = md5($transaction_id);

                // Send email with confirmation link
                $content = array();
                $content['firstname'] = $userdata->firstname;
                $content['username'] = $userdata->username;
                $content['transaction_id'] = $transaction_id;
                if ($_SERVER['SERVER_NAME'] == "stagingdev.philrice.gov.ph") {
                    $content['link'] = 'https://' . $_SERVER['SERVER_NAME'] . '/rsis/portal/activate_account/' . $link;
                } else if ($_SERVER['SERVER_NAME'] == "rsis.philrice.gov.ph") {
                    $content['link'] = 'https://' . $_SERVER['SERVER_NAME'] . '/portal/activate_account/' . $link;
                } else {
                    $content['link'] = 'http://localhost/portal/activate_account/' . $link;
                }
                // $content['link'] = url('/') . '/activate_account/' . $link;
                $res3 = $this->send_email(1, $userdata->email, $content);

                if ($res3 == "success") {
                    $request->session()->flash('success', 'User registration successfully approved.');
                } else {
                    $request->session()->flash('error', $res3);
                }

                return redirect()->route('pending_registrations.index');
            } else {
                $request->session()->flash('success', 'User registration successfully approved.');
                return redirect()->route('pending_registrations.index');
            }
        } else {
            $request->session()->flash('error', $res2);
            return redirect()->route('pending_registrations.index');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function datatable() {
        // Get data
        $pending_registration = new PendingRegistration();
        $pending_registrations = $pending_registration->pending_registrations();

        $data = array();

        $user_model = new User();

        foreach ($pending_registrations as $item) {
            // User affiliation
            $user_affiliation = $user_model->get_user_affiliation($item->user_id);

            $data[] = array(
                'id' => $item->user_id,
                'name' => $item->fullname,
                'email' => $item->email,
                'username' => $item->username,
                'affiliation' => $user_affiliation->affiliation_name
            );
        }

        $data = collect($data);

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            if (Entrust::can('approve_pending_registration')) {
                $buttons = '<a href="'.route('pending_registrations.edit', $data['id']).'" class="btn btn-success btn-sm" title="Approve"><i class="fa fa-check"></i> Approve</a>&nbsp;&nbsp;<a href="'.route('pending_registrations.disapprove', $data['id']).'" class="btn btn-danger btn-sm" title="Disapprove"><i class="fa fa-ban"></i> Dissapprove</a>';
            } else {
                $buttons = '';
            }

            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function send_email($approved, $email, $content) {
        $mail = new PHPMailer\PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->isSMTP();  // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'rsis.bpi.philrice@gmail.com'; // SMTP username
            // $mail->Password = 'rsisDA_2020'; // SMTP password
            $mail->Password = 'nbyklvyfxpemkydo';
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to

            //Recipients
            $mail->setFrom('rsis.bpi.philrice@gmail.com', 'Rice Seed Information System');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            
            if ($approved == 1) {
                $mail->Subject = 'RSIS User ' .$content['username']. ' Registration';
                $mail->Body    = $this->email_content(1, $content);
            } else {
                $mail->Subject = 'RSIS User ' .$content['username']. ' Decline';
                $mail->Body    = $this->email_content(2, $content);
            }
            
            $mail->AltBody = 'Please enable HTML content to view this email.';

            $mail->send();
            return "success";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function email_content($approved, $content) {
        if ($approved == 1) {
            return view('email.success_registration')->with($content);
        } else {
            return view('email.disapprove')->with($content);
        }
    }

    public function disapprove($id) {
        // // Get data
        // $pending_registration = new PendingRegistration();
        // $data = $pending_registration->pending_registration($id);

        // return view('pending_registrations.disapprove')
        // ->with(compact('data'));

        // Get data
        $pending_registration = new PendingRegistration();
        $user_data = $pending_registration->pending_registration($id);

        // User affiliation
        $user_model = new User();
        $user_affiliation = $user_model->get_user_affiliation($id);

        // User country
        $user_country = "";
        $countries = $this->countries();
        foreach ($countries as $key => $value) {
            if ($key == str_replace(' ', '', $user_data->country)) {
                $user_country = $value;
            }
        }

        // User province
        $user_province = ($user_data->province != NULL) ? $user_model->get_user_province($user_data->province) : '';

        // User municipality 
        $user_municipality = ($user_data->municipality != NULL) ? $user_model->get_user_municipality($user_data->municipality): '';

        // User region
        $user_region = ($user_data->region != NULL) ? $user_model->get_user_region($user_data->region) : '';

        // User created
        $user_created = $user_model->get_date_created($id);

        return view('pending_registrations.disapprove')
        ->with(compact('user_data'))
        ->with(compact('user_affiliation'))
        ->with(compact('user_country'))
        ->with(compact('user_province'))
        ->with(compact('user_municipality'))
        ->with(compact('user_region'))
        ->with(compact('user_created'));
    }

    public function update_disapprove(Request $request, $id) {
        $user_model = new User;

        DB::beginTransaction();
        try {
            // Update user to disapproved and add reasons
            $user = User::find($id);
            $user->isapproved = 2;
            $user->disapprove_reasons = $request->reasons;
            $user->save();

            // Add log
            $log = array(
                'activity_id' => 13,
                'user_id' => $id,
                'browser' => $this->browser(),
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res = $user_model->add_log($log);

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        // Update user database
        // $pending_registration = new PendingRegistration();
        // $res = $pending_registration->add_reasons($id, $request->reasons);

        if ($res == "success") {
            // Get user details for email
            $userdata = $user_model->getUser($id);

            // Send email with confirmation link
            $content = array();
            $content['firstname'] = $userdata->firstname;
            $content['username'] = $userdata->username;
            $content['reasons'] = $userdata->disapprove_reasons;
            $res2 = $this->send_email(2, $userdata->email, $content);        

            if ($res2 == "success") {
                $request->session()->flash('success', 'User registration successfully disapproved.');
            } else {
                $request->session()->flash('error', $res2);
            }

            return redirect()->route('pending_registrations.index');
        } else {
            echo $res;
        }
    }
}
