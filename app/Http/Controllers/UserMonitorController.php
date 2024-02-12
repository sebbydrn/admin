<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Activity;
use App\ActivityUser;
use DB;

class UserMonitorController extends Controller
{
    public function users_last_login() {
        // $users = DB::table('users as u')
        // ->leftJoin('activities_user as au', 'u.user_id', '=', 'au.user_id')
        // ->leftJoin('activities as a', 'au.activity_id', '=', 'a.activity_id')
        // ->selectRaw('DISTINCT ON (u.username) u.username, u.fullname, u.email, au.timestamp')
        // ->where('a.name', '=', 'User Logged In')
        // ->orderBy('au.timestamp', 'DESC')
        // ->get();

        $users = Users::select('user_id', 'fullname', 'username')
        ->where('isactive', '=', 1)
        ->get();

        $data = array();

        foreach ($users as $u) {
            

            $data[] = array(

            );
        }

        dd($data);

        return view('user_monitor.last_login');
    }
}
