<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Entrust;
use App\User;
use Yajra\Datatables\Datatables;

class UserDataMonitoringController extends Controller {

	public function __construct() {
		$this->middleware('permission:view_registration_data_monitoring')->only(['index']);
	}
    
	public function index() {
		$registrationDataCount = DB::table('activities_user')
									->where('activity_id', '=', 11)
									->get()
									->count();

		$dateToday = date('Y-m-d');

		$registrationDataCountDaily = DB::table('activities_user')
									->where('activity_id', '=', 11)
									->whereDate('timestamp', '=', $dateToday)
									->get()
									->count();

		$userRegistrationActivities = DB::table('activities_user')
										->select('user_id', 'timestamp')
										->where('activity_id', '=', 11)
										->whereDate('timestamp', '=', $dateToday)
										->orderBy('timestamp', 'desc')
										->limit(10)
										->get();

		$registrationData = array();

		$user_model = new User();

		foreach ($userRegistrationActivities as $item) {
			$users = DB::table('users')
						->select('fullname', 'username', 'email')
						->where('user_id', '=', $item->user_id)
						->first();

			// User affiliation
            $user_affiliation = $user_model->get_user_affiliation($item->user_id);

            $registrationData[] = array(
            	'timestamp' => $item->timestamp,
            	'fullname' => $users->fullname,
            	'username' => $users->username,
            	'email' => $users->email,
            	'affiliation' => $user_affiliation->affiliation_name
            );
		}

		return view('user_data_monitoring.index', compact(['registrationDataCount', 'registrationDataCountDaily', 'registrationData']));
	}

	public function show_registration_data() {
		$registrationDataCount = DB::table('activities_user')
									->where('activity_id', '=', 11)
									->get()
									->count();

		$dateToday = date('Y-m-d');

		$registrationDataCountDaily = DB::table('activities_user')
									->where('activity_id', '=', 11)
									->whereDate('timestamp', '=', $dateToday)
									->get()
									->count();

		$userRegistrationActivities = DB::table('activities_user')
										->select('user_id', 'timestamp')
										->where('activity_id', '=', 11)
										->whereDate('timestamp', '=', $dateToday)
										->orderBy('timestamp', 'desc')
										->limit(10)
										->get();

		$registrationData = array();

		$user_model = new User();

		foreach ($userRegistrationActivities as $item) {
			$users = DB::table('users')
						->select('fullname', 'username', 'email')
						->where('user_id', '=', $item->user_id)
						->first();

			// User affiliation
            $user_affiliation = $user_model->get_user_affiliation($item->user_id);

            $registrationData[] = array(
            	'timestamp' => $item->timestamp,
            	'fullname' => $users->fullname,
            	'username' => $users->username,
            	'email' => $users->email,
            	'affiliation' => $user_affiliation->affiliation_name
            );
		}

		$data = array(
			'registrationDataCount' => $registrationDataCount,
			'registrationDataCountDaily' => $registrationDataCountDaily,
			'registrationData' => $registrationData
		);

		echo json_encode($data);
	}

	public function all_data_datatable(Request $request) {
		$userRegistrationActivities = DB::table('activities_user')
										->select('user_id', 'timestamp')
										->where('activity_id', '=', 11);
										if (isset($request->dateStart) && isset($request->dateEnd)) {
											$userRegistrationActivities = $userRegistrationActivities->whereDate('timestamp', '>=', $request->dateStart)
																									->whereDate('timestamp', '<=', $request->dateEnd);
										}
										$userRegistrationActivities = $userRegistrationActivities->get();

		$registrationData = array();

		$user_model = new User();

		foreach ($userRegistrationActivities as $item) {
			$users = DB::table('users')
						->select('fullname', 'username', 'email')
						->where('user_id', '=', $item->user_id)
						->first();

			// User affiliation
            $user_affiliation = $user_model->get_user_affiliation($item->user_id);

            if ($users) {
	            $registrationData[] = array(
	            	'timestamp' => date('Y-m-d H:i:s', strtotime($item->timestamp)),
	            	'fullname' => $users->fullname,
	            	'username' => $users->username,
	            	'email' => $users->email,
	            	'affiliation' => $user_affiliation->affiliation_name
	            );
            }
		}

		$data = collect($registrationData);

		return Datatables::of($data)->make(true);
	}

}
