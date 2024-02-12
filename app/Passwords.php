<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passwords extends Model
{
    protected $primaryKey = 'password_id';
    
    protected $fillable = ['password', 'user_id', 'system_id'];

    public $timestamps = false;
}
