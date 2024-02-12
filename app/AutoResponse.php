<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class AutoResponse extends Model {

	protected $connection = 'cms';

	protected $table = 'auto_response';

    protected $primaryKey = 'auto_response_id';

	protected $fillable = ['sender', 'title', 'body', 'is_enabled'];

	public $timestamps = false;

	public function auto_responses() {
    	$auto_responses = DB::connection('cms')->table('auto_response')->get();
    	return $auto_responses;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('auto_response_activities')->insert($log);
    }

    public function auto_response($auto_response_id) {
    	$data = DB::connection('cms')
    	->table('auto_response')
    	->select('*')
    	->where('auto_response_id', $auto_response_id)
    	->first();

    	return $data;
    }

    public function get_date_created($auto_response_id) {
        $date_created = DB::connection('cms')
        ->table('auto_response_activities')
        ->select('timestamp')
        ->where('auto_response_id', $auto_response_id)
        ->where('activity', "Added new auto response")
        ->first();

        return $date_created;
    }

    public function get_date_updated($auto_response_id) {
        $date_updated = DB::connection('cms')
        ->table('auto_response_activities')
        ->select('timestamp')
        ->where('auto_response_id', $auto_response_id)
        ->where('activity', "Updated auto response")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_auto_response($auto_response_id, $log) {
    	DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('auto_response')
            ->where('auto_response_id', $auto_response_id)
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
