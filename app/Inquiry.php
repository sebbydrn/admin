<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Inquiry extends Model {
    
	protected $connection = 'cms';

	protected $table = 'inquiries';

    protected $primaryKey = 'inquiry_id';

	protected $fillable = ['sender', 'email', 'inquiry', 'status'];

	public $timestamps = false;

	public function inquiries() {
		$inquiries = DB::connection('cms')->table('inquiries')->get();

		$data = array();

		foreach ($inquiries as $inquiry) {
			$response = DB::connection('cms')
			->table('response_inquiry')
			->where('inquiry_id', $inquiry->inquiry_id)
			->first();

			$data[] = array(
				'inquiry_id' => $inquiry->inquiry_id,
				'sender' => $inquiry->sender,
				'email' => $inquiry->email,
				'inquiry' => str_limit($inquiry->inquiry, 500, '...'),
				'status' => ($response) ? 1 : 0,
				'response_id' => ($response) ? $response->response_id : ''
			);
		}

		return $data;
	}

	public function inquiry($inquiry_id) {
		$inquiry = DB::connection('cms')
		->table('inquiries')
		->where('inquiry_id', $inquiry_id)
		->first();
		
		$response_activity = DB::connection('cms')
		->table('response_inquiry')
		->where('inquiry_id', $inquiry->inquiry_id)
		->first();

		$response = '';
		
		if ($response_activity) {
			$response = DB::connection('cms')
			->table('responses')
			->where('response_id', $response_activity->response_id)
			->first();
		}
		

		$data = array(
			'inquiry_id' => $inquiry->inquiry_id,
			'sender' => $inquiry->sender,
			'email' => $inquiry->email,
			'inquiry' => $inquiry->inquiry,
			'response_title' => ($response) ? $response->title : '',
			'response_body' => ($response) ? $response->body : '',
			'response_email' => ($response) ? $response->email_registered : '',
			'response_timestamp' => ($response_activity) ? $response_activity->timestamp : ''
		);

		return $data;
	}

	public function get_date_created($inquiry_id) {
        $date_created = DB::connection('cms')
        ->table('inquiry_activities')
        ->select('timestamp')
        ->where('inquiry_id', $inquiry_id)
        ->where('activity', "Sent new inquiry")
        ->first();

        return $date_created;
    }

    public function add_log($log) {
    	DB::connection('cms')->table('response_inquiry')->insert($log);
    }

}
