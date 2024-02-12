<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Auth;
use App\Api;
use Carbon\Carbon;
class CheckApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the provided api of BPI.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $api = new Api;
        //insert the number of data of SG API
        if($api->checkSgApi()){
            $data = array(
                'api_name' => "sg",
                'value' => count($api->checkSgApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $sgApi = new Api;
            $sgApi->insertDataLogs($data);
        }else{
            $data = array(
                'api_name' => "sg",
                'value' => "0",
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $sgApi = new Api;
            $sgApi->insertDataLogs($data);
        }

        //insert the number of data of SC API
        if($api->checkScApi()){
            $scData = array(
                'api_name' => "sc",
                'value' => count($api->checkScApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $scApi = new Api;
            $scApi->insertDataLogs($scData);
        }else{
            $scData = array(
                'api_name' => "sc",
                'value' => "0",
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $scApi = new Api;
            $scApi->insertDataLogs($scData);
        }

        //insert the number of data of SFI API
        if($api->checkSfiApi()){
            $sfiData = array(
                'api_name' => "sfi",
                'value' => count($api->checkSfiApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $sfiApi = new Api;
            $sfiApi->insertDataLogs($sfiData);
        }else{
            $sfiData = array(
                'api_name' => "sfi",
                'value' => "0",
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $sfiApi = new Api;
            $sfiApi->insertDataLogs($sfiData);
        }

        //insert the number of data of SPI API
        if($api->checkSpiApi()){
            $spiData = array(
                'api_name' => "spi",
                'value' => count($api->checkSpiApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $spiApi = new Api;
            $spiApi->insertDataLogs($spiData);
        }else{
            $spiData = array(
                'api_name' => "spi",
                'value' => "0",
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $spiApi = new Api;
            $spiApi->insertDataLogs($spiData);
        }

        //insert the number of data of ST API
        if($api->checkStApi()){
            $stData = array(
                'api_name' => "st",
                'value' => count($api->checkStApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $stApi = new Api;
            $stApi->insertDataLogs($stData);
        }else{
            $stData = array(
                'api_name' => "st",
                'value' => "0",
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $stApi = new Api;
            $stApi->insertDataLogs($stData);
        }

        if($api->checkRcepApi()){
            $rcepData = array(
                'api_name' => "rceplabtest",
                'value' => count($api->checkRcepApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $rcepApi = new Api;
            $rcepApi->insertDataLogs($rcepData);
        }else{
            $rcepData = array(
                'api_name' => "rceplabtest",
                'value' => "0",
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $rcepApi = new Api;
            $rcepApi->insertDataLogs($rcepData);
        }

        if($api->checkVarietyApi()){
            $rcepData = array(
                'api_name' => "variety",
                'value' => count($api->checkVarietyApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $rcepApi = new Api;
            $rcepApi->insertDataLogs($rcepData);
        }else{
            $rcepData = array(
                'api_name' => "variety",
                'value' => "0",
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $rcepApi = new Api;
            $rcepApi->insertDataLogs($rcepData);
        }
        /*if($api->checkGrowAppApi()){
            $growAppData = array(
                'api_name' => 'growapp',
                'value' => count($api->checkGrowAppApi()),
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );

            $growAppApi = new Api;
            $growAppApi->insertDataLogs($growAppData);
        }
        else{
            $growAppData = array(
                'api_name' => 'growapp',
                'value' => '0',
                'timestamp' => Carbon::now()->format('Y-m-d H:i:s')
            );
            $growAppApi = new Api;
            $growAppApi->insertDataLogs($growAppData);
        }*/
    }
}
