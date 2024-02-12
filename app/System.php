<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class System extends Model
{	
	protected $primaryKey = 'system_id';

	protected $fillable = ['name', 'display_name', 'description'];

	public $timestamps = false;

    public function systems() {
    	$systems = DB::table('systems')->get();
    	return $systems;
    }

    public function add_log($log) {
    	DB::table('system_activities')->insert($log);
    }

    public function system($system_id) {
    	$data = DB::table('systems')
    	->select('*')
    	->where('system_id', $system_id)
    	->first();

    	return $data;
    }

    public function delete_system($system_id, $log) {
    	DB::beginTransaction();
        try {
            DB::table('systems')
            ->where('system_id', $system_id)
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

    public function get_date_created($system_id) {
        $date_created = DB::table('system_activities')
        ->select('timestamp')
        ->where('system_id', $system_id)
        ->where('activity', "Added new system")
        ->first();

        return $date_created;
    }

    public function get_date_updated($system_id) {
        $date_updated = DB::table('system_activities')
        ->select('timestamp')
        ->where('system_id', $system_id)
        ->where('activity', "Updated system")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

}
