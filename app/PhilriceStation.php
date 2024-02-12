<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class PhilriceStation extends Model
{
    protected $table = 'philrice_station';

    protected $primaryKey = 'philrice_station_id';

	protected $fillable = ['name', 'station_code'];

	public $timestamps = false;

	public function stations() {
		$stations = DB::table('philrice_station')->get();
    	return $stations;
	}

	public function add_log($log) {
    	DB::table('station_activities')->insert($log);
    }

    public function station($philrice_station_id) {
    	$data = DB::table('philrice_station')
    	->select('*')
    	->where('philrice_station_id', $philrice_station_id)
    	->first();

    	return $data;
    }

    public function delete_station($philrice_station_id, $log) {
    	DB::beginTransaction();
        try {
            DB::table('philrice_station')
            ->where('philrice_station_id', $philrice_station_id)
            ->delete();

            // Add log
            $this->add_log($log);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function get_date_created($philrice_station_id) {
        $date_created = DB::table('station_activities')
        ->select('timestamp')
        ->where('philrice_station_id', $philrice_station_id)
        ->where('activity', "Added new PhilRice station")
        ->first();

        return $date_created;
    }

    public function get_date_updated($philrice_station_id) {
        $date_updated = DB::table('station_activities')
        ->select('timestamp')
        ->where('philrice_station_id', $philrice_station_id)
        ->where('activity', "Updated PhilRice station")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function getStationLogs($from,$to){
        $to = date('m/d/Y', strtotime($to . ' +1 day'));
        $logs = DB::table('station_activities as sa')
        ->leftJoin('users','sa.user_id','users.user_id')
        ->select('sa.*','users.firstname','users.lastname','users.middlename','users.user_id');
        if($from != 0 && $to != 0 ){
            $logs->whereBetween('timestamp',[$from,$to]);
        }
        return $logs->get();
    }
}
