<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AutoResponse;
use DB;
use Entrust;
use Yajra\Datatables\Datatables;
use Auth;

class AutoResponseController extends Controller {

    public function __construct() {
        $this->middleware('permission:view_auto_response')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_auto_response')->only(['create', 'add']);
        $this->middleware('permission:edit_auto_response')->only(['edit', 'update', 'enable']);
        $this->middleware('permission:delete_auto_response')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('auto_response.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('auto_response.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'sender' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);

        $auto_response_model = new AutoResponse();

        $data = array(
            'sender' => $request->sender,
            'title' => $request->title,
            'body' => htmlspecialchars($request->body)
        );

        DB::beginTransaction();
        try {
            // Add auto response
            $res = AutoResponse::create($data);
            $auto_response_id = $res->auto_response_id;

            // Add log
            $log = array(
                'auto_response_id' => $auto_response_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new auto response",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res2 = $auto_response_model->add_log($log);

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Auto response successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('auto_response.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get auto response
        $auto_response_model = new AutoResponse();
        $data = $auto_response_model->auto_response($id);
        $date_created = $auto_response_model->get_date_created($id);
        $date_updated = $auto_response_model->get_date_updated($id);

        return view('auto_response.show')
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
        $auto_response_model = new AutoResponse();
        $data = $auto_response_model->auto_response($id);

        return view('auto_response.edit')
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
        $this->validate($request, [
            'sender' => 'required',
            'title' => 'required',
            'body' => 'required',
        ]);

        $data = array(
            'sender' => $request->sender,
            'title' => $request->title,
            'body' => htmlspecialchars($request->body)
        );

        $old_data = array(
            'old_sender' => $request->old_sender,
            'old_title' => $request->old_title,
            'old_body' => htmlspecialchars($request->old_body)
        );

        DB::beginTransaction();
        try {
            // Update auto response
            $auto_response = AutoResponse::find($id);
            $auto_response->sender = $request->sender;
            $auto_response->title = $request->title;
            $auto_response->body = htmlspecialchars($request->body);
            $auto_response->save();

            $auto_response_model = new AutoResponse();

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'auto_response_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated auto response",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res = $auto_response_model->add_log($log);
                }
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        if ($res == "success") {
            $request->session()->flash('success', 'Auto response successfully updated.');
        } else {
            $request->session()->flash('error', $res);
        }

        return redirect()->route('auto_response.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $auto_response_model = new AutoResponse();

        // Delete log
        $log = array(
            'auto_response_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted auto response",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $auto_response_model->delete_auto_response($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $auto_response_model = new AutoResponse();
        $auto_responses = $auto_response_model->auto_responses();

        $data = array();

        foreach ($auto_responses as $auto_response) {
            $data[] = array(
                'auto_response_id' => $auto_response->auto_response_id,
                'sender' => $auto_response->sender,
                'title' => $auto_response->title,
                'body' => str_limit($auto_response->body, '500', '...'),
                'is_enabled' => $auto_response->is_enabled
            );
        }

        return Datatables::of($data)
        ->addColumn('status', function($data) {
            $status = '';
            if ($data['is_enabled'] == 1) {
                $status .= '<span class="badge badge-success">Enabled</span>&nbsp;&nbsp;';
            } else if ($data['is_enabled'] == 0 || $data['is_enabled'] == '') {
                $status .= '<span class="badge badge-danger">Disabled</span>&nbsp;&nbsp;';
            }

            return $status;
        })
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_auto_response')) {
                $buttons .= '<a href="'.route('auto_response.show', $data['auto_response_id']).'" class="btn btn-info btn-sm action_buttons" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_auto_response')) {
                $buttons .= '<a href="'.route('auto_response.edit', $data['auto_response_id']).'" class="btn btn-warning btn-sm action_buttons" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
                if ($data['is_enabled'] == 0 || $data['is_enabled'] == '') {
                    $buttons .= '<button onclick="enable_auto_response('.$data['auto_response_id'].')" class="btn btn-success btn-sm action_buttons" title="Enable Auto Response"><i class="fa fa-check"></i> Enable</button>&nbsp;&nbsp;';
                } else {
                    $buttons .= '<button onclick="disable_auto_response('.$data['auto_response_id'].')" class="btn btn-danger btn-sm action_buttons" title="Disable Auto Response"><i class="fa fa-ban"></i> Disable</button>&nbsp;&nbsp;';
                }
            }
            if (Entrust::can('delete_auto_response')) {
                $buttons .= '<button onclick="delete_auto_response('.$data['auto_response_id'].')" class="btn btn-danger btn-sm action_buttons" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
    }

    public function enable(Request $request) {
        $auto_response_id = $request->auto_response_id;
        $is_enabled = $request->enable;

        DB::beginTransaction();
        try {
            $auto_response_model = new AutoResponse();

            if ($is_enabled == 1) {
                // Disable enabled auto response first before enabling new auto response
                $exist = AutoResponse::where('is_enabled', 1)->first();

                if ($exist != null) {
                    $auto_response = AutoResponse::find($exist->auto_response_id);
                    $auto_response->is_enabled = 0;
                    $auto_response->save();

                    $log = array(
                        'auto_response_id' => $exist->auto_response_id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Disabled auto response",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => 1,
                        'new_value' => 0,
                        'OS' => $this->operating_system()
                    );

                    $res = $auto_response_model->add_log($log);
                }
                    

                // Enable new auto response
                $auto_response = AutoResponse::find($auto_response_id);
                $auto_response->is_enabled = 1;
                $auto_response->save();

                $log = array(
                    'auto_response_id' => $auto_response_id,
                    'user_id' => Auth::user()->user_id,
                    'browser' => $this->browser(),
                    'activity' => "Enabled auto response",
                    'device' => $this->device(),
                    'ip_env_address' => $request->ip(),
                    'ip_server_address' => request()->server('SERVER_ADDR'),
                    'old_value' => 0,
                    'new_value' => 1,
                    'OS' => $this->operating_system()
                );

                $res = $auto_response_model->add_log($log);

            } else {
                // Disable auto response only
                // Update auto_response
                $auto_response = AutoResponse::find($auto_response_id);
                $auto_response->is_enabled = 0;
                $auto_response->save();

                $log = array(
                    'auto_response_id' => $auto_response_id,
                    'user_id' => Auth::user()->user_id,
                    'browser' => $this->browser(),
                    'activity' => "Disabled auto response",
                    'device' => $this->device(),
                    'ip_env_address' => $request->ip(),
                    'ip_server_address' => request()->server('SERVER_ADDR'),
                    'old_value' => 1,
                    'new_value' => 0,
                    'OS' => $this->operating_system()
                );

                $res = $auto_response_model->add_log($log);
            }

            DB::commit();
            $res = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }

        echo json_encode($res);
    }
}
