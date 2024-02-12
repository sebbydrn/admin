<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Affiliation extends Model
{
    protected $primaryKey = 'affiliation_id';

	protected $fillable = ['name'];

	public $timestamps = false;

	public function affiliations() {
    	$affiliations = DB::table('affiliations')->get();
    	return $affiliations;
    }

    public function add_log($log) {
    	DB::table('affiliation_activities')->insert($log);
    }

    public function affiliation($affiliation_id) {
    	$data = DB::table('affiliations')
    	->select('*')
    	->where('affiliation_id', $affiliation_id)
    	->first();

    	return $data;
    }

    public function delete_affiliation($affiliation_id, $log) {
    	DB::beginTransaction();
        try {
            DB::table('affiliations')
            ->where('affiliation_id', $affiliation_id)
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

    public function get_date_created($affiliation_id) {
        $date_created = DB::table('affiliation_activities')
        ->select('timestamp')
        ->where('affiliation_id', $affiliation_id)
        ->where('activity', "Added new affiliation")
        ->first();

        return $date_created;
    }

    public function get_date_updated($affiliation_id) {
        $date_updated = DB::table('affiliation_activities')
        ->select('timestamp')
        ->where('affiliation_id', $affiliation_id)
        ->where('activity', "Updated affiliation")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }
}
