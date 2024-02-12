<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DownloadableCategoryActivities extends Model {
    
    protected $connection = "cms";
	protected $primaryKey = "downloadable_category_activity_id";
	protected $table = "downloadable_category_activities";
	protected $fillable = [
		'downloadable_category_id', 
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

	// Get the downloadable category that owns the activity
	public function downloadable_category() {
		return $this->belongsTo('App\DownloadableCategory', 'downloadable_category_id', 'downloadable_category_id');
	}

}
