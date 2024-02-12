<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Contact extends Model {
    
	protected $connection = 'cms';

	protected $table = 'contacts';

    protected $primaryKey = 'contact_id';

	protected $fillable = ['name', 'contact_detail'];

	public $timestamps = false;

	public function contacts() {
    	$contacts = DB::connection('cms')->table('contacts')->get();
    	return $contacts;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('contact_activities')->insert($log);
    }

    public function contact($contact_id) {
    	$data = DB::connection('cms')
    	->table('contacts')
    	->select('*')
    	->where('contact_id', $contact_id)
    	->first();

    	return $data;
    }

    public function get_date_created($contact_id) {
        $date_created = DB::connection('cms')
        ->table('contact_activities')
        ->select('timestamp')
        ->where('contact_id', $contact_id)
        ->where('activity', "Added new contact")
        ->first();

        return $date_created;
    }

    public function get_date_updated($contact_id) {
        $date_updated = DB::connection('cms')
        ->table('contact_activities')
        ->select('timestamp')
        ->where('contact_id', $contact_id)
        ->where('activity', "Updated contact")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_contact($contact_id, $log) {
    	DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('contacts')
            ->where('contact_id', $contact_id)
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
