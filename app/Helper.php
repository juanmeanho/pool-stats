<?php


use GuzzleHttp\Client;
use App\User;
use Illuminate\Support\Facades\Auth;


	function getLastBlock() { // Obtiene el height del Ultimo bloque de la blockchain

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://blockchain.info/latestblock');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === false) {
			if (print_r('print_error_if_api_down')) {
				exit('Error: No API connect');
			} else {
				exit();
			}
		}
		// validate JSON
		$result_json = json_decode($result);
		if (json_last_error() != JSON_ERROR_NONE) exit('Error: '.json_last_error().' ('.$result.')');
			
		return $result_json;

	}

	function getLastBlockPool() { //Obtiene el height del ultimo bloque en las stats del pool para comparar

		$last_blockchain_block = getLastBlock();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://www.antpool.com/poolStats.htm');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$source_pool = curl_exec($ch);
		curl_close($ch);

		if ($source_pool === '') {
			if (print_r('print_error_if_api_down')) {
				exit('Error: No API connect');
			} else {
				exit();
			}
		}

		//$coinciden = strpos($source_pool, 'chain.btc.com/block/646729');
		$coinciden = strpos($source_pool, 'chain.btc.com/block/'.$last_blockchain_block->height);
		
		if($coinciden){
			$ts = $last_blockchain_block->time;
			$fecha = new DateTime("@$ts");

			//$resp = "guardar ". $last_blockchain_block->height; //si esta
			$block_data = ['height' => $last_blockchain_block->height,
						   'time'   => $fecha->format('Y-m-d H:i:s'),
						   'hash'   => $last_blockchain_block->hash];

		}else{
			$block_data = [];
		}

		return $block_data;

	}

?>