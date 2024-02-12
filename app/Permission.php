<?php

namespace App;

use Zizaco\Entrust\EntrustPermission;
use DB;
use Auth;

class Permission extends EntrustPermission
{
    protected $primaryKey = 'permission_id';

    protected $fillable = ['name', 'display_name', 'description'];

    public $timestamps = false;

    /*function addPermission($input) {
        DB::beginTransaction();
        try {
            // Insert permission
            DB::table('permissions')
            ->insert([
                'name' => $input['name'],
                'display_name' => $input['display_name'],
                'description' => $input['description']
            ]);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }*/

    // Get all permissions
    function getPermissions() {
        $permissions = DB::table('permissions')->select('*')->orderBy('display_name', 'asc')->get();
        return $permissions;
    }

    // Get permission
    function getPermission($permission_id) {
        $permission = DB::table('permissions')
        ->select('*')
        ->where('permission_id', $permission_id)
        ->first();

        return $permission;
    }

    // Update permission
    function updatePermission($permission_id, $input) {
        DB::beginTransaction();
        try {
            DB::table('permissions')
            ->where('permission_id', $permission_id)
            ->update([
                'name' => $input['name'],
                'display_name' => $input['display_name'],
                'description' => $input['description'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    // Delete permission
    function deletePermission($permission_id, $log) {
        DB::beginTransaction();
        try {
            DB::table('permissions')->where('permission_id', $permission_id)->delete();
            DB::table('permission_role')->where('permission_id', $permission_id)->delete();

            // Add log
            $this->add_log($log);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    // Get permissions of role
    function getRolePermissions($id) {
        $permissions = DB::table('permission_role')
        ->leftJoin('permissions', 'permissions.permission_id', '=', 'permission_role.permission_id')
        ->select('permissions.*')
        ->where('permission_role.role_id', $id)
        ->orderBy('permissions.display_name', 'asc')
        ->get();

        return $permissions;
    }

    public function add_log($log) {
        DB::table('permission_activities')->insert($log);
    }

    public function get_date_created($permission_id) {
        $date_created = DB::table('permission_activities')
        ->select('timestamp')
        ->where('permission_id', $permission_id)
        ->where('activity', "Added new permission")
        ->first();

        return $date_created;
    }

    public function get_date_updated($permission_id) {
        $date_updated = DB::table('permission_activities')
        ->select('timestamp')
        ->where('permission_id', $permission_id)
        ->where('activity', "Updated permission")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

}
