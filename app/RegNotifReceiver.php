<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegNotifReceiver extends Model
{
    protected $connection = "cms";
    protected $table = "reg_notif_receivers";
    protected $primaryKey = "receiver_id";
    protected $fillable = ['user_id', 'receive_type'];
    public $timestamps = false;
}
