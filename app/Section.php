<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Section extends Model {
    
	protected $connection = 'cms';

	protected $table = 'sections';

    protected $primaryKey = 'section_id';

	protected $fillable = ['page_id', 'display_name', 'url', 'is_public', 'is_dynamic', 'is_published'];

	public $timestamps = false;

	public function sections() {
    	$sections = DB::connection('cms')
    	->table('sections')
    	->leftJoin('pages', 'pages.page_id', '=', 'sections.page_id')
    	->select('sections.*', 'pages.display_name as page_name')
    	->get();
    	return $sections;
    }
    
    public function add_log($log) {
    	DB::connection('cms')->table('section_activities')->insert($log);
    }

    public function section($section_id) {
    	$data = DB::connection('cms')
    	->table('sections')
    	->leftJoin('pages', 'pages.page_id', '=', 'sections.page_id')
    	->select('sections.*', 'pages.display_name as page_name')
    	->where('sections.section_id', $section_id)
    	->first();

    	return $data;
    }

    public function get_date_created($section_id) {
        $date_created = DB::connection('cms')
        ->table('section_activities')
        ->select('timestamp')
        ->where('section_id', $section_id)
        ->where('activity', "Added new section")
        ->first();

        return $date_created;
    }

    public function get_date_updated($section_id) {
        $date_updated = DB::connection('cms')
        ->table('section_activities')
        ->select('timestamp')
        ->where('section_id', $section_id)
        ->where('activity', "Updated section")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_section($section_id, $log) {
    	DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('sections')
            ->where('section_id', $section_id)
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
