<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Entrust;
class Monitoring extends Model
{
    public function getAffiliationLogs($from,$to){
        $to = date('m/d/Y', strtotime($to . ' +1 day'));
        $logs = DB::table('affiliation_activities as aa')
        ->leftJoin('users','aa.user_id','users.user_id')
        ->select('aa.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }

    public function getActivityLogs($from,$to){
        $logs = DB::table('activities_activities as aa')
        ->leftJoin('users','aa.user_id','users.user_id')
        ->select('aa.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }

    public function getPermissionLogs($from,$to){
        $to = date('m/d/Y', strtotime($to . ' +1 day'));
        $logs = DB::table('permission_activities as pa')
        ->leftJoin('users','pa.user_id','users.user_id')
        ->select('pa.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }

    public function getRoleLogs($from,$to){
        $to = date('m/d/Y', strtotime($to . ' +1 day'));
        $logs = DB::table('role_activities as ra')
        ->leftJoin('users','ra.user_id','users.user_id')
        ->select('ra.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }

    public function getSystemLogs($from,$to){
        $to = date('m/d/Y', strtotime($to . ' +1 day'));
        $logs = DB::table('system_activities as sys')
        ->leftJoin('users','sys.user_id','users.user_id')
        ->select('sys.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }

    public function getStationLogs($from,$to){
        $to = date('m/d/Y', strtotime($to . ' +1 day'));
        $logs = DB::table('station_activities as sa')
        ->leftJoin('users','sa.user_id','users.user_id')
        ->select('sa.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }

    public function getUserLogs($from, $to){
        $to = date('m/d/Y', strtotime($to . ' +1 day'));
        $logs = DB::table('activities_user as act_user')
        ->leftJoin('activities as act','act_user.activity_id','act.activity_id')
        ->leftJoin('users','act_user.user_id','users.user_id')
        ->select('act_user.*','users.firstname','users.lastname','users.middlename','act.name as activity', 'users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }

    public function getSeedOrderingVariableLogs($from, $to) {
    	$to = date('m/d/Y', strtotime($to . ' +1 day'));
    	 $logs = DB::connection('seed_ordering')->table('tbl_variable_activities as tva')
        ->leftJoin('rsis.users as users','tva.user_id','users.user_id')
        ->select('tva.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        if(Entrust::hasRole('admin')){
            $logs->where('users.user_id',Auth::id());
        }
        return $logs->get();
    }
}
