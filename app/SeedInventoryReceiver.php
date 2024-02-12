<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeedInventoryReceiver extends Model
{
    protected $table = "seed_inventory_email_receiver";
    protected $fillable = ['email', 'receive_type'];
    public $timestamps = false;
}
