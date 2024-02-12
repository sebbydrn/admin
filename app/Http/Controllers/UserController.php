<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Role;
use App\AffiliationUser;
use App\Passwords;
use App\System;
use App\UserRoleSystem;
use Yajra\Datatables\Datatables;
use DB;
use Hash;
use Auth;
use Illuminate\Validation\Rule;
use Entrust;

class UserController extends Controller
{   
    public function __construct() {
        $this->middleware('permission:view_user')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_user')->only(['create', 'add']);
        $this->middleware('permission:edit_user')->only(['edit', 'update']);
        $this->middleware('permission:delete_user')->only(['destroy']);
        $this->middleware('permission:add_system_user_role')->only(['add_roles', 'store_roles', 'delete_roles']);
        $this->middleware('permission:change_password')->only(['change_password', 'store_password']);
        $this->middleware('permission:deactivate_user')->only(['deactivate', 'activate']);
        $this->middleware('permission:force_logout')->only(['force_logout']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get total number of users
        $users = User::count();

        // Get total number of active users
        $active_users = User::where('isactive', '=', 1)->count();

        // Get total number of deactivated users
        $deactivated_users = User::where('isactive', '=', 2)->count();

        return view('users.index')->with(compact('users', 'active_users', 'deactivated_users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Stations
        $stations = $this->stations();
        
        // Roles
        // $role = new Role();
        // $roles = $role->getRoles2();

        // Countries
        $countries = $this->countries();

        // Provinces
        $provinces = $this->provinces();

        // Affiliations
        $affiliations = $this->affiliations();

        return view('users.create')
        ->with(compact('stations'))
        // ->with(compact('roles'))
        ->with(compact('countries'))
        ->with(compact('provinces'))
        ->with(compact('affiliations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|same:password_confirmation|min:6',
            'email' => 'required|email|unique:users,email',
            'secondaryEmail' => 'email|unique:users,email',
            // 'birthday' => 'required',
            'age' => 'required',
            'contact_no' => 'unique:users,contact_no',
            // 'country' => 'required',
            'province' => 'required_if:country,PH',
            'municipality' => 'required_if:country,PH',
            'barangay' => 'required_if:country,PH',
            'affiliation' => 'required',
            'station' => 'required_if:affiliation,1',
            'philrice_idno' => 'nullable|required_if:affiliation,1|unique:users,philrice_idno',
            'agency' => 'required_if:affiliation,6',
            'school' => 'required_if:affiliation,5',
        ], [
            'firstname.required' => 'The first name field is required.',
            'lastname.required' => 'The last name field is required.',
            'contact_no.unique' => 'The contact no. has already been taken.',
            'province.required_if' => 'The province field is required when country is Philippines.',
            'municipality.required_if' => 'The municipality field is required when country is Philippines.',
            'barangay.required_if' => 'The barangay field is required when country is Philippines.',
            'philrice_idno.required_if' => 'The PhilRice ID No. field is required if you selected a PhilRice as affiliation.',
            'philrice_idno.unique' => 'The PhilRice ID No. has already been taken.',
            'station.required_if' => 'The PhilRice Station field is required if you selected PhilRice as affiliation.',
            'agency.required_if' => 'The Agency field is required if you selected Researcher as affiliation.',
            'school.required_if' => 'The University/ School field is required if you selected Student as affiliation.'
        ]);

        // $input = $request->all();
        $password = Hash::make($request->password);

        // Fullname of user
        if ($request->middlename == "" && $request->extname == "") {
            $fullname = $request->firstname . ' ' . $request->lastname;
        } elseif ($request->middlename != "" && $request->extname == "") {
            $fullname = $request->firstname . ' ' . $request->middlename . ' ' . $request->lastname;
        } elseif ($request->middlename == "" && $request->extname != "") {
            $fullname = $request->firstname . ' ' . $request->lastname . ' ' . $request->extname;
        } elseif ($request->middlename != "" && $request->extname != "") {
            $fullname = $request->firstname . ' ' . $request->middlename . ' ' . $request->lastname . ' ' . $request->extname;
        }

        $user = new User();

        $user_data = array(
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'extname' => $request->extname,
            'fullname' => $fullname,
            'username' => $request->username,
            'email' => $request->email,
            'secondaryemail' => $request->secondaryemail,
            // 'birthday' => $request->birthday,
            'age' => $request->age,
            'sex' => $request->sex,
            'contact_no' => $request->contact_no,
            // 'country' => $request->country,
            'region' => $request->region,
            'province' => $request->province,
            'municipality' => $request->municipality,
            'barangay' => $request->barangay,
            'philrice_idno' => ($request->affiliation == 1) ? $request->philrice_idno : '',
            'designation' => $request->designation,
            'cooperative' => ($request->affiliation == 3 || $request->affiliation == 9) ? $request->coop : '',
            'agency' => ($request->affiliation == 6) ? $request->agency : '',
            'school' => ($request->affiliation == 5) ? $request->school : '',
            'accreditation_no' => ($request->affiliation == 3 || $request->affiliation == 9) ? $request->accreditation_no : '',
            'isactive' => 0
        );

        DB::beginTransaction();
        try {
            // Add user
            $res = User::create($user_data);
            $user_id = $res->user_id;

            $password_data = array(
                'password' => $password,
                'user_id' => $user_id,
                'system_id' => 0
            );

            $res2 = Passwords::create($password_data);

            $affiliation_data = array(
                'affiliation_id' => $request->affiliation,
                'user_id' => $user_id,
                'affiliated_to' => ($request->affiliation == 1) ? $request->station : 0
            );

            $res3 = AffiliationUser::create($affiliation_data);

            // Add log
            $log = array(
                'activity_id' => 1,
                'user_id' => $user_id,
                'browser' => $this->browser(),
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res4 = $user->add_log($log);

            DB::commit();
            $res4 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res4 = $e->getMessage();
        }

        if ($res4 == "success") {
            $request->session()->flash('success', 'User successfully added.');
        } else {
            $request->session()->flash('error', $res4);
        }

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // User data
        $user = new User();
        $user_data = $user->getUser($id);

        // User affiliation
        $user_affiliation = $user->get_user_affiliation($id);
        if ($user_affiliation == NULL) {
            $user_affiliation = '';
        }

        // User country
        $user_country = "";
        $countries = $this->countries();
        foreach ($countries as $key => $value) {
            if ($key == str_replace(' ', '', $user_data->country)) {
                $user_country = $value;
            }
        }

        // User province
        $user_province = ($user_data->province != NULL) ? $user->get_user_province($user_data->province) : '';

        // User municipality 
        $user_municipality = ($user_data->municipality != NULL) ? $user->get_user_municipality($user_data->municipality): '';

        // User region
        $user_region = ($user_data->region != NULL) ? $user->get_user_region($user_data->region) : '';

        // User created
        $user_created = $user->get_date_created($id);

        // User last updated
        $user_updated = $user->get_date_updated($id);

        // Get user's exisiting roles
        $role_model = new Role;
        $user_roles = $role_model->getRoles($id);

        return view('users.show')
        ->with(compact('user_data'))
        ->with(compact('user_affiliation'))
        ->with(compact('user_country'))
        ->with(compact('user_region'))
        ->with(compact('user_province'))
        ->with(compact('user_municipality'))
        ->with(compact('user_created'))
        ->with(compact('user_updated'))
        ->with(compact('user_roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Get user
        $user = new User();
        $user_data = $user->getUser($id);

        // Get municipalities for user's province
        $province = $user->get_province_id($user_data->province);
        if ($province) {
            $province_id = $province->province_id;
            $municipalities = $user->get_municipalities($province_id);
        } else {
            $municipalities = '';
        }
        
        // Stations
        $stations = $this->stations();

        // Countries
        $countries = $this->countries();

        // Provinces
        $provinces = $this->provinces();

        // Affiliations
        $affiliations = $this->affiliations();

        // Get user affiliation
        $user_affiliation = $user->get_user_affiliation($id);
        if ($user_affiliation == NULL) {
            $user_affiliation = '';
        }

        return view('users.edit')
        ->with(compact('user_data'))
        ->with(compact('stations'))
        ->with(compact('countries'))
        ->with(compact('provinces'))
        ->with(compact('municipalities'))
        ->with(compact('affiliations'))
        ->with(compact('user_affiliation'));
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
            'firstname' => 'required',
            'lastname' => 'required',
            'username' => 'required|unique:users,username, '.$id.',user_id',
            'email' => 'required|email|unique:users,email, '.$id.',user_id',
            'secondaryEmail' => 'email|unique:users,email, '.$id.',user_id',
            // 'birthday' => 'required',
            'age' => 'required',
            'contact_no' => 'unique:users,contact_no, '.$id.',user_id',
            // 'country' => 'required',
            'province' => 'required_if:country,PH',
            'municipality' => 'required_if:country,PH',
            'barangay' => 'required_if:country,PH',
            'affiliation' => 'required',
            'station' => 'required_if:affiliation,1',
            'philrice_idno' => 'nullable|required_if:affiliation,1|unique:users,philrice_idno, '.$id.',user_id',
            'agency' => 'required_if:affiliation,6',
            'school' => 'required_if:affiliation,5',
        ], [
            'firstname.required' => 'The first name field is required.',
            'lastname.required' => 'The last name field is required',
            'contact_no.unique' => 'The contact no. has already been taken.',
            'province.required_if' => 'The province field is required when country is Philippines.',
            'municipality.required_if' => 'The municipality field is required when country is Philippines.',
            'barangay.required_if' => 'The barangay field is required when country is Philippines.',
            'philrice_idno.required_if' => 'The PhilRice ID No. field is required if you selected a PhilRice as affiliation.',
            'philrice_idno.unique' => 'The PhilRice ID No. has already been taken.',
            'station.required_if' => 'The PhilRice Station field is required if you selected PhilRice as affiliation.',
            'agency.required_if' => 'The Agency field is required if you selected Researcher as affiliation.',
            'school.required_if' => 'The University/ School field is required if you selected Student as affiliation.'
        ]);

        // Fullname of user
        if ($request->middlename == "" && $request->extname == "") {
            $fullname = $request->firstname . ' ' . $request->lastname;
        } elseif ($request->middlename != "" && $request->extname == "") {
            $fullname = $request->firstname . ' ' . $request->middlename . ' ' . $request->lastname;
        } elseif ($request->middlename == "" && $request->extname != "") {
            $fullname = $request->firstname . ' ' . $request->lastname . ' ' . $request->extname;
        } elseif ($request->middlename != "" && $request->extname != "") {
            $fullname = $request->firstname . ' ' . $request->middlename . ' ' . $request->lastname . ' ' . $request->extname;
        }

        $user_model = new User();

        $user_data = array(
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'extname' => $request->extname,
            'fullname' => $fullname,
            'username' => $request->username,
            'email' => $request->email,
            'secondaryemail' => $request->secondaryemail,
            // 'birthday' => $request->birthday,
            'age' => $request->age,
            'sex' => $request->sex,
            'contact_no' => $request->contact_no,
            // 'country' => $request->country,
            'region' => $request->region,
            'province' => $request->province,
            'municipality' => $request->municipality,
            'barangay' => $request->barangay,
            'philrice_idno' => ($request->affiliation == 1) ? $request->philrice_idno : '',
            'designation' => $request->designation,
            'affiliation' => $request->affiliation,
            'station' => ($request->affiliation == 1) ? $request->station : '',
            'cooperative' => ($request->affiliation == 3 || $request->affiliation == 9) ? $request->coop : '',
            'agency' => ($request->affiliation == 6) ? $request->agency : '',
            'school' => ($request->affiliation == 5) ? $request->school : '',
            'accreditation_no' => ($request->affiliation == 3 || $request->affiliation == 9) ? $request->accreditation_no : ''
        );

        $old_user_data = array(
            'old_firstname' => $request->old_firstname,
            'old_middlename' => $request->old_middlename,
            'old_lastname' => $request->old_lastname,
            'old_extname' => $request->old_extname,
            'old_fullname' => $request->old_fullname,
            'old_username' => $request->old_username,
            'old_email' => $request->old_email,
            'old_secondaryemail' => $request->old_secondaryemail,
            'old_age' => $request->old_age,
            // 'old_birthday' => $request->old_birthday,
            'old_sex' => $request->old_sex,
            'old_contact_no' => $request->old_contact_no,
            // 'old_country' => $request->old_country,
            'old_region' => $request->old_region,
            'old_province' => $request->old_province,
            'old_municipality' => $request->old_municipality,
            'old_barangay' => $request->barangay,
            'old_philrice_idno' => $request->old_philrice_idno,
            'old_designation' => $request->old_designation,
            'old_affiliation' => $request->old_affiliation,
            'old_station' => $request->old_station,
            'old_cooperative' => $request->old_coop,
            'old_agency' => $request->old_agency,
            'old_school' => $request->old_school,
            'old_accreditation_no' => $request->old_accreditation_no
        );

        DB::beginTransaction();
        try {
            // Update user
            $user = User::find($id);
            $user->firstname = $user_data['firstname'];
            $user->middlename = $user_data['middlename'];
            $user->lastname = $user_data['lastname'];
            $user->extname = $user_data['extname'];
            $user->fullname = $user_data['fullname'];
            $user->username = $user_data['username'];
            $user->email = $user_data['email'];
            $user->secondaryemail = $user_data['secondaryemail'];
            // $user->birthday = $user_data['birthday'];
            $user->age = $user_data['age'];
            $user->sex = $user_data['sex'];
            $user->contact_no = $user_data['contact_no'];
            // $user->country = $user_data['country'];
            $user->region = $user_data['region'];
            $user->province = $user_data['province'];
            $user->municipality = $user_data['municipality'];
            $user->barangay = $user_data['barangay'];
            $user->philrice_idno = $user_data['philrice_idno'];
            $user->designation = $user_data['designation'];
            $user->cooperative = $user_data['cooperative'];
            $user->agency = $user_data['agency'];
            $user->school = $user_data['school'];
            $user->accreditation_no = $user_data['accreditation_no'];
            $user->save();

            // Update user affiliation
            /*$user_affiliation = AffiliationUser::where('user_id', $id)
            ->update([
                'affiliation_id' => $user_data['affiliation'],
                'affiliated_to' => $user_data['station']
            ]);*/
            
            if ($old_user_data['old_affiliation'] != '') {
                AffiliationUser::where('user_id', $id)
                ->update([
                    'affiliation_id' => $user_data['affiliation'],
                    'affiliated_to' => ($user_data['station']) ? $user_data['station'] : 0
                ]); 
            } else {
                $user_affiliation = AffiliationUser::updateOrCreate([
                    'user_id' => $id,
                    'affiliation_id' => $user_data['affiliation'],
                    'affiliated_to' => $user_data['station']
                ]);
            }
            

            // if ($user_data['affiliation'] == 1) {
            //     $user_affiliation = AffiliationUser::updateOrCreate([
            //         'user_id' => $id,
            //         'affiliation_id' => $user_data['affiliation'],
            //         'affiliated_to' => $user_data['station']
            //     ]);
            // } else {
            //     if ($user_data['affiliation'] != $old_user_data['old_affiliation']) {
            //         $user_affiliation = AffiliationUser::updateOrCreate([
            //             'user_id' => $id,
            //             'affiliation_id' => $user_data['affiliation'],
            //             'affiliated_to' => 0
            //         ]);  
            //     } else {
            //         $user_affiliation = AffiliationUser::updateOrCreate([
            //             'user_id' => $id,
            //             'affiliation_id' => $user_data['affiliation'],
            //             'affiliated_to' => 0
            //         ]);
            //     }
            // }
            

            // Check if original value is different from changed value
            // If true save as log
            foreach ($user_data as $key => $value) {
                if ($old_user_data['old_'.$key] != $value) {
                    $log = array(
                        'activity_id' => 4,
                        'user_id' => $id,
                        'browser' => $this->browser(),
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_user_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $user_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'User successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        if($request->has('portal_edit'))
        {
            return redirect('../portal/profile');
        }
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user_model = new User();

        // Delete log
        $log = array(
            'activity_id' => 8,
            'user_id' => $id,
            'browser' => $this->browser(),
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        // Delete user and add log
        $result = $user_model->deleteUser($id, $log);

        echo json_encode($result);
    }

    // Users datatable
    public function datatable()
    {
        // Get data
        $user = new User();
        $users = $user->getUsers();

        $data = array();

        $role = new Role();
        foreach ($users as $item) {
            // Show only users who is not flagged deleted
            if ($item->isdeleted == 0) {
                // Get user's role
                $roles = $role->getRoles($item->user_id);

                // Get user logged in status
                $user_logged_in = $user->get_logged_in_status($item->user_id);

                $data[] = array(
                    'id' => $item->user_id,
                    'name' => $item->fullname,
                    'email' => $item->email,
                    'username' => $item->username,
                    'isactive' => $item->isactive,
                    'isdeleted' => $item->isdeleted,
                    'roles' => $roles,
                    'user_logged_in' => $user_logged_in
                );
            }
        }

        $data = collect($data);

        return Datatables::of($data)
        ->addColumn('status', function($data) {
            if ($data['isactive'] == 0) {
                $status = '<span class="badge badge-warning" style="color: white;">Pending Activation</span>&nbsp;';
            } else if ($data['isactive'] == 1) {
                $status = '<span class="badge badge-success">Active</span>&nbsp;';
            } else if ($data['isactive'] == 2) {
                $status = '<span class="badge badge-danger">Deactivated</span>&nbsp;';
            }

            if ($data['user_logged_in'] > 0) {
                $status .= '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Logged In</span>';
            }

            return $status;
        })
        ->addColumn('roles', function($data) {
            $roles = "";
            foreach ($data['roles'] as $item) {
                $roles .= '<span class="badge badge-primary">'.$item->display_name.' ('.$item->system_display_name.')</span>&nbsp;';
            }

            return $roles;
        })
        ->addColumn('actions', function($data) {
            $buttons = '';
            if ($data['isactive'] == 0) {
                if (Entrust::can('add_system_user_role')) {
                    $buttons .= '<a href="'.route('users.roles.add', $data['id']).'" class="btn btn-primary btn-sm action_buttons" title="Add Roles"><i class="fa fa-plus-circle"></i> Add Roles</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('view_user')) {
                    $buttons .= '<a href="'.route('users.show', $data['id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('edit_user')) {
                    $buttons .= '<a href="'.route('users.edit', $data['id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('change_password')) {
                    $buttons .= '<a class="btn btn-warning btn-sm action_buttons" href="'.route('users.change_password', $data['id']).'" title="Change Password" style="color: white;"><i class="fa fa-key"></i> Change Password</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('delete_user')) {
                    $buttons .= '<button onclick="delete_user('.$data['id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
                }
            } else if ($data['isactive'] == 1) {
                if (Entrust::can('add_system_user_role')) {
                    $buttons .= '<a href="'.route('users.roles.add', $data['id']).'" class="btn btn-primary btn-sm action_buttons" title="Add Roles"><i class="fa fa-plus-circle"></i> Add Roles</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('view_user')) {
                    $buttons .= '<a href="'.route('users.show', $data['id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('edit_user')) {
                    $buttons .= '<a href="'.route('users.edit', $data['id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('change_password')) {
                    $buttons .= '<a class="btn btn-warning btn-sm action_buttons" href="'.route('users.change_password', $data['id']).'" title="Change Password" style="color: white;"><i class="fa fa-key"></i> Change Password</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('deactivate_user')) {
                    $buttons .= '<button onclick="deactivate_user('.$data['id'].')" class="btn btn-danger btn-sm action_buttons" title="Deactivate"><i class="fa fa-ban"></i> Deactivate</button>&nbsp;&nbsp;';
                }
                if (Entrust::can('delete_user')) {
                    $buttons .= '<button onclick="delete_user('.$data['id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
                }
                if ($data['user_logged_in'] > 0 && Entrust::can('force_logout')) {
                    $buttons .= '&nbsp;&nbsp;<button onclick="force_logout('.$data['id'].')" class="btn btn-dark btn-sm action_buttons" title="Force Logout"><i class="fa fa-sign-out-alt"></i> Force Logout</button>';
                }
            } else if ($data['isactive'] == 2) {
                if (Entrust::can('add_system_user_role')) {
                    $buttons .= '<a href="'.route('users.roles.add', $data['id']).'" class="btn btn-primary btn-sm action_buttons" title="Add Roles"><i class="fa fa-plus-circle"></i> Add Roles</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('view_user')) {
                    $buttons .= '<a href="'.route('users.show', $data['id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('edit_user')) {
                    $buttons .= '<a href="'.route('users.edit', $data['id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('change_password')) {
                    $buttons .= '<a class="btn btn-warning btn-sm action_buttons" href="'.route('users.change_password', $data['id']).'" title="Change Password" style="color: white;"><i class="fa fa-key"></i> Change Password</a>&nbsp;&nbsp;';
                }
                if (Entrust::can('deactivate_user')) {
                    $buttons .= '<button onclick="activate_user('.$data['id'].')" class="btn btn-success btn-sm action_buttons" title="Activate"><i class="fa fa-check"></i> Activate</button>&nbsp;&nbsp;';
                }
                if (Entrust::can('delete_user')) {
                    $buttons .= '<button onclick="delete_user('.$data['id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
                }
            }

            return $buttons;
        })
        ->rawColumns(['status', 'roles', 'actions'])
        ->make(true);
    }

    public function region_code(Request $request) {
        $region_id = $request->region_id;
        $user = new User();
        $region_code = $user->get_region_code($region_id);
        
        echo json_encode($region_code);
    }

    public function municipalities(Request $request) {
        $province_id = $request->province_id;
        $user = new User();
        $municipalities = $user->get_municipalities($province_id);

        echo json_encode($municipalities);
    }

    // Add roles to user
    public function add_roles($user_id) {
        $role = new Role();

        // Get user's exisiting roles
        $user_roles = $role->getRoles($user_id);

        // Get roles list
        $roles = $role->getRoles2();

        $system = new System();

        // Get systems list
        $systems = $system->systems();

        return view('users.add_roles')
        ->with(compact('user_roles'))
        ->with(compact('user_id'))
        ->with(compact('roles'))
        ->with(compact('systems'));
    }

    // Store user roles
    public function store_roles(Request $request) {
        $this->validate($request, [
            'system' => 'required',
            'role' => 'required'
        ]);

        $data = array(
            'user_id' => $request->user_id,
            'role_id' => $request->role,
            'system_id' => $request->system
        );

        $user = new User;

        // Check if user role exists
        $validate = UserRoleSystem::where('user_id', $request->user_id)
        ->where('role_id', $request->role)
        ->where('system_id', $request->system)
        ->count();

        // Check if no prior user role
        $no_user_role = UserRoleSystem::where('user_id', $request->user_id)->count();

        if ($validate == 1) {
            $res2 = "User has this role already.";
        } else {
            DB::beginTransaction();
            try {
                // Add user role
                $res = UserRoleSystem::create($data);

                if ($no_user_role == 0) {
                    // Activate user
                    $user2 = User::find($request->user_id);
                    $user2->isactive = 1;
                    $user2->save();
                }

                // Add log
                $log = array(
                    'activity_id' => 2,
                    'user_id' => $request->user_id,
                    'browser' => $this->browser(),
                    'device' => $this->device(),
                    'ip_env_address' => $request->ip(),
                    'ip_server_address' => request()->server('SERVER_ADDR'),
                    'OS' => $this->operating_system()
                );

                $res2 = $user->add_log($log);

                DB::commit();
                $res2 = "success";
            } catch (Exception $e) {
                DB::rollback();
                $res2 = $e->getMessage();
            }
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Role successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('users.roles.add', $request->user_id);
    }

    // Delete user role
    public function destroy_roles(Request $request) {
        DB::beginTransaction();
        try {
            // Delete user role
            UserRoleSystem::destroy($request->user_role_system_id);

            // Delete log
            $log = array(
                'activity_id' => 3,
                'user_id' => $request->user_id,
                'browser' => $this->browser(),
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $user = new User;
            $res = $user->add_log($log);
            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }
        
        echo json_encode($res);
    }

    // User change password view
    public function change_password($user_id) {
        $user_id = $user_id;
        return view('users.change_password')->with(compact('user_id'));
    }

    // Store new password
    public function store_password(Request $request) {
        $this->validate($request, [
            'password' => 'required|same:password_confirmation|min:6',
        ]);

        $password = Hash::make($request->password);
        $user_id = $request->user_id;

        DB::beginTransaction();
        try {
            // Update password
            Passwords::where('user_id', $user_id)
            ->update(['password' => $password]);

            // Add log
            $log = array(
                'activity_id' => 5,
                'user_id' => $user_id,
                'browser' => $this->browser(),
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $user_model = new User;
            $res = $user_model->add_log($log);

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Password successfully changed.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('users.index');
    }

    // Deactivate user
    public function deactivate(Request $request) {
        $user_id = $request->user_id;

        DB::beginTransaction();
        try {
            $user = User::find($user_id);
            $user->isactive = 2;
            $user->save(); 

            // Add log
            $log = array(
                'activity_id' => 6,
                'user_id' => $user_id,
                'browser' => $this->browser(),
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'new_value' => 2,
                'old_value' => 1,
                'OS' => $this->operating_system()
            );

            $user_model = new User;
            $user_model->add_log($log);

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }
        
        echo json_encode($res);
    }

    // Activate user
    public function activate(Request $request) {
        $user_id = $request->user_id;

        DB::beginTransaction();
        try {
            $user = User::find($user_id);
            $user->isactive = 1;
            $user->save(); 

            // Add log
            $log = array(
                'activity_id' => 7,
                'user_id' => $user_id,
                'browser' => $this->browser(),
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'new_value' => 1,
                'old_value' => 2,
                'OS' => $this->operating_system()
            );

            $user_model = new User;
            $user_model->add_log($log);

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }
        
        echo json_encode($res);
    }

    // Force logout
    public function force_logout(Request $request) {
        $user_id = $request->user_id;

        $user_model = new User();

        // Add log
        $log = array(
            'activity_id' => 19,
            'user_id' => $user_id,
            'browser' => $this->browser(),
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        // remove session in sessions table
        $res = $user_model->delete_session($user_id, $log);

        echo json_encode($res);
    }
}
