<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use App\User;
use Hash;

class LockController extends Controller
{	
    public function unlock(Request $request) {
    	$user = User::find(Auth::user()->user_id);
    	if (Hash::check($request->password, $user->password)) {
    		echo json_encode(1);
    	} else {
    		echo json_encode(0);
    	}
    }

    public function check_logged_in() {
    	if (Auth::user()) {
    		echo json_encode(1);
    	} else {
    		echo json_encode(0);
    	}
    }
}
