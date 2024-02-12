<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Permission;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use Entrust;

class PermissionController extends Controller
{
    public function __construct() {
        $this->middleware('permission:view_permission')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_permission')->only(['create', 'add']);
        $this->middleware('permission:edit_permission')->only(['edit', 'update']);
        $this->middleware('permission:delete_permission')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('permissions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'display_name' => 'required|unique:permissions,display_name|max:255',
            'name' => 'required|unique:permissions,name|max:255'
        ]);

        $permission = new Permission;

        $data = array(
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description
        );

        DB::beginTransaction();
        try {
            // Add permission
            $res = Permission::create($data);
            $permission_id = $res->permission_id;

            // Add log
            $log = array(
                'permission_id' => $permission_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new permission",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );
            $res2 = $permission->add_log($log);
            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Permission successfully added.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('permissions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get permission
        $permission = new Permission();
        $data = $permission->getPermission($id);
        $date_created = $permission->get_date_created($id);
        $date_updated = $permission->get_date_updated($id);

        return view('permissions.show')
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
        // Get permission
        $permission = new Permission();
        $data = $permission->getPermission($id);

        return view('permissions.edit')->with(compact('data'));
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
            'display_name' => 'required|unique:permissions,display_name, '.$id.',permission_id|max:255',
            'name' => 'required|unique:permissions,name, '.$id.',permission_id|max:255'
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
            // Update permission
            $permission = Permission::find($id);
            $permission->name = $request->name;
            $permission->display_name = $request->display_name;
            $permission->description = $request->description;
            $permission->save();

            $permission2 = new Permission;

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'permission_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated permission",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res2 = $permission2->add_log($log);
                }
            }

            DB::commit();
            $res2 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res2 = $e->getMessage();
        }

        if ($res2 == "success") {
            $request->session()->flash('success', 'Permission successfully updated.');
        } else {
            $request->session()->flash('error', $res2);
        }

        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $permission = new Permission();

        // Delete log
        $log = array(
            'permission_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted permission",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $result = $permission->deletePermission($id, $log);
        echo json_encode($result);
    }

    // Permissions datatable
    public function datatable() {
        // Get data
        $permission = new Permission();
        $permissions = $permission->getPermissions();

        $data = collect($permissions);

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_permission')) {
                $buttons .= '<a href="'.route('permissions.show', $data->permission_id).'" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_permission')) {
                $buttons .= '<a href="'.route('permissions.edit', $data->permission_id).'" class="btn btn-warning btn-sm" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_permission')) {
                $buttons .= '<button onclick="delete_permission('.$data->permission_id.')" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
