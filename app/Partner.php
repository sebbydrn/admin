<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Partner extends Model {
    
	protected $connection = 'cms';

	protected $table = 'partners';

    protected $primaryKey = 'partner_id';

	protected $fillable = ['name', 'description', 'logo', 'website', 'short_name'];

	public $timestamps = false;

	public function partners() {
    	$partners = DB::connection('cms')->table('partners')->get();
    	return $partners;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('partner_activities')->insert($log);
    }

    public function partner($partner_id) {
        $data = DB::connection('cms')
        ->table('partners')
        ->where('partners.partner_id', $partner_id)
        ->first();

        return $data;
    }

    public function get_date_created($partner_id) {
        $date_created = DB::connection('cms')
        ->table('partner_activities')
        ->select('timestamp')
        ->where('partner_id', $partner_id)
        ->where('activity', "Added new partner")
        ->first();

        return $date_created;
    }

    public function get_date_updated($partner_id) {
        $date_updated = DB::connection('cms')
        ->table('partner_activities')
        ->select('timestamp')
        ->where('partner_id', $partner_id)
        ->where('activity', "Updated partner")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_partner($partner_id, $log) {
        DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('partners')
            ->where('partner_id', $partner_id)
            ->delete();

            // Add log
            $this->add_log($log);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

}
