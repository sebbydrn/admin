<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Content extends Model {

    protected $connection = 'cms';

	protected $table = 'contents';

    protected $primaryKey = 'content_id';

	protected $fillable = ['page_id', 'section_id', 'subtitle', 'content', 'image', 'is_published'];

	public $timestamps = false;

	public function contents() {
    	$contents = DB::connection('cms')
    	->table('contents')
    	->leftJoin('pages', 'pages.page_id', '=', 'contents.page_id')
    	->leftJoin('sections', 'sections.section_id', '=', 'contents.page_id')
    	->select('contents.*', 'pages.display_name as page_name', 'sections.display_name as section_name')
    	->get();

    	return $contents;
    }

    public function sections($page_id) {
    	$sections = DB::connection('cms')
    	->table('sections')
    	->select('section_id', 'display_name')
    	->where('page_id', $page_id)
    	->orderBy('display_name', 'asc')
    	->get();

    	return $sections;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('content_activities')->insert($log);
    }

    public function content($content_id) {
        $data = DB::connection('cms')
        ->table('contents')
        ->leftJoin('pages', 'pages.page_id', '=', 'contents.page_id')
        ->leftJoin('sections', 'sections.section_id', '=', 'contents.page_id')
        ->select('contents.*', 'pages.display_name as page_name', 'sections.display_name as section_name')
        ->where('contents.content_id', $content_id)
        ->first();

        return $data;
    }

    public function get_date_created($content_id) {
        $date_created = DB::connection('cms')
        ->table('content_activities')
        ->select('timestamp')
        ->where('content_id', $content_id)
        ->where('activity', "Added new content")
        ->first();

        return $date_created;
    }

    public function get_date_updated($content_id) {
        $date_updated = DB::connection('cms')
        ->table('content_activities')
        ->select('timestamp')
        ->where('content_id', $content_id)
        ->where('activity', "Updated content")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_content($content_id, $log) {
        DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('contents')
            ->where('content_id', $content_id)
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
