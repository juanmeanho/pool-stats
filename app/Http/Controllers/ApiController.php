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


		$nonce = time();
		$hmac_message = $sign_id.$sign_key.$nonce;
		$hmac = strtoupper(hash_hmac('sha256', $hmac_message, $sign_SECRET, false));
	
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

	
		$client = new Client([
			"base_uri" => "https://antpool.com", 
			'verify' => false, 
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'curl' => [
				CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
				CURLOPT_SSL_VERIFYPEER => false
			]
		]);

		$options = [
			'form_params' => $post_fields
		]; 

		$response = $client->post("api/".$type.".htm", $options);
		
        if ($response->getReasonPhrase() === 'OK') {
            return  $response->getBody();
        }else{
			print_r('print_error_if_api_down');
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
		
		$blockHelper = getLastBlockPoolHelper();

        if($blockHelper){

            $is_block = Block::where('height', $blockHelper["height"])->first();
            
            if (!$is_block) {
                $block->height = $blockHelper["height"];
                $block->time = $blockHelper["time"];
                $block->hash = $blockHelper["hash"];
                $block->save();
            }
            return ['block' => $blockHelper];
        }else{
            return ['block' => 'Sin coincidencia'];
        }

	}

 }


