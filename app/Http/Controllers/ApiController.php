<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB, Entrust;
use Carbon\Carbon;
use App\Api;

use Yajra\Datatables\Datatables;
use PHPMailer\PHPMailer;
class ApiController extends Controller
{
	public function index(){
		


		/*$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://bpinsqcs.da.gov.ph/nsqcs-api/sg-api.php',
			CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_TIMEOUT => 30000,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "GET",
		    CURLOPT_HTTPHEADER => array(
		    	// Set Here Your Requesred Headers
		        'Content-Type: application/json',
		    ),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
		    dd ("cURL Error #:" . $err);
		} else {
		    dd(json_decode($response));
		}*/

/*		$url = "https://bpinsqcs.da.gov.ph/nsqcs-api/sg-api.php";
		$json = file_get_contents($url);
		$json_encode= json_encode($json);
		$json_data = json_decode($json_encode,true);
		$count = count($json_data);
		//dd($json_data);

		$api = new Api;
		dd($json_data);*/
/*		$date = '2020-07-10';
		$data = array(
                'api_name' => "sg",
                'value' => "0",
                'timestamp' => Carbon::today()->format('Y-m-d')
            );
		$today = Carbon::today()->format("Y-m-d");

		$check = DB::table('api_logs')
        ->where('timestamp', $today)
        ->count();
        if($check > 1){
            return DB::connection('pgsql')
            ->table('api_logs')
            ->where('timestamp',$today)
            ->update($data);
        }else{
            return DB::connection('pgsql')
            ->table('api_logs')
            ->insert($data);
        }
		//$time = strtotime(Carbon::now());
		//dd($time,time());
		//dd(time());
		$api = new Api;
		$dir = base_path().'/archive/'.$date;
		$test=scandir($dir,SCANDIR_SORT_DESCENDING);
		$xml = simplexml_load_file($dir.'/'.$test[0]);
		$json = json_encode($xml);
		$array = json_decode($json,true);
		//dd($array);*/
		return view('api.index');
	}
	public function storeEmailRecipient(Request $request){
		$email = $request->email;
		$is_programmer = ($request->has('is_programmer')) ? 1 : 0;

		DB::beginTransaction();
		try{
			DB::table('api_email_receiver')
			->insert(['email' => $email, 'is_programmer' => $is_programmer]);

			DB::commit();
			$res = 'success';
		}catch (Exception $e) {
            DB::rollback();
            $res = $e->getMessage();
        }
        /*if($res == 'success'){
        	return
        }*/
        return redirect()->route('api.index');
	}

	public function recipientDatatable(){
		$data = DB::table('api_email_receiver')->get();

		$data = collect($data);

		return Datatables::of($data)
        ->addColumn('actions', function($data) {
			return '<button class="btn btn-danger btn-sm deleteBtn" title="View"><i class="fa fa-trash"></i> Delete</button>';
        })
        ->rawColumns(['actions'])
        ->make(true);
	}

	public function store(Request $request) {
		$this->validate($request, [
			'provider' => 'required',
            'domain_ip' => 'required|unique:api.api_server,domain_ip',
            'link_address' => 'required',
            'category' => 'required'
        ]);
		
		$api = new Api;
		$api->domain_ip = $request->domain_ip;
		$api->isSSL =($request->has('isSSL')) ? "1" : "0";
		$api->provider = $request->provider;
		$api->save();

		$data = array(
			'link_address' => $request->link_address,
			'category' => $request->category,
			'status' => 1,
			'server' => $api->api_server_id
		);

		DB::table('api.api_link')->insert($data);
	}

