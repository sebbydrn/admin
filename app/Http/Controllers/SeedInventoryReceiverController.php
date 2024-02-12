<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeedInventoryReceiver;
use App\User;
use Yajra\Datatables\Datatables;
use Entrust, Auth, DB;


class SeedInventoryReceiverController extends Controller
{
    public function index() {
        return view('seedInventoryReceiver.index');
    }

    public function datatable() {
        // Get data
        $receivers = SeedInventoryReceiver::get();

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
                $buttons .= '<button onclick="delete_seed_inventory_receiver('.$data['id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['receive_type', 'actions'])
        ->make(true);
    }

    public function create() {
        return view('seedInventoryReceiver.create');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'email' => 'required',
            'receive_type' => 'required'
        ]);

        DB::beginTransaction();
        try {
            // Add receiver
            $res = new SeedInventoryReceiver;
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
            $request->session()->flash('success', 'Seed inventory receiver successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('seed_inventory_receivers.index');
    }

    public function destroy(Request $request, $id) {
        $res = SeedInventoryReceiver::where('id', '=', $id)->delete();

        echo json_encode("success");
    }
}
