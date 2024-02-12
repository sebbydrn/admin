<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inquiry;
use App\Response;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;
use PHPMailer\PHPMailer;

class InquiryController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_inquiry')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:send_response')->only(['create_response', 'send_response']);
    }
    
	public function index() {
		return view('inquiry.index');
	}

	public function show($id) {
		// Get inquiry
        $inquiry_model = new Inquiry();
        $data = $inquiry_model->inquiry($id);
        $date_created = $inquiry_model->get_date_created($id);

        return view('inquiry.show')
        ->with(compact('data'))
        ->with(compact('date_created'));
	}

	public function datatable() {
		// Get data
        $inquiry_model = new Inquiry();
        $inquiries = $inquiry_model->inquiries();

        $data = $inquiries;

        return Datatables::of($data)
        ->addColumn('status', function($data) {
            $status = '';
            if ($data['status'] == 1) {
                $status .= '<span class="badge badge-success">Responded</span>&nbsp;&nbsp;';
            } else if ($data['status'] == 0) {
                $status .= '<span class="badge badge-warning" style="color: white;">Pending Inquiry</span>&nbsp;&nbsp;';
            }

            return $status;
        })
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_inquiry')) {
                $buttons .= '<a href="'.route('inquiries.show', $data['inquiry_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('send_response') && $data['status'] == 0) {
                $buttons .= '<a href="'.route('inquiries.create_response', $data['inquiry_id']).'" class="btn btn-primary btn-sm action_buttons" title="Respond"><i class="fa fa-edit"></i> Respond</a>&nbsp;&nbsp;';
            }
            
            return $buttons;
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
	}

	public function create_response($inquiry_id) {
        // Get inquiry
        $inquiry_model = new Inquiry();
        $inquiry = $inquiry_model->inquiry($inquiry_id);

        return view('inquiry.create_response')->with(compact('inquiry'));
	}

    public function send_response(Request $request) {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required'
        ]);

        $response_model = new Response();
        $inquiry_model = new Inquiry();

        $data = array(
            'title' => $request->title,
            'body' => htmlspecialchars($request->body),
            'email_registered' => $request->email
        );

        DB::beginTransaction();
        try {
            // Add reponse
            $res = Response::create($data);
            $response_id = $res->response_id;

            // Add log (response_activities)
            $log = array(
                'response_id' => $response_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Created response",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $response_model->add_log($log);

            // Get email of inquiry sender
            $inquiry = Inquiry::find($request->inquiry_id);

            // Send response email
            $email_data = array(
                'recipient_email' => $inquiry->email, // recipient email
                'recipient' => $inquiry->sender,
                'title' => $request->title,
                'body' => $request->body
            );

            $sent = $this->send_email($email_data);

            if ($sent == "success") {
                // Add log (response_inquiry)
                $log = array(
                    'response_id' => $response_id,
                    'inquiry_id' => $request->inquiry_id,
                    'browser' => $this->browser(),
                    'activity' => "Sent response",
                    'device' => $this->device(),
                    'ip_env_address' => $request->ip(),
                    'ip_server_address' => request()->server('SERVER_ADDR'),
                    'OS' => $this->operating_system()
                ); 

                $res2 = $inquiry_model->add_log($log);
            }
            

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Your response was successfully sent.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('inquiries.index');
    }

    public function send_email($data) {
        $mail = new PHPMailer\PHPMailer(true);

        try {
            // Server settings
            // $mail->SMTPDebug = 2; // Enable verbose debug output
            $mail->isSMTP();  // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'rsis.bpi.philrice@gmail.com'; // SMTP username
            $mail->Password = 'rsisDA@2020'; // SMTP password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('rsis.bpi.philrice@gmail.com', 'Rice Seed Information System');
            $mail->addAddress($data['recipient_email'], $data['recipient']);
            
            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = $data['title'];
            $mail->Body    = $this->email_content($data);
            $mail->AltBody = 'Please enable HTML content to view this email.';

            $mail->send();

            return "success";
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function email_content($data) {
        return view('email.response')->with($data);
    }

}
