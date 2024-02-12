<?php

namespace App;

use Zizaco\Entrust\EntrustRole;
use DB;
use Auth;

class Role extends EntrustRole
{
    protected $primaryKey = 'role_id';

    protected $fillable = ['name', 'display_name', 'description'];

    public $timestamps = false;

    // Get user's roles
    function getRoles($userid) {
        $roles = DB::table('user_role_system')
        ->leftJoin('roles', 'roles.role_id', '=', 'user_role_system.role_id')
        ->leftJoin('systems', 'systems.system_id', '=', 'user_role_system.system_id')
        ->select('roles.*', 'systems.name as system_name', 'systems.display_name as system_display_name', 'user_role_system.user_role_system_id')
        ->where('user_role_system.user_id', $userid)
        ->get();

        return $roles;
    }

    // Get roles for creating users
    function getRoles2() {
        $roles = DB::table('roles')->select('*')->get();
        return $roles;
    }

    /*function addRole($input) {
        \DB::beginTransaction();
        try {
            // Insert role
            $roleid = \DB::table('roles')
            ->insertGetId([
                'name' => $input['name'],
                'display_name' => $input['display_name'],
                'description' => $input['description']
            ]);

            // Insert role's permissions
            foreach ($input['permissions'] as $key => $value) {
                \DB::table('permission_role')
                ->insert([
                    'permission_id' => $value,
                    'role_id' => $roleid
                ]);
            }

            \DB::commit();
            return "success";
        } catch (\Exception $e) {
            \DB::rollback();
            return $e->getMessage();
        }
    }*/

    // Add permission to role
    public function add_role_permission($role_permission) {
        $res = DB::table('permission_role')->insert($role_permission);
        return $res;
    }

    public function add_log($log) {
        DB::table('role_activities')->insert($log);
    }

    // Delete old role permissions
    public function delete_role_permissions($role_id) {
        $res = DB::table('permission_role')->where('role_id', $role_id)->delete();
        return $res;
    }

    // Get role
    function getRole($role_id) {
        $role = DB::table('roles')->select('*')->where('role_id', $role_id)->first();
        return $role;
    }

    // Update role
    /*function updateRole($id, $input) {
        \DB::beginTransaction();
        try {
            // Update role
            \DB::table('roles')
            ->where('role_id', $id)
            ->update([
                'name' => $input['name'],
                'display_name' => $input['display_name'],
                'description' => $input['description'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Delete role permissions
            \DB::table('permission_role')->where('role_id', $id)->delete();

            // Insert role's permissions
            foreach ($input['permissions'] as $key => $value) {
                \DB::table('permission_role')
                ->insert([
                    'permission_id' => $value,
                    'role_id' => $id
                ]);
            }

            \DB::commit();
            return "success";
        } catch (\Exception $e) {
            \DB::rollback();
            return $e->getMessage();
        }
    }*/

    // Delete role
    function deleteRole($role_id, $log) {
        DB::beginTransaction();
        try {
            // Delete from roles table
            DB::table('roles')->where('role_id', $role_id)->delete();

            // Delete from permission_role table
            DB::table('permission_role')->where('role_id', $role_id)->delete();

            // Delete from user_role_system table
            DB::table('user_role_system')->where('role_id', $role_id)->delete();

            // Add log
            $this->add_log($log);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function get_date_created($role_id) {
        $date_created = DB::table('role_activities')
        ->select('timestamp')
        ->where('role_id', $role_id)
        ->where('activity', "Added new role")
        ->first();

        return $date_created;
    }

    public function get_date_updated($role_id) {
        $date_updated = DB::table('role_activities')
        ->select('timestamp')
        ->where('role_id', $role_id)
        ->where('activity', "Updated role")
        ->orWhere('activity', "Updated role permission")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    
}
