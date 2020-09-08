<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\User;
use App\Block;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;





class ApiController extends Controller
{
    public function getData($type, $currency, $page){ 
        
        $page_size = 50;
		$sign_key    = $this->getPasswords()[0];
		$sign_SECRET = $this->getPasswords()[1];
		$sign_id     = $this->getPasswords()[2];


		$nonce = time();#毫秒时间戳
		$hmac_message = $sign_id.$sign_key.$nonce;
		$hmac = strtoupper(hash_hmac('sha256', $hmac_message, $sign_SECRET, false));
	
		// create curl request
		$post_fields = array(
			'key' => $sign_key,
			'nonce' => $nonce,
			'signature' => $hmac,
			'coin' => $currency
		);

		if($type === 'paymentHistoryV2')
			$post_fields = array_merge( $post_fields, array('pageSize' => $page_size), array('type' => 'recv'), array('page' => $page));


		if($type === 'workers')
			$post_fields = array_merge( $post_fields, array('pageSize' => $page_size));

	
		$post_data = '';
		foreach($post_fields as $key => $value) {
			$post_data.= $key.'='.$value.'&';
		}
		
		rtrim($post_data, '&');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://antpool.com/api/'.$type.'.htm');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
		curl_setopt($ch, CURLOPT_POST, count($post_fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$result = curl_exec($ch);
		curl_close($ch);

		// check if curl was timed out
		if ($result === false) {
			if (print_r('print_error_if_api_down')) {
				exit('Error: No API connect');
			} else {
				exit();
			}
		}

		// validate JSON
		$result_json = json_decode($result);
		if (json_last_error() != JSON_ERROR_NONE) exit('Error: read broken JSON from API - JSON Error: '.json_last_error().' ('.$result.')');

		if ($result_json->message == 'ok') {
			return ['getData' => $result_json->data];
		} else {
			exit('API Error: '.print_r($result_json, true));
		}
        
    }

    public function getPasswords(){ 
        
        $user = User::where('id', Auth::id())->first();
        if($user->password_2)
        	$password_1 = Crypt::decryptString($user->password_2);
		else $password_1 = '';

		if($user->password_3)
        	$password_2 = Crypt::decryptString($user->password_3);
		else $password_2 = '';
		
		return [$password_1, $password_2, $user->user];
        
    }

    public function getLastBlockPool(){

        $block = new Block();


        if(getLastBlockPool()){

            $is_block = Block::where('height', getLastBlockPool()["height"])->first();
            
            if (!$is_block) {
                $block->height = getLastBlockPool()["height"];
                $block->time = getLastBlockPool()["time"];
                $block->hash = getLastBlockPool()["hash"];
                $block->save();
            }
            return ['block' => getLastBlockPool()];
        }else{
            return ['block' => 'Sin coincidencia'];
        }

	}
	
	public function getMarkets(){ 

		$coin_type           = 'BTC';#币种
		$sign_id             = 'juanmeanho';# 子账号名
		$sign_key            = 'af1ca86c175d459fb6c44ac26a52d603';#密钥
		$sign_SECRET         = '28aa0919dde346a786018c959fa45839';#密码
		
		$nonce = time();#毫秒时间戳
		$hmac_message = $sign_id.$sign_key.$nonce;
		$hmac = strtoupper(hash_hmac('sha256', $hmac_message, $sign_SECRET, false));
	
		// create curl request
		// $post_fields = array(
		// 	'key' => $sign_key,
		// 	'nonce' => $nonce,
		// 	'signature' => $hmac,
		// 	'coin' => $coin_type
		// );
			
	
			// $post_data = '';
			// foreach($post_fields as $key => $value) {
			// 	$post_data.= $key.'='.$value.'&';
			// }
			
			// rtrim($post_data, '&');

		$client = new Client(["base_uri" => "https://antpool.com", 'verify' => false, 

		'headers' => [
			'Content-Type' => 'application/json',
		],
		'curl' => [
			CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
			CURLOPT_SSL_VERIFYPEER => false
		]]);

		// $client = new Client([
		// 	'form_params' => [
		// 		'key' => $sign_key,
		//  		'nonce' => $nonce,
		//  		'signature' => $hmac,
		// 		'coin' => $coin_type
		// 	]
		// 	'verify' => false, 'curl' => [CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2]
		// ]);
		
		$options = [
			'form_params' => [
				'key' => $sign_key,
		 		'nonce' => $nonce,
		 		'signature' => $hmac,
				'coin' => $coin_type
			]
		]; 

		//$response = json_decode($client->request('GET', "https://antpool.com/api/poolStats.htm")->getBody());

		$response = $client->post("api/poolStats.htm", $options);

		return  $response->getBody();
	}


 }


