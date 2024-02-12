<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Response extends Model {
    
	protected $connection = 'cms';

	protected $table = 'responses';

    protected $primaryKey = 'response_id';

	protected $fillable = ['title', 'body', 'email_registered'];

	public $timestamps = false;

	public function add_log($log) {
    	DB::connection('cms')->table('response_activities')->insert($log);
    }

}
