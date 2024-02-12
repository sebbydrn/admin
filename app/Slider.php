<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Slider extends Model {
    
	protected $connection = 'cms';

	protected $table = 'sliders';

    protected $primaryKey = 'slider_id';

	protected $fillable = ['name', 'image', 'link'];

	public $timestamps = false;

	public function sliders() {
    	$sliders = DB::connection('cms')->table('sliders')->get();
    	return $sliders;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('slider_activities')->insert($log);
    }

    public function slider($slider_id) {
        $data = DB::connection('cms')
        ->table('sliders')
        ->where('sliders.slider_id', $slider_id)
        ->first();

        return $data;
    }

    public function get_date_created($slider_id) {
        $date_created = DB::connection('cms')
        ->table('slider_activities')
        ->select('timestamp')
        ->where('slider_id', $slider_id)
        ->where('activity', "Added new slider")
        ->first();

        return $date_created;
    }

    public function get_date_updated($slider_id) {
        $date_updated = DB::connection('cms')
        ->table('slider_activities')
        ->select('timestamp')
        ->where('slider_id', $slider_id)
        ->where('activity', "Updated slider")
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function delete_slider($slider_id, $log) {
        DB::beginTransaction();
        try {
            DB::connection('cms')
            ->table('sliders')
            ->where('slider_id', $slider_id)
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
