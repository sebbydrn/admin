<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Role;
use App\Permission;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use Entrust;

class RoleController extends Controller
{
    public function __construct() {
        $this->middleware('permission:view_role')->only(['index', 'show', 'datatable']);
        $this->middleware('permission:add_role')->only(['create', 'add']);
        $this->middleware('permission:edit_role')->only(['edit', 'update']);
        $this->middleware('permission:delete_role')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Permissions
        $permission = new Permission();
        $permissions = $permission->getPermissions();

        return view('roles.create')->with(compact('permissions'));
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
            'display_name' => 'required|unique:roles,display_name|max:255',
            'name' => 'required|unique:roles,name|max:255',
            // 'permissions' => 'required'
        ]);

        $role = new Role;

        $data = array(
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description
        );

        DB::beginTransaction();
        try {
            // Add role
            $res = Role::create($data);
            $role_id = $res->role_id;

            if ($request->permissions) {
                // Add permissions to role
                foreach ($request->permissions as $permission) {
                    $role_permission = array(
                        'permission_id' => $permission,
                        'role_id' => $role_id
                    );

                    $res2 = $role->add_role_permission($role_permission);
                }
            }

            // Add log
            $log = array(
                'role_id' => $role_id,
                'user_id' => Auth::user()->user_id,
                'browser' => $this->browser(),
                'activity' => "Added new role",
                'device' => $this->device(),
                'ip_env_address' => $request->ip(),
                'ip_server_address' => request()->server('SERVER_ADDR'),
                'OS' => $this->operating_system()
            );

            $res3 = $role->add_log($log);

            DB::commit();
            $res3 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res3 = $e->getMessage();
        }

        if ($res3 == "success") {
            $request->session()->flash('success', 'Role successfully added.');
        } else {
            $request->session()->flash('error', $res3);
        }

        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        // Get role
        $role = new Role();
        $data = $role->getRole($id);

        // Get role permissions
        $permission = new Permission();
        $permissions = $permission->getRolePermissions($id);

        $date_created = $role->get_date_created($id);
        $date_updated = $role->get_date_updated($id);

        return view('roles.show')
        ->with(compact('data'))
        ->with(compact('permissions'))
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
        // Get role
        $role = new Role();
        $data = $role->getRole($id);

        // Get permissions for editing
        $permission = new Permission();
        $permissions = $permission->getPermissions();

        // Get existing role_permissions
        $role_permissions = $permission->getRolePermissions($id);
        $role_permissions_array = array();
        foreach ($role_permissions as $role_permission) {
            array_push($role_permissions_array, $role_permission->permission_id);
        }

        return view('roles.edit')
        ->with(compact('data'))
        ->with(compact('permissions'))
        ->with(compact('role_permissions_array'));
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
            'display_name' => 'required|unique:roles,display_name,'.$id.',role_id|max:255',
            'name' => 'required|unique:roles,name,'.$id.',role_id|max:255',
            'permissions' => 'required'
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
            // Update role
            $role = Role::find($id);
            $role->name = $request->name;
            $role->display_name = $request->display_name;
            $role->description = $request->description;
            $role->save();

            $role2 = new Role;

            // Old role permissions array
            $q = explode(",", $request->old_permissions);
            $old_permissions = array();
            $old_permissions = array_merge($old_permissions, $q);

            // Update role_permission
            $res2 = $role2->delete_role_permissions($id);

            foreach ($request->permissions as $permission) {
                $role_permission = array(
                    'permission_id' => $permission,
                    'role_id' => $id
                );

                $res3 = $role2->add_role_permission($role_permission);

                // Check if added new permission
                // If true save as log
                if (!in_array($permission, $old_permissions)) {
                    $log = array(
                        'role_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated role permission",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => '',
                        'new_value' => $permission,
                        'OS' => $this->operating_system()
                    );

                    $res4 = $role2->add_log($log);
                }
            }

            // Check if original value is different from changed value
            // If true save as log
            foreach ($data as $key => $value) {
                if ($old_data['old_'.$key] != $value) {
                    $log = array(
                        'role_id' => $id,
                        'user_id' => Auth::user()->user_id,
                        'browser' => $this->browser(),
                        'activity' => "Updated role",
                        'device' => $this->device(),
                        'ip_env_address' => $request->ip(),
                        'ip_server_address' => request()->server('SERVER_ADDR'),
                        'old_value' => $old_data['old_'.$key],
                        'new_value' => $value,
                        'OS' => $this->operating_system()
                    );

                    $res5 = $role2->add_log($log);
                }
            }

            DB::commit();
            $res5 = "success";
        } catch (Exception $e) {
            DB::rollback();
            $res5 = $e->getMessage();
        }

        if ($res5 == "success") {
            $request->session()->flash('success', 'Role successfully updated.');
        } else {
            $request->session()->flash('error', $res5);
        }

        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $role = new Role;

        // Delete log
        $log = array(
            'role_id' => $id,
            'user_id' => Auth::user()->user_id,
            'browser' => $this->browser(),
            'activity' => "Deleted role",
            'device' => $this->device(),
            'ip_env_address' => $request->ip(),
            'ip_server_address' => request()->server('SERVER_ADDR'),
            'OS' => $this->operating_system()
        );

        $res = $role->deleteRole($id, $log);
        echo json_encode($res);
    }

    // Roles datatable
    public function datatable() {
        // Get data
        $role = new Role();
        $roles = $role->getRoles2();

        $data = collect($roles);

        return Datatables::of($data)
        ->addColumn('actions', function($data) {
            $buttons = '';
            if (Entrust::can('view_role')) {
                $buttons .= '<a href="'.route('roles.show', $data->role_id).'" class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i> View</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('edit_role')) {
                $buttons .= '<a href="'.route('roles.edit', $data->role_id).'" class="btn btn-warning btn-sm" title="Edit" style="color: white;"><i class="fa fa-edit"></i> Edit</a>&nbsp;&nbsp;';
            }
            if (Entrust::can('delete_role')) {
                $buttons .= '<button onclick="delete_role('.$data->role_id.')" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash-alt"></i> Delete</button>';
            }
            
            return $buttons;
        })
        ->rawColumns(['actions'])
        ->make(true);
    }
}
