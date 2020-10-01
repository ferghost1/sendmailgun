<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    return view('send_mail');
});

Route::post('/send_mail', function (Request $req) {
    $content = file_get_contents('content.html');
    $clients = DB::table('tbl_events_email')->where('b_show', 0)->get();
    $api_key="key-8f321d5f2d19f15e7c3fa75474cdcafe";// Api Key got from https://mailgun.com/cp/my_account /
    $domain ="beis.beintl.com"; // Domain Name you given to Mailgun /
    $from = $req->from ?? 'itBee Solutions <info@itbeesolutions.com>';
    $result = [];
    $ch = curl_init();
    foreach($clients as $client) {
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:'.$api_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/'.$domain.'/messages');
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'from' => $from,
            'to' => $client->t_email,
            'subject' => $req->subject ?? 'Hi friend',
            'html' => $content
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
