<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataComplianceReceiver;
use App\User;
use Yajra\Datatables\Datatables;
use Entrust, Auth, DB;

class DataComplianceReceiverController extends Controller
{
    public function index() {
        return view('dataComplianceReceiver.index');
    }

    public function datatable() {
        // Get data
        $receivers = DataComplianceReceiver::get();

        $data = array();

        foreach ($receivers as $receiver) {
            $data[] = array(
                'id' => $receiver->id,
                'email' => $receiver->email,
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

            if (Entrust::can('delete_data_compliance_receivers')) {
                $buttons .= '<button onclick="delete_data_compliance_receiver('.$data['id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['receive_type', 'actions'])
        ->make(true);
    }

    public function create() {
        return view('dataComplianceReceiver.create');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'email' => 'required',
            'receive_type' => 'required'
        ]);

        DB::beginTransaction();
        try {
            // Add receiver
            $res = new DataComplianceReceiver;
            $res->email = $request->email;
            $res->receive_type = $request->receive_type;
            $res->save();

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Data compliance receiver successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('data_compliance_receivers.index');
    }

    public function destroy(Request $request, $id) {
        $res = DataComplianceReceiver::where('id', '=', $id)->delete();

        echo json_encode("success");
    }
}
