<?php

//Uwaga! Nalezy zmienic w pliku schemy blfDest dla XbusyLampField minoccurs na 0
//https://bst.cloudapps.cisco.com/bugsearch/bug/CSCvs40005
//
//Format pliku wejsciowego: Index,BLF Dest,BLF Dirn,Partycja,Label,Pickup(boolean)\n
//BLF Dest nie jest brany pod uwage.

$options = getopt("h:l:p:d:f:");

$host=$options["h"];
$login=$options["l"];
$password=$options["p"];
$phone=$options["d"];
$myfile = $options["f"];

//Czyszczenie cache
ini_set("soap.wsdl_cache_enabled", 0);

$context = stream_context_create(array('ssl'=>array('allow_self_signed'=>true,'verify_peer'=> false,'verify_peer_name'=> false)));

   $client = new SoapClient("AXLAPI.wsdl",
                array('trace'=>true,
               'exceptions'=>true,
               'location'=>"https://".$host.":8443/axl",
               'login'=>$login,
               'password'=>$password,
               'stream_context'=>$context
            ));

//$busyLampFields=array("busyLampField"=>array("index"=>"1","blfDirn"=>"104","routePartition"=>"Base","label"=>"Ksdfjsdoij","associatedBlfSdFeatures"=>array("feature"=>"Pickup")));

//Czytanie pliku
$handler = fopen($myfile, "r") or die("Unable to open file!");

    while(!feof($handler)) {
	$line=str_replace(array("\r", "\n"), '', fgets($handler));
	if($line){
	    $lineTab=explode(",",$line);
		$record = array("index"=>$lineTab[0],
			"blfDirn"=>$lineTab[2],
			"routePartition"=>$lineTab[3],
			"label"=>$lineTab[4]);
			if($lineTab[5]){
			$record["associatedBlfSdFeatures"]=array("feature"=>"Pickup");
			}
	$busyLampField[] = $record;
	}
    }

$busyLampFields = array("busyLampField"=>$busyLampField);

print_r($busyLampFields);

     try {
    $response = $client->updatePhone(array("name"=>$phone,"busyLampFields"=>$busyLampFields));
    }
    catch (SoapFault $sf) {
        echo "SoapFault: " . $sf ."\n";
    }
    catch (Exception $e) {
        echo "Exception: ". $e ."\n";
    }

print_r($response);

?>