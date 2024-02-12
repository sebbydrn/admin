<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Activity extends Model
{
    protected $primaryKey = 'activity_id';

	protected $fillable = ['name'];

	public $timestamps = false;

	public function activities() {
    	$activities = DB::table('activities')->get();
    	return $activities;
    }

    public function add_log($log) {
    	DB::table('activities_activities')->insert($log);
    }

    public function activity($activity_id) {
    	$data = DB::table('activities')
    	->select('*')
    	->where('activity_id', $activity_id)
    	->first();

    	return $data;
    }

    public function get_date_created($activity_id) {
        $date_created = DB::table('activities_activities')
        ->select('timestamp')
        ->where('activity_id', $activity_id)
        ->where('activity', "Added new activity")
        ->first();

        return $date_created;
    }

    public function get_date_updated($activity_id) {
        $date_updated = DB::table('activities_activities')
        ->select('timestamp')
        ->where('activity_id', $activity_id)
        ->where('activity', "Updated activity")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_activity($activity_id, $log) {
    	DB::beginTransaction();
        try {
            DB::table('activities')
            ->where('activity_id', $activity_id)
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

    
}
