<?php


use GuzzleHttp\Client;
use App\User;
use Illuminate\Support\Facades\Auth;

use function GuzzleHttp\json_decode;

function getLastBlock() { // Obtiene dato del Ultimo bloque de la blockchain

		$client = new Client([
			'Content-Type' => 'application/json',
		]);

		$response = json_decode($client->request('GET', "https://blockchain.info/latestblock")->getBody());

		return $response;

	}

	function getLastBlockPoolHelper() { //Obtiene el height del ultimo bloque en las stats del pool para comparar

		$last_blockchain_block = getLastBlock();

		$client = new Client();

		$source_pool = $client->request('GET', "https://www.antpool.com/poolStats.htm")->getBody()->getContents();

		//$coinciden = strpos($source_pool, 'chain.btc.com/block/647780');
		$coinciden = strpos($source_pool, 'chain.btc.com/block/'.$last_blockchain_block->height);
		
		if($coinciden){
			$ts = $last_blockchain_block->time;
			$fecha = new DateTime("@$ts");

			$block_data = ['height' => $last_blockchain_block->height,
						   'time'   => $fecha->format('Y-m-d H:i:s'),
						   'hash'   => $last_blockchain_block->hash];

		}else{
			$block_data = [];
		}

		return $block_data;

	}

?>