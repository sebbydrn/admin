<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataComplianceReceiver extends Model
{
    protected $table = "data_compliance_email_receiver";
    protected $fillable = ['id', 'email', 'timestamp', 'receive_type'];
    public $timestamps = false;
}
