<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class Page extends Model {

	protected $connection = 'cms';

	protected $table = 'pages';

    protected $primaryKey = 'page_id';

	protected $fillable = ['display_name', 'url', 'is_public', 'is_published'];

	public $timestamps = false;

	public function pages() {
    	$pages = DB::connection('cms')->table('pages')->get();
    	return $pages;
    }
    
    public function add_log($log) {
    	DB::connection('cms')->table('page_activities')->insert($log);
    }

    public function page($page_id) {
    	$data = DB::connection('cms')
    	->table('pages')
    	->select('*')
    	->where('page_id', $page_id)
    	->first();

    	return $data;
    }

    public function get_date_created($page_id) {
        $date_created = DB::connection('cms')
        ->table('page_activities')
        ->select('timestamp')
        ->where('page_id', $page_id)
        ->where('activity', "Added new page")
        ->first();

        return $date_created;
    }

    public function get_date_updated($page_id) {
        $date_updated = DB::connection('cms')
        ->table('page_activities')
        ->select('timestamp')
        ->where('page_id', $page_id)
        ->where('activity', "Updated page")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_page($page_id, $log) {
    	DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('pages')
            ->where('page_id', $page_id)
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
