<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class PendingRegistration extends Model
{
    // Get pending registrations
    function pending_registrations() {
        $data = DB::table('users')->select('*')->where('isapproved', 0)->get();
        return $data;
    }

    // Get pending registration for approval
    function pending_registration($id) {
    	$data = DB::table('users')->select('*')->where('user_id', $id)->first();
        return $data;
    }

    // Add roles for new user
    function add_roles($id, $roles) {
        DB::beginTransaction();
        try{
            foreach ($roles as $key => $value) {
                DB::table('role_user')
                ->insert([
                    'user_id' => $id,
                    'role_id' => $value
                ]);
            }
            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    // Add password for new user
    function add_password($id, $password) {
        DB::beginTransaction();
        try {
            // Update password
            DB::table('users')
            ->where('id', $id)
            ->update([
                'password' => $password,
                'isactive' => 1,
                'isapproved' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $transactionid = $id . '' . time();
            $transactionid = md5($transactionid);

            // Add transaction log
            DB::table('transactions')
            ->insert([
                'transactionid' => $transactionid,
                'action' => 'Approved user registration without confirmation email',
                'userid' => $id,
                'transaction_date' => date('Y-m-d H:i:s')
            ]);
            
            DB::commit();
            return "success";
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    // Approve registration with email
    function edit_active_status($id, $data) {
        DB::beginTransaction();
        try {
            DB::table('users')
            ->where('id', $id)
            ->update([
                'isapproved' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Add transaction log
            DB::table('transactions')->insert($data);

            DB::commit();
            return "success";
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    // Add password link to password_links table
    function add_password_link($id, $link) {
        DB::beginTransaction();
        try {
            DB::table('password_links')
            ->insert([
                'user_id' => $id,
                'link' => $link
            ]);
            DB::commit();
            return "success";
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    /*// Check activate/add password link if already used
    function password_link($link) {
        $status = DB::table('password_links')
        ->select('done')
        ->where('link', $link)
        ->first();

        return $status;
    }

    // Add password for new user which is through email
    function add_password2($id, $password) {
        DB::beginTransaction();
        try {
            // Update password
            DB::table('users')
            ->where('id', $id)
            ->update([
                'password' => $password,
                'isactive' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $transactionid = $id . '' . time();
            $transactionid = md5($transactionid);

            // Add transaction log
            DB::table('transactions')
            ->insert([
                'transactionid' => $transactionid,
                'action' => 'Add password',
                'userid' => $id,
                'transaction_date' => date('Y-m-d H:i:s')
            ]);

            // Set link to done = 1
            DB::table('password_links')
            ->where('userid', $id)
            ->update(['done' => 1]);
            
            DB::commit();
            return "success";
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    // Get userid
    function get_userid($id) {
        $data = DB::table('password_links')
        ->select('userid')
        ->where('link', $id)
        ->first();

        return $data;
    }*/

    // Add disapprove reasons
    function add_reasons($id, $reasons) {
        DB::beginTransaction();
        try {
            DB::table('users')
            ->where('id', $id)
            ->update([
                'isapproved' => 2,
                'disapprove_reasons' => $reasons,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $transactionid = $id . '' . time();
            $transactionid = md5($transactionid);

            // Add transaction log
            DB::table('transactions')
            ->insert([
                'transactionid' => $transactionid,
                'action' => 'Disapprove registration',
                'userid' => $id,
                'transaction_date' => date('Y-m-d H:i:s')
            ]);

            DB::commit();
            return "success";
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
