<?php


use GuzzleHttp\Client;

	//Used to get the open and available trading markets at Bittrex along with other meta data.

	function getMarkets() {

	$uri = env('URI').'public/getmarkets?apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getCurrencies() {

	$uri = env('URI').'public/getcurrencies?apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getTicker($market) {

	$uri = env('URI').'public/getticker?market='.$market.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getmarketSummaries() {

	$uri = env('URI').'public/getmarketsummaries?apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getmarketSummary($market) {

	$uri = env('URI').'public/getmarketsummary?market='.$market.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getOrderBook($market, $type) {

	$uri = env('URI').'public/getorderbook?market='.$market.'&type='.$type.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getmarketHistory($market) {

	$uri = env('URI').'public/getmarkethistory?market='.$market.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

//-----------------------------Market Functions ---------------------------------//

    function buyLimit($market, $quantity, $rate, $timeInForce) {

	$uri = env('URI').'market/buylimit?market='.$market.'&quantity='.$quantity.'&rate='.$rate.'&timeInForce='.$timeInForce.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function sellLimit($market, $quantity, $rate, $timeInForce) {

	$uri = env('URI').'market/selllimit?market='.$market.'&quantity='.$quantity.'&rate='.$rate.'&timeInForce='.$timeInForce.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function cancel($uuid) {

	$uri = env('URI').'market/cancel?uuid='.$uuid.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getOpenOrders($market) {

	$uri = env('URI').'market/getopenorders?market='.$market.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

 //----------------------------Account Functions ---------------------------------//


    function getBalances() {

	$uri = env('URI').'account/getbalances?apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getBalance($currency) {

	$uri = env('URI').'account/getbalance?currency='.$currency.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getDepositAddress($currency) {

	$uri = env('URI').'account/getdepositaddress?currency='.$currency.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function withdraw($currency, $quantity, $rate) {

	$uri = env('URI').'account/withdraw?currency='.$currency.'&quantity='.$quantity.'&rate='.$rate.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getOrder($uuid) {

	$uri = env('URI').'account/getorder?uuid='.$uuid.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getOrderHistoryAll() {

	$uri = env('URI').'account/getorderhistory?apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getOrderHistory($market) {

	$uri = env('URI').'account/getorderhistory?market='.$market.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getWithdrawalHistory($currency) {

	$uri = env('URI').'account/getwithdrawalhistory?currency='.$currency.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }

    function getDepositHistory($currency) {

	$uri = env('URI').'account/getdeposithistory?currency='.$currency.'&apikey='.env('API_KEY').'&nonce='.time();

		$sign=hash_hmac('sha512',$uri,env('API_SECRET'));
		$client = new Client([
			'headers' => ['apisign' => $sign],
		]);

		$response = json_decode($client->request('GET', $uri)->getBody());

		return $response;
    }


?>