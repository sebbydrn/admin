<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Receiver extends Model {
    
	protected $connection = 'cms';

	protected $table = 'receivers';

    protected $primaryKey = 'receiver_id';

	protected $fillable = ['user_id', 'receive_type'];

	public $timestamps = false;

	public function receivers() {
    	$receivers = DB::connection('cms')->table('receivers')->get();

    	$data = array();

    	foreach ($receivers as $receiver) {
    		// Get user from users table
    		$user = DB::table('users')
    		->select('fullname')
    		->where('user_id', $receiver->user_id)
    		->first();

    		$data[] = array(
    			'receiver_id' => $receiver->receiver_id,
    			'name' => $user->fullname,
    			'receive_type' => $receiver->receive_type 
    		);
    	}

    	return $data;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('inquiry_receiver_activities')->insert($log);
    }

    public function receiver($receiver_id) {
    	$receiver = DB::connection('cms')
    	->table('receivers')
    	->select('*')
    	->where('receiver_id', $receiver_id)
    	->first();

    	$user = DB::table('users')
    	->select('fullname')
    	->where('user_id', $receiver->user_id)
    	->first();

    	$data = array(
    		'receiver_id' => $receiver->receiver_id,
    		'user_id' => $receiver->user_id,
    		'name' => $user->fullname,
    		'receive_type' => $receiver->receive_type
    	);

    	return $data;
    }

    public function get_date_created($receiver_id) {
        $date_created = DB::connection('cms')
        ->table('inquiry_receiver_activities')
        ->select('timestamp')
        ->where('receiver_id', $receiver_id)
        ->where('activity', "Added new receiver")
        ->first();

        return $date_created;
    }

    public function get_date_updated($receiver_id) {
        $date_updated = DB::connection('cms')
        ->table('inquiry_receiver_activities')
        ->select('timestamp')
        ->where('receiver_id', $receiver_id)
        ->where('activity', "Updated receiver")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_receiver($receiver_id, $log) {
    	DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('receivers')
            ->where('receiver_id', $receiver_id)
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
