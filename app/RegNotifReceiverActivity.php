<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegNotifReceiverActivity extends Model
{
    protected $connection = "cms";
    protected $table = "reg_notif_receiver_activities";
    protected $primaryKey = "reg_notif_receiver_activity_id";
    protected $fillable = ['receiver_id', 'user_id', 'activity', 'browser', 'device', 'ip_env_address', 'ip_server_address', 'timestamp', 'new_value', 'old_value', 'OS'];
    public $timestamps = false;
}
