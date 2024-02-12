<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use PHPMailer\PHPMailer;
use App\Api;
class ApiEmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apiEmail:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email about API provided by BPI';

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
        $recipients = DB::table('api_email_receiver')->get();
        $mail = new PHPMailer\PHPMailer(true);
        foreach($recipients as $recipient){
            
            
            $mail->isSMTP();  // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'rsis.bpi.philrice@gmail.com'; // SMTP username
            $mail->Password = 'rsisDA_2020'; // SMTP password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to

            //Recipients
            $mail->setFrom('rsis.bpi.philrice@gmail.com', 'Rice Seed Information System');
            $mail->addAddress($recipient->email); // Add a recipient
            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = 'API Access Summary Report as of '. Carbon::now()->format('M d, Y H:i');
            $mail->Body    = $this->send_summary();
            $mail->AltBody = 'Please enable HTML content to view this email.';

            $mail->send();
        }
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
                        'remarks' => 'PhilRice didn`t receive any data'
                    );
                }
            }

            if($item->api_name == 'variety'){
                if($item->value > 0){
                    $data[] = array(
                        'code' => 'variety',
                        'api_name' => 'Seed Variety',
                        'value' => $item->value,
                        'status' => 'Success',
                        'provider' => 'BPI-NSQCS',
                        'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
                        'remarks' => 'N/A'
                    );
                }else{
                    $data[] = array(
                        'code' => 'variety',
                        'api_name' => 'Seed Variety',
                        'value' => '0',
                        'status' => 'No data',
                        'provider' => 'BPI-NSQCS',
                        'timestamp' => Carbon::parse($item->timestamp)->format('H:i'),
                        'remarks' => 'PhilRice didn`t receive any data'
                    );
                }
            }
            /*if($item->api_name == 'growapp'){
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
            }*/
        }
        dd($data);
        return view('email.api_summary',compact('data'))->render();
    }
}
