<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRoleSystem extends Model
{
    protected $table = 'user_role_system';

    protected $primaryKey = 'user_role_system_id';
    
    protected $fillable = ['user_id', 'role_id', 'system_id'];

    public $timestamps = false;
}