	public function datatable(Request $request){
		$api = new Api;
		$from = $request->date_from;
		$to = $request->date_to;
		if($from == null && $to == null){
			$from = Carbon::yesterday()->format('Y-m-d');
			$to = Carbon::today()->format('Y-m-d');
		}

		$filter = array(
			'api_name' => $request->api_name,
			'date_from' => $from,
			'date_to' => $to
		);
		$items = $api->getAllLogs($filter);
		//dd($items);
		$data = array();
        foreach ($items as $key => $item) {
        	//print_r($key);
        	if($item->api_name == 'sc')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'sc',
		                'api_name' =>'Seed Cooperative',
		                'value' => $item->value,
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'sc',
		                'api_name' =>'Seed Cooperative',
		                'value' => 'No Data',
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		
        	}
        	if($item->api_name == 'sg')
        	{	
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'sg',
		                'api_name' =>'Seed Grower',
		                'value' => $item->value,
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'sg',
		                'api_name' =>'Seed Grower',
		                'value' => 'No Data',
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		
        	}
        	if($item->api_name == 'sfi')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'sfi',
		                'api_name' =>'Seed Final Inspection',
		                'value' => $item->value,
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'sfi',
		                'api_name' =>'Seed Final Inspection',
		                'value' => 'No Data',
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		
        	}

        	if($item->api_name == 'spi')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'spi',
		                'api_name' =>'Seed Preliminary Inspection',
		                'value' => $item->value,
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'spi',
		                'api_name' =>'Seed Preliminary Inspection',
		                'value' => 'No Data',
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
	            	);
        		}
        		
        	}

        	if($item->api_name == 'st')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'st',
		                'api_name' =>'Seed Testing',
		                'value' => $item->value,
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
		            );
        		}
        		else{
        			$data[] = array(
        				'code' => 'st',
		                'api_name' =>'Seed Testing',
		                'value' => 'No Data',
		                'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
		            );
        		}
        		
        	}

        	if($item->api_name == 'rceplabtest'){
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'rceplabtest',
        				'api_name' => 'RCEP Lab Test',
        				'value' => $item->value,
        				'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
        			);
        		}else{
        			$data[] = array(
        				'code' => 'rceplabtest',
        				'api_name' => 'RCEP Lab Test',
        				'value' => 'No Data',
        				'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
        			);
        		}
        	}

        	if($item->api_name == 'growapp'){
        		if($item->value > 0){
        			$data[] =array(
        				'code' => 'growapp',
        				'api_name' => 'GrowApp',
        				'value' => $item->value,
        				'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
        			);
        		}
        		else{
        			$data[] =array(
        				'code' => 'growapp',
        				'api_name' => 'GrowApp',
        				'value' => 'No Data',
        				'timestamp' => Carbon::parse($item->timestamp)->format('Y-m-d')
        			);
        		}
        	}
        }
		//return Datatables::of($data)->make(true);
