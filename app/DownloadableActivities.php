<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DownloadableActivities extends Model {
    
	protected $connection = "cms";
	protected $primaryKey = "downloadable_activity_id";
	protected $table = "downloadable_activities";
	protected $fillable = [
		'downloadable_id', 
		'user_id', 
		'activity', 
		'browser', 
		'device', 
		'ip_env_address', 
		'ip_server_address',
		'new_value',
		'old_value',
		'OS'
	];
	
	public $timestamps = false;

	// Get the downloadable that owns the activity
	public function downloadable() {
		return $this->belongsTo('App\Downloadable', 'downloadable_id', 'downloadable_id');
	}

}
