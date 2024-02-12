<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
class Api extends Model
{
    protected $connection = "api";
    protected $primaryKey = "api_server_id";

    protected $table = "api_server";

    protected $fillable = ['domain_ip','isSSL','provider','status'];

    public $timestamps = false;

    public function insertDataLogs($request){
        
        $today = Carbon::today()->format("Y-m-d");
        $check = DB::table('api_logs')
        ->whereDate('timestamp', $today)
        ->where('api_name',$request['api_name'])
        ->count();
        if($check >= 1){
            return DB::connection('pgsql')
            ->table('api_logs')
            ->whereDate('timestamp',$today)
            ->where('api_name',$request['api_name'])
            ->update($request);
        }else{
            return DB::connection('pgsql')
            ->table('api_logs')
            ->insert($request);
        }
        
    }

    public function getAllLogs($filter){
        $query = DB::connection('pgsql')
        ->table('api_logs')
        ->select('*')
        //->take(5)
        ->orderBy('api_name','desc');
        if(!$filter['api_name'] == 0){
            $query->where('api_name',$filter['api_name']);
        }
        if(!$filter['date_from'] == 0 && !$filter['date_to'] == 0){
            $to = date('Y-m-d',strtotime($filter['date_to']));
            $query->whereBetween(DB::raw('DATE(timestamp)'),[$filter['date_from'],$to]);
        }

        return $query->get();
    }

    public function sgFilePath() {
        //return '/var/www/rsis/api/xml/bpi/APISG/APIDataAPISG';
        return base_path().'/APIDataAPISG';
    }

    public function scFilePath() {
    	//return '/var/www/rsis/api/xml/bpi/APISC/APIDataAPISC';
        return base_path().'/APIDataAPISC';
    }

    public function sfiFilePath() {
    	//return '/var/www/rsis/api/xml/bpi/SFI/APIDataSFI';
        return base_path().'/APIDataSFI';
    }

    public function spiFilePath() {
    	//return '/var/www/rsis/api/xml/bpi/SPI/APIDataSPI';
        return base_path().'/APIDataSPI';
    }

    public function stFilePath() {
    	//return '/var/www/rsis/api/xml/bpi/ST/APIDataST';
        return base_path().'/APIDataST';
    }
    public function rcefFilePath(){
        //return '/var/www/rsis/api/xml/bpi/APIRCEPLABTEST/APIDataAPIRCEPLABTEST';
        return base_path().'/APIDataAPIRCEPLABTEST';
    }
    public function varietyFilePath(){
        //return '/var/www/rsis/api/xml/bpi/APIVARIETY/APIDataAPIVARIETY';
        return base_path().'/APIDataAPIVARIETY';
    }
    function checkScApi(){
    	$filepath = $this->scFilePath();
    	$xml = @simplexml_load_file($filepath);
    	if($xml === false){
    		return false;
    	}else{
    		$json = json_encode($xml);
	        //$today = Carbon::today()->format("Y-m-d");
	        $data = json_decode($json,TRUE);
	        return $data;
    	}
	}

	function checkSgApi(){
		$filepath = $this->sgFilePath();
        $xml = @simplexml_load_file($filepath);
        if($xml === false){
    		return false;
    	}else{
    		$json = json_encode($xml);
	        //$today = Carbon::today()->format("Y-m-d");
	        $array = json_decode($json,TRUE);
	        return $array;
    	}
	}

	function checkSfiApi(){
		$filepath = $this->sfiFilePath();
        $xml = @simplexml_load_file($filepath);
        if($xml === false){
    		return false;
    	}else{
    		$json = json_encode($xml);
	        //$today = Carbon::today()->format("Y-m-d");
	        $array = json_decode($json,TRUE);
	        return $array;
    	}
	}

	function checkSpiApi(){
		$filepath = $this->spiFilePath();
        $xml = @simplexml_load_file($filepath);
        if($xml === false){
    		return false;
    	}else{
    		$json = json_encode($xml);
	        //$today = Carbon::today()->format("Y-m-d");
	        $array = json_decode($json,TRUE);
	        return $array;
    	}
	}

	function checkStApi(){
		$filepath = $this->stFilePath();
        $xml = @simplexml_load_file($filepath);
        if($xml === false){
    		return false;
    	}else{
    		$json = json_encode($xml);
	        //$today = Carbon::today()->format("Y-m-d");
	        $array = json_decode($json,TRUE);
	        return $array;
    	}
	}
    function checkRcepApi(){
        $filepath = $this->rcefFilePath();
        $xml = @simplexml_load_file($filepath);
        if($xml === false){
            return false;
        }else{
            $json = json_encode($xml);
            //$today = Carbon::today()->format("Y-m-d");
            $array = json_decode($json,TRUE);
            return $array;
        }
    }

    function checkVarietyApi(){
        $filepath = $this->varietyFilePath();
        $xml = @simplexml_load_file($filepath);
        if($xml === false){
            return false;
        }else{
            $json = json_encode($xml);
            //$today = Carbon::today()->format("Y-m-d");
            $array = json_decode($json,TRUE);
            return $array;
        }
    }
    function checkGrowAppApi(){
        $url = 'https://rsis.philrice.gov.ph/philrice_api/public/api/v1/sg_forms/f84621b3-a4db-451f-96b3-d96a4c56b26e';
        $data = file_get_contents($url);
        $json = json_decode($data,true);

        return $json['data'];
    }

    function activeSeedGrower(){
        $data = array();
        foreach($this->checkSgApi() as $value){
            if(Carbon::createFromFormat('Y-m-d',$value['AccreditationExpiryDate']) > Carbon::today()->format('Y-m-d')){
                $data[] = array(
                    'accredNo' => $value['AccreditationNo'],
                    'staticArea' => $value['AccreditatedArea'],
                    'totalArea' => $value['AccreditatedArea'],
                    'isAffiliated' => 0,
                    'committedArea' => 0,
                    'expirydate' =>$value['AccreditationExpiryDate']
                );
            }
        }
        return $data;
    }
}
