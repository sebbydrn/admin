<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\System;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use Entrust;

class SystemController extends Controller
{   
    public function __construct() {
        $this->middleware('permission:view_system')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_system')->only(['create', 'add']);
        $this->middleware('permission:edit_system')->only(['edit', 'update']);
        $this->middleware('permission:delete_system')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('systems.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {   
        return view('systems.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|unique:systems,name',
            'display_name' => 'required|unique:systems,display_name'
        ]);

        $system = new System;

        $data = array(
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description
        );

        DB::beginTransaction();
        try {
            // Add system
            $res = System::create($data);
            $system_id = $res->system_id;

            // Add log
            $log = array(
                'system_id' => $system_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new system",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );
            $res2 = $system->add_log($log);
            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'System successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('systems.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get system
        $system = new System;
        $data = $system->system($id);
        $date_created = $system->get_date_created($id);
        $date_updated = $system->get_date_updated($id);

        return view('systems.show')
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
        $system = new System;
        $data = $system->system($id);

        return view('systems.edit')->with(compact('data'));
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
            'name' => 'required|unique:systems,name,'.$id.',system_id',
            'display_name' => 'required|unique:systems,display_name,'.$id.',system_id'
        ]);

        $data = array(
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description
        );

        $old_data = array(
            'old_name' => $request->old_name,
            'old_display_name' => $request->old_display_name,
            'old_description' => $request->old_description
        );

        DB::beginTransaction();
        try {
            // Update system
            $system = System::find($id);
            $system->name = $request->name;
            $system->display_name = $request->display_name;
            $system->description = $request->description;
            $system->save();

            $system2 = new System;

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'system_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated system",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res2 = $system2->add_log($log);
                }
            }

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'System successfully updated.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('systems.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $system = new System;

        // Delete log
        $log = array(
            'system_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted system",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $system->delete_system($id, $log);
        echo json_encode($res);
    }

    public function datatable() {
        // Get data
        $system = new System;
        $systems = $system->systems();

        $data = array();

        foreach ($systems as $item) {
            $data[] = array(
                'id' => $item->system_id,
                'name' => $item->display_name,
                'description' => $item->description
            );
        }

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_system')) {
                $buttons .= '<a href="'.route('systems.show', $data['id']).'" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_system')) {
                $buttons .= '<a href="'.route('systems.edit', $data['id']).'" class="btn btn-warning btn-sm" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_system')) {
                $buttons .= '<button onclick="delete_system('.$data['id'].')" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
