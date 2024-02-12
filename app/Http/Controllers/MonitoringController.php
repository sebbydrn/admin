<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monitoring;
use App\Affiliation;
use App\Permission;
use App\PhilRiceStation;
use App\Role;
use App\System;
use App\Activity;
use App\User;
use Auth;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
class MonitoringController extends Controller
{

    public function __construct() {
        $this->middleware('permission:view_monitoring')->only(['index', 'datatable']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logs = $this->getLogs(0,0);
        $unique_activity = array();
        foreach($logs as $l){
            $unique_activity[] = $l['activity'];
        }

        $activities = array_unique($unique_activity);

    	$systems = System::orderBy('system_id','asc')->get();
        $users = User::orderBy('user_id','asc')->get();
        return view('monitoring.index',compact('systems','users','activities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function datatable(Request $request) {
    	$logs = $this->getLogs($request->date_from,$request->date_to);
      $from = $request->date_from;
      $to = $request->date_to;
      //remove all the duplicate data;
      $unique_activity = array();
      $unique_user = array();
      $data = array();
      $test = "";
      foreach($logs as $l)
      {
          /*$unique_activity[] = $l['activity'];
          $unique_user[] = $l['user'];*/

          if($request->system == 0 && $request->user == 0 && $request->activity == "none") {
              $test = "if 1";
              $data[] = array(
              'system' => $l['system'],
              'activity' => $l['activity'],
              'user' => $l['user'],
              'device' => $l['device'],
              'browser' => $l['browser'],
              'timestamp' => date('m/d/Y', strtotime($l['timestamp'])),
              );
          }

          if(($l['system_id'] == $request->system && $request->user == 0 && $request->activity == "none") || ($l['user_id'] == $request->user &&  $request->system == 0 && $request->activity == 'none') || ($l['activity'] == $request->activity && $request->user == 0 && $request->system == 0 )) {
              $test = "if 2";
              $data[] = array(
              'system' => $l['system'],
              'activity' => $l['activity'],
              'user' => $l['user'],
              'device' => $l['device'],
              'browser' => $l['browser'],
              'timestamp' => date('m/d/Y', strtotime($l['timestamp']))
              );
          }

          if(($l['system_id'] == $request->system && $request->user == $l['user_id'] && $request->activity == "none") || ($l['user_id'] == $request->user &&  $request->system == $l['system_id'] && $request->activity == 'none') || ($l['activity'] == $request->activity && $request->user == $l['user_id'] && $request->system == 0 )){
              $test = "if 2.1";
              $data[] = array(
              'system' => $l['system'],
              'activity' => $l['activity'],
              'user' => $l['user'],
              'device' => $l['device'],
              'browser' => $l['browser'],
              'timestamp' => date('m/d/Y', strtotime($l['timestamp']))
              );
          }

          if(($l['system_id'] == $request->system && $request->user == 0 && $request->activity == $l['activity']) || ($l['user_id'] == $request->user &&  $request->system == 0 && $request->activity == $l['activity']) || ($l['activity'] == $request->activity && $request->user == 0 && $request->system == $l['system_id'] )) {
              $test = "if 2.2";
              $data[] = array(
              'system' => $l['system'],
              'activity' => $l['activity'],
              'user' => $l['user'],
              'device' => $l['device'],
              'browser' => $l['browser'],
              'timestamp' => date('m/d/Y', strtotime($l['timestamp']))
              );
          }

          if($l['user_id'] == $request->user && $l['system_id'] == $request->system && $l['activity'] == $request->activity){
              $test = "if 3";
              $data[] = array(
              'system' => $l['system'],
              'activity' => $l['activity'],
              'user' => $l['user'],
              'device' => $l['device'],
              'browser' => $l['browser'],
              'timestamp' => date('m/d/Y', strtotime($l['timestamp']))
              );
          }


          /*if($request->activity != 0 && $l['activity'] == $request->activity) {
              $data[] = array(
              'system' => $l['system'],
              'activity' => $l['activity'],
              'user' => $l['user'],
              'device' => $l['device'],
              'browser' => $l['browser'],
              'timestamp' => date('m/d/Y', strtotime($l['timestamp']))
              );
          }*/
      }
      
      
      /*$unique_activity = array_unique($unique_activity);
      $unique_user = array_unique($unique_user);

      $user = array();
      foreach($unique_user as $uu) {
          $user[] = $uu;
      }

      $activity = array();
      foreach($unique_activity as $ua) {
          $activity[] = $ua;
      }*/
      $datatable = Datatables::of($data);

      $datatable->with([
          'test' => $test
      ]);
      /*if($request->draw == 1) {
          $systems = System::orderBy('system_id','asc')->pluck('display_name');
          $datatable->with([
            'allActivity' => $activity,
            'allSystem' => $systems,
            'allUser' => $user
          ]);
        }*/
      return $datatable->make(true);
  }
}