/*		$tempArr = array_unique(array_column($data, 'code'));
		$data = array_intersect_key($data, $tempArr);*/

		return Datatables::of($data)
        ->addColumn('actions', function($data) {
			return '<button data-code='.$data['code'].' data-date='.$data['timestamp'].' class="btn btn-info btn-sm viewBtn" title="View"><i class="fa fa-eye"></i> View</button>';
        })
        ->rawColumns(['actions'])
        ->make(true);
	}

	
	public function viewApiDetail(Request $request){
		if($request->code == 'growapp'){
			$url = 'https://rsis.philrice.gov.ph/philrice_api/public/api/v1/sg_forms/f84621b3-a4db-451f-96b3-d96a4c56b26e';
	        $data = file_get_contents($url);
	        $json = json_decode($data,true);
	        $array = $json['data'];
		}
		else{
			$apiFileName = "APIDataAPI".strtoupper($request->code);
			//$apiFilePath = base_path()."/".$apiFileName;

			//serverside
			$apiFilePath = '/var/www/rsis/api/xml/bpi/API'.strtoupper($request->code).'/archive/'.$request->date;

			//localhost
			//$apiFilePath = base_path().'/'.$apiFileName;

			$file=scandir($apiFilePath,SCANDIR_SORT_DESCENDING);

			$xml = simplexml_load_file($apiFilePath.'/'.$file[0]);
			$json = json_encode($xml);
	/*		$array = json_decode($json,true);
			$xml = simplexml_load_file($apiFilePath);
			$json = json_encode($xml);*/

			$array = json_decode($json,true);
		}
		
		//return $array;
		//dd($array);
		return Datatables::of($array)
        //->addColumn('actions','Test')
        ->make(true);
		//$api = new Api;
	}


	public function send_summary(){
		$api = new Api;
		//$from = $request->date_from;
		//$to = $request->date_to;
		/*if($from == null && $to == null){
			$from = Carbon::yesterday()->format('Y-m-d');
			$to = Carbon::today()->format('Y-m-d');
		}*/

		$filter = array(
			'api_name' => 0,
			'date_from' => Carbon::now()->format('Y-m-d'),
			'date_to' => Carbon::now()->format('Y-m-d')
		);
		$items = $api->getAllLogs($filter);
		//dd($items);
		$data = array();
        foreach ($items as $key => $item) {
        	//print_r($key);
        	if($item->api_name == 'sc')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'sc',
		                'api_name' =>'Seed Cooperative',
		                'value' => $item->value,
		                'status' => 'Success',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'N/A'
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'sc',
		                'api_name' =>'Seed Cooperation',
		                'value' => '0',
		                'status' => 'No data',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'PhilRice didn`t receive any data'
	            	);
        		}
        		
        	}
        	if($item->api_name == 'sg')
        	{	
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'sg',
		                'api_name' =>'Seed Grower',
		                'value' => $item->value,
		                'status' => 'Success',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'N/A'
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'sg',
		                'api_name' =>'Seed Grower',
		                'value' => '0',
		                'status' => 'No data',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'PhilRice didn`t receive any data'
	            	);
        		}
        		
        	}
        	if($item->api_name == 'sfi')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'sfi',
		                'api_name' =>'Seed Final Inspection',
		                'value' => $item->value,
		                'status' => 'Success',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'N/A'
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'sfi',
		                'api_name' =>'Seed Final Inspection',
		                'value' => '0',
		                'status' => 'No data',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'PhilRice didn`t receive any data'
	            	);
        		}
        		
        	}

        	if($item->api_name == 'spi')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'spi',
		                'api_name' =>'Seed Preliminary Inspection',
		                'value' => $item->value,
		                'status' => 'Success',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'N/A'
	            	);
        		}
        		else{
        			$data[] = array(
        				'code' => 'spi',
		                'api_name' =>'Seed Preliminary Inspection',
		                'value' => '0',
		                'status' => 'No data',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'PhilRice didn`t receive any data'
	            	);
        		}
        		
        	}

        	if($item->api_name == 'st')
        	{
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'st',
		                'api_name' =>'Seed Testing',
		                'value' => $item->value,
		                'status' => 'Success',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'N/A'
		            );
        		}
        		else{
        			$data[] = array(
        				'code' => 'st',
		                'api_name' =>'Seed Testing',
		                'value' => '0',
		                'status' => 'No data',
		                'provider' => 'BPI-NSQCS',
		                'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
		                'remarks' => 'PhilRice didn`t receive any data'
		            );
        		}
        		
        	}

        	if($item->api_name == 'rceplabtest'){
        		if($item->value > 0){
        			$data[] = array(
        				'code' => 'rceplabtest',
        				'api_name' => 'RCEP Lab Test',
        				'value' => $item->value,
        				'status' => 'Success',
        				'provider' => 'BPI-NSQCS',
        				'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
        				'remarks' => 'N/A'
        			);
        		}else{
        			$data[] = array(
        				'code' => 'rceplabtest',
        				'api_name' => 'RCEP Lab Test',
        				'value' => '0',
        				'status' => 'No data',
        				'provider' => 'BPI-NSQCS',
        				'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
        			);
        		}
        	}
        	if($item->api_name == 'growapp'){
        		if($item->value > 0){
        			$data[] =array(
        				'code' => 'growapp',
        				'api_name' => 'GrowApp',
        				'value' => $item->value,
        				'status' => 'Success',
        				'provider' => 'PhilRice',
        				'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
        				'remarks' => 'N/A'
        			);
        		}
        		else{
        			$data[] =array(
        				'code' => 'growapp',
        				'api_name' => 'GrowApp',
        				'value' => '0',
        				'status' => 'No data',
        				'provider' => 'PhilRice',
        				'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
        				'remarks' => 'PhilRice didn`t sent any data'
        			);
        		}
        	}
        }
		return view('email.api_summary',compact('data'));
	}

}
