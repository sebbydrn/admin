<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Receiver;
use App\User;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;

class ReceiverController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_receiver')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_receiver')->only(['create', 'add']);
        $this->middleware('permission:edit_receiver')->only(['edit', 'update']);
        $this->middleware('permission:delete_receiver')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('receivers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('receivers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'user_id' => 'required',
            'receive_type' => 'required'
        ], [
            'user_id.required' => 'The name field is required.',
        ]);

        $receiver_model = new Receiver();

        $data = array(
            'user_id' => $request->user_id,
            'receive_type' => $request->receive_type
        );

        DB::beginTransaction();
        try {
            // Add receiver
            $res = Receiver::create($data);
            $receiver_id = $res->receiver_id;

            // Add log
            $log = array(
                'receiver_id' => $receiver_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new receiver",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $receiver_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Receiver successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('receivers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get auto response
        $receiver_model = new Receiver();
        $data = $receiver_model->receiver($id);
        $date_created = $receiver_model->get_date_created($id);
        $date_updated = $receiver_model->get_date_updated($id);

        return view('receivers.show')
        ->with(compact('data'))
        ->with(compact('date_created'))
        ->with(compact('date_updated'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // Get data
        $receiver_model = new Receiver();
        $data = $receiver_model->receiver($id);

        return view('receivers.edit')
        ->with(compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $this->validate($request, ['receive_type' => 'required']);

        DB::beginTransaction();
        try {
            // Update receiver
            $receiver = Receiver::find($id);
            $receiver->receive_type = $request->receive_type;
            $receiver->save();

            $receiver_model = new Receiver();

            // Check if original value is different from changed value
            // If true save as log
            if ($request->old_receive_type != $request->receive_type) {
                $log = array(
                    'receiver_id' => $id,
                    'user_id' => Auth::user()->user_id,
                    'browser' => $this->browser(),
                    'activity' => "Updated receiver",
                    'device' => $this->device(),
                    'ip_env_address' => $request->ip(),
                    'ip_server_address' => request()->server('SERVER_ADDR'),
                    'old_value' => $request->old_receive_type,
                    'new_value' => $request->receive_type,
                    'OS' => $this->operating_system()
                );

                $res = $receiver_model->add_log($log);
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Receiver successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('receivers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $receiver_model = new Receiver();

        // Delete log
        $log = array(
            'receiver_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted receiver",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $receiver_model->delete_receiver($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $receiver_model = new Receiver();
        $receivers = $receiver_model->receivers();

        $data = $receivers;

        return Datatables::of($data)
        ->addColumn('receive_type', function($data) {
            $status = '';
            if ($data['receive_type'] == 1) {
                $status .= '<span class="badge badge-primary">Main Recipient</span>&nbsp;&nbsp;';
            } else if ($data['receive_type'] == 2) {
                $status .= '<span class="badge badge-secondary">Carbon Copy</span>&nbsp;&nbsp;';
            } else if ($data['receive_type'] == 3) {
                $status .= '<span class="badge badge-dark">Blind Carbon Copy</span>&nbsp;&nbsp;';
            }

            return $status;
        })
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_receiver')) {
                $buttons .= '<a href="'.route('receivers.show', $data['receiver_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_receiver')) {
                $buttons .= '<a href="'.route('receivers.edit', $data['receiver_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_receiver')) {
                $buttons .= '<button onclick="delete_receiver('.$data['receiver_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['receive_type', 'actions'])
        ->make(true);
    }

    public function users() {
        // Get users
        $users = User::get();

        $data = array();

        foreach ($users as $user) {
            $data[] = array(
                'user_id' => $user->user_id,
                'name' => $user->fullname
            );
        }

        echo json_encode($data);
    }
}
