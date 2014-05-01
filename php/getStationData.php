<?php
/*
 * Gibt die Daten einer Haltestelle als XML zurück
 * 
 * Parameter: station
 * Beschreibung: Gibt die externalId für den Request an (Interne Nummer der Haltestelle)
 * Beispiel: ?station=000696044#80 (Haltestelle Am Wall, Bremen)
 *
 */
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
//header ("Content-Type:text/xml");


class getStationData {

	var $url = "http://fahrplaner.vbn.de/hafas/mgate.exe/dl";
	var $station = "000696044#80"; //Am Wall, Bremen
	var $request = "";
	var $time;
	var $date;
	
	function getStationData(){
		$this->date = date("Ymd");
		$this->time = date("H:i:s");
		$this->generateRequest();
		$this->startRequest();
	}
	
	function generateRequest(){
		$this->request =  "<?xml version='1.0' encoding='iso-8859-1'?>".
					"<ReqC ver='1.1' prod='String' lang='de'>".
						"<STBReq boardType='DEP'>".
							"<Time>".$this->time."</Time>".
							"<Period>".
								"<DateBegin>".$this->date."</DateBegin>".
								"<DateEnd>".$this->date."</DateEnd>".
							"</Period>".
							"<TableStation externalId='000696044#80'/>".
							"<ProductFilter>1111111111111111</ProductFilter>".
						"</STBReq>".
					"</ReqC>";
	}

	function startRequest(){
		$headers = array(
				"Content-type: application/xml;charset=\"utf-8\"",
				"Connection: close"
		);
		
		
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_POST, 1);
		
			// send xml request to a server
		
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
			curl_setopt($ch, CURLOPT_POSTFIELDS,  $this->request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$data = curl_exec($ch);
		
			//convert the XML result into array
			if($data === false){
				$error = curl_error($ch);
				echo $error;
				die('error occured');
			}else{
				$xml = simplexml_load_string($data);
				$json = json_encode($xml);
				print($json);
			}
			curl_close($ch);
		
		}catch(Exception  $e){
			echo 'Message: ' .$e->getMessage();die("Error");
		}
	}

}

$main = new getStationData();