<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Link extends Model {
   	
	protected $connection = 'cms';

	protected $table = 'links';

    protected $primaryKey = 'link_id';

	protected $fillable = ['name', 'link'];

	public $timestamps = false;

	public function links() {
    	$links = DB::connection('cms')->table('links')->get();
    	return $links;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('link_activities')->insert($log);
    }

    public function link($link_id) {
    	$data = DB::connection('cms')
    	->table('links')
    	->select('*')
    	->where('link_id', $link_id)
    	->first();

    	return $data;
    }

    public function get_date_created($link_id) {
        $date_created = DB::connection('cms')
        ->table('link_activities')
        ->select('timestamp')
        ->where('link_id', $link_id)
        ->where('activity', "Added new link")
        ->first();

        return $date_created;
    }

    public function get_date_updated($link_id) {
        $date_updated = DB::connection('cms')
        ->table('link_activities')
        ->select('timestamp')
        ->where('link_id', $link_id)
        ->where('activity', "Updated link")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_link($link_id, $log) {
    	DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('links')
            ->where('link_id', $link_id)
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
