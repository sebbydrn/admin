<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RegNotifReceiver;
use App\RegNotifReceiverActivity;
use App\User;
use Yajra\Datatables\Datatables;
use Entrust, Auth, DB;

class RegNotifReceiverController extends Controller
{
    public function index() {
        return view('regNotifReceiver.index');
    }

    public function datatable() {
        // Get data
        $receivers = RegNotifReceiver::get();

        $data = array();

        foreach ($receivers as $receiver) {
            $user = User::select('fullname')->where('user_id', '=', $receiver->user_id)->first();

            $data[] = array(
                'receiver_id' => $receiver->receiver_id,
                'name' => $user->fullname,
                'receive_type' => $receiver->receive_type
            );
        }

        $data = $data;

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
            if (Entrust::can('view_reg_notif_receiver')) {
                $buttons .= '<a href="'.route('reg_notif_receivers.show', $data['receiver_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_reg_notif_receiver')) {
                $buttons .= '<a href="'.route('reg_notif_receivers.edit', $data['receiver_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_reg_notif_receiver')) {
                $buttons .= '<button onclick="delete_reg_notif_receiver('.$data['receiver_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['receive_type', 'actions'])
        ->make(true);
    }

    public function create() {
        return view('regNotifReceiver.create');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'user_id' => 'required',
            'receive_type' => 'required'
        ], [
            'user_id.required' => 'The name field is required.',
        ]);

        DB::beginTransaction();
        try {
            // Add receiver
            $res = new RegNotifReceiver;
            $res->user_id = $request->user_id;
            $res->receive_type = $request->receive_type;
            $res->save();
            $receiver_id = $res->receiver_id;

            // Add log
            $log = new RegNotifReceiverActivity;
            $log->receiver_id = $receiver_id;
            $log->user_id = Auth::user()->user_id;
            $log->browser = $this->browser();
            $log->activity = "Added new registration notification receiver";
            $log->device = $this->device();
            $log->ip_env_address = $request->ip();
            $log->ip_server_address = request()->server('SERVER_ADDR');
            $log->OS = $this->operating_system();
            $log->save();

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Registration notification receiver successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('reg_notif_receivers.index');
    }

    public function show($id) {
        // Get registration notification receiver
        $receiver = RegNotifReceiver::where('receiver_id', '=', $id)->first();

        $user = User::select('fullname', 'email')->where('user_id', '=', $receiver->user_id)->first();

        $receiver_created = RegNotifReceiverActivity::select('timestamp')
                                                    ->where([
                                                        ['receiver_id', '=', $id],
                                                        ['activity', '=', 'Added new registration notification receiver'],
                                                    ])
                                                    ->first();

        $receiver_updated = RegNotifReceiverActivity::select('timestamp')
                                                    ->where([
                                                        ['receiver_id', '=', $id],
                                                        ['activity', '=', 'Updated registration notification receiver']
                                                    ])
                                                    ->orderBy('timestamp', 'desc')
                                                    ->first();

        return view('regNotifReceiver.show')
        ->with(compact('receiver'))
        ->with(compact('user'))
        ->with(compact('receiver_created'))
        ->with(compact('receiver_updated'));
    }

    public function edit($id) {
        // Get data
        $receiver = RegNotifReceiver::where('receiver_id', '=', $id)->first();

        $user = User::select('fullname', 'email')->where('user_id', '=', $receiver->user_id)->first();

        return view('regNotifReceiver.edit')
        ->with(compact('receiver', 'user'));
    }

    public function update(Request $request, $id) {
        $this->validate($request, ['receive_type' => 'required']);

        DB::beginTransaction();
        try {
            // Update receiver
            $receiver = RegNotifReceiver::find($id);
            $receiver->receive_type = $request->receive_type;
            $receiver->save();

            // Check if original value is different from changed value
            // If true save as log
            if ($request->old_receive_type != $request->receive_type) {
                $log = new RegNotifReceiverActivity;
                $log->receiver_id = $id;
                $log->user_id = Auth::user()->user_id;
                $log->browser = $this->browser();
                $log->activity = "Updated registration notification receiver";
                $log->device = $this->device();
                $log->ip_env_address = $request->ip();
                $log->ip_server_address = request()->server('SERVER_ADDR');
                $log->old_value = $request->old_receive_type;
                $log->new_value = $request->receive_type;
                $log->OS = $this->operating_system();
                $log->save();
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Registration notification receiver successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('reg_notif_receivers.index');
    }

    public function destroy(Request $request, $id) {
        $res = RegNotifReceiver::where('receiver_id', '=', $id)->delete();

        $log = new RegNotifReceiverActivity;
        $log->receiver_id = $id;
        $log->user_id = Auth::user()->user_id;
        $log->browser = $this->browser();
        $log->activity = "Deleted registration notification receiver";
        $log->device = $this->device();
        $log->ip_env_address = $request->ip();
        $log->ip_server_address = request()->server('SERVER_ADDR');
        $log->old_value = $request->old_receive_type;
        $log->new_value = $request->receive_type;
        $log->OS = $this->operating_system();
        $log->save();

        echo json_encode("success");
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
