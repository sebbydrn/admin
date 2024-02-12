<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliationUser extends Model
{
    protected $table = 'affiliation_user';

    protected $primaryKey = 'affiliation_user_id';
    
    protected $fillable = ['affiliation_id', 'user_id', 'affiliated_to'];

    public $timestamps = false;
}
