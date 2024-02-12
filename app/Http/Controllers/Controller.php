<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Browser;
use DB;
use Storage;
use App\Monitoring;
use App\Affiliation;
use App\Permission;
use App\PhilRiceStation;
use App\Role;
use App\System;
use App\Activity;
use App\User;
use Entrust;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Get all stations
    public function stations()
    {
        $stations = DB::table('philrice_station')->select('*')->orderBy('name', 'asc')->get();
        return $stations;
    }
    // get
    public function getUserStation($user_id){
        return DB::table('affiliation_user')->select('affiliated_to')->where('user_id',$user_id)->first();
    }

    // Browser name for logs
    public function browser() {
    	return Browser::browserName();
    }

    // Device for logs
    public function device() {
    	if (Browser::isMobile()) {
    		if (Browser::deviceModel() != "Unknown") {
    			return Browser::deviceModel();
    		} else {
    			return "Mobile";
    		}
    	} else if (Browser::isTablet()) {
    		if (Browser::deviceModel() != "Unknown") {
    			return Browser::deviceModel();
    		} else {
    			return "Tablet";
    		}
    	} else if (Browser::isDesktop()) {
    		return "Desktop";
    	}
    }

    // Countries
    public function countries() {
        // json file is in storage folder
        $json = Storage::disk('local')->get('countries.json');
        $countries = json_decode($json, true);
        asort($countries);
        return $countries;
    }

    // Provinces
    public function provinces() {
        $provinces = DB::connection('seed_grow')->table('provinces')->orderBy('name', 'asc')->get();
        return $provinces;
    }

    // Affiliations
    public function affiliations() {
        $affiliations = DB::table('affiliations')->orderBy('name', 'asc')->get();
        return $affiliations;
    }

    // OS name for logs
    public function operating_system() {
        return Browser::platformName();
    }

    public function getLogs($from,$to) {
        $monitoring = new Monitoring;
        $logs = array();
        /*if($request->system_id != 0)
            $system = $system->where('system_id',$request->system_id);*/

        //$system = $system->first();
        //dd($system);

            if(Entrust::can('view_affiliation_logs')){
                $affilitionLogs = $monitoring->getAffiliationLogs($from,$to);
                foreach($affilitionLogs as $al){
                    $system = System::select('display_name')->where('system_id',1)->first();
                    $logs[] = array(
                        'system_id' => 1,
                        'user_id' => $al->user_id,
                        'system' => $system->display_name,
                        'activity' => $al->activity,
                        'user' => $al->firstname.' '.$al->lastname,
                        'device' => $al->device,
                        'browser' => $al->browser,
                        'timestamp' =>  date('m/d/Y', strtotime($al->timestamp))
                    );
                }
            }

            if(Entrust::can('view_permission_logs')){
                $permissionLogs = $monitoring->getPermissionLogs($from,$to);
                foreach($permissionLogs as $pl){
                    $system = System::select('display_name')->where('system_id',1)->first();
                    $logs[] = array(
                        'system_id' => 1,
                        'user_id' => $pl->user_id,
                        'system' => $system->display_name,
                        'activity' => $pl->activity,
                        'user' => $pl->firstname.' '.$pl->lastname,
                        'device' => $pl->device,
                        'browser' => $pl->browser,
                        'timestamp' => date('m/d/Y', strtotime($pl->timestamp))
                    );
                }
            }

            if(Entrust::can('view_role_logs')){
                $roleLogs = $monitoring->getRoleLogs($from,$to);
                foreach($roleLogs as $rl){
                    $system = System::select('display_name')->where('system_id',1)->first();
                    $logs[] = array(
                        'system_id' => 1,
                        'user_id' => $rl->user_id,
                        'system' => $system->display_name,
                        'activity' => $rl->activity,
                        'user' => $rl->firstname.' '.$rl->lastname,
                        'device' => $rl->device,
                        'browser' => $rl->browser,
                        'timestamp' => date('m/d/Y', strtotime($rl->timestamp))
                    );
                }
            }

            if(Entrust::can('view_station_logs')){
                $stationLogs = $monitoring->getStationLogs($from,$to);
                foreach($stationLogs as $sl) {
                    $system = System::select('display_name')->where('system_id',1)->first();
                    $logs[] = array(
                        'system_id' => 1,
                        'user_id' => $sl->user_id,
                        'system' => $system->display_name,
                        'activity' => $sl->activity,
                        'user' => $sl->firstname.' '.$sl->lastname,
                        'device' => $sl->device,
                        'browser' => $sl->browser,
                        'timestamp' => date('m/d/Y', strtotime($sl->timestamp))
                    );
                }
            }

            if(Entrust::can('view_station_logs')){
                $systemLogs = $monitoring->getSystemLogs($from,$to);
                foreach($systemLogs as $sl) {
                    $system = System::select('display_name')->where('system_id',1)->first();
                    $logs[] = array(
                        'system_id' => 1,
                        'user_id' => $sl->user_id,
                        'system' => $system->display_name,
                        'activity' => $sl->activity,
                        'user' => $sl->firstname.' '.$sl->lastname,
                        'device' => $sl->device,
                        'browser' => $sl->browser,
                        'timestamp' => date('m/d/Y', strtotime($sl->timestamp))
                    );
                }
            }

            if(Entrust::can('view_activity_logs')){
                $activityLogs = $monitoring->getActivityLogs($from,$to);
                foreach($activityLogs as $al) {
                    $system = System::select('display_name')->where('system_id',1)->first();
                    $logs[] = array(
                        'system_id' => 1,
                        'user_id' => $al->user_id,
                        'system' => $system->display_name,
                        'activity' => $al->activity,
                        'user' => $al->firstname.' '.$al->lastname,
                        'device' => $al->device,
                        'browser' => $al->browser,
                        'timestamp' => date('m/d/Y', strtotime($al->timestamp))
                    );
                }
            }

            /*if(Entrust::can('view_activity_logs')){
                $userLogs = $monitoring->getUserLogs($from,$to);
                foreach($userLogs as $ul) {
                    $system = System::select('display_name')->where('system_id',1)->first();
                    $logs[] = array(
                        'system_id' => 1,
                        'user_id' => $ul->user_id,
                        'system' => $system->display_name,
                        'activity' => $ul->activity,
                        'user' => $ul->firstname.' '.$ul->lastname,
                        'device' => $ul->device,
                        'browser' => $ul->browser,
                        'timestamp' => date('m/d/Y', strtotime($ul->timestamp))
                    );
                }
            }*/
            if(Entrust::can('view_ordering_variable_logs')){
                $seedOrderingVariableLogs = $monitoring->getSeedOrderingVariableLogs($from,$to);
                foreach($seedOrderingVariableLogs as $ol){
                    $system = System::select('display_name')->where('system_id',6)->first();
                    $logs[] = array(
                        'system_id' => 6,
                        'user_id' => $ol->user_id,
                        'system' => $system->display_name,
                        'activity' => $ol->activity,
                        'user' => $ol->firstname.' '.$ol->lastname,
                        'device' => $ol->device,
                        'browser' => $ol->browser,
                        'timestamp' => date('m/d/Y', strtotime($ol->timestamp))
                    );
                }
            }
        
        return $logs;
    }

    
}
