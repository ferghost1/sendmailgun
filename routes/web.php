<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/send_mail', function () {
    $files = Storage::allFiles('mail_template');
    foreach ($files as &$file) {
        $file = explode('/', $file);
        $file = end($file);
    }
    
    return view('send_mail', ['files' => $files]);
});

Route::post('/send_mail', function (Request $req) {
    $content = Storage::get("mail_template/$req->template_name");
    $clients = DB::table('tbl_events_email')->where('b_show', 0)->get();
    $api_key="key-8f321d5f2d19f15e7c3fa75474cdcafe";// Api Key got from https://mailgun.com/cp/my_account /
    $domain ="beis.beintl.com"; // Domain Name you given to Mailgun /
    $from = $req->from ?? 'itBee Solutions <info@itbeesolutions.com>';
    $result = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'api:'.$api_key);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/'.$domain.'/messages');
    
    foreach($clients as $client) {
        // replace var in content
        $filteredContent = $content;
        $filteredContent = str_replace("#*t_company_name*#", $client->t_company_name, $filteredContent);
        $filteredContent = str_replace("#*tel_no*#", $client->tel_no, $filteredContent);
        $filteredContent = str_replace("#*t_email*#", $client->t_email, $filteredContent);
        $filteredContent = str_replace("#*t_event_desc*#", $client->t_event_desc, $filteredContent);
        $filteredContent = str_replace("#*t_country*#", $client->t_country, $filteredContent);

        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'from' => $from,
            'to' => $client->t_email,
            'subject' => $req->subject ?? 'Hi friend',
            'html' => $filteredContent
        ));
        $res = json_decode(curl_exec($ch));

        if (!empty($res->id)) {
            DB::table('tbl_events_email')
                ->where('t_event_email_id', $client->t_event_email_id)
                ->update(['b_show' => 1]);
        } 

        $result[] = array('email' => $client->t_email, 'result' => $res);
    };
    
    dump($result);
    
});
