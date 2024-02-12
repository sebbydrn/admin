<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliationAccess extends Model {
    
	protected $connection = "cms";
	protected $primaryKey = "affiliation_access_id";
	protected $table = "affiliation_access";
	protected $fillable = ['downloadable_id', 'affiliation_id'];

	public $timestamps = false;

}
