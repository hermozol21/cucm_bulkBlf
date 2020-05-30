<?php

//Usage#php blfRead.php -h 192.168.0.10 -l login -p password -d SEP123456789012

$options = getopt("h:l:p:d:");

$host=$options["h"];
$login=$options["l"];
$password=$options["p"];
$phone=$options["d"];

//ini_set("soap.wsdl_cache_enabled", 0);

$context = stream_context_create(array('ssl'=>array('allow_self_signed'=>true,'verify_peer'=> false,'verify_peer_name'=> false)));
$returnedTags = array("busyLampFields"=>array("busyLampField"=>array("index"=>"","blfDest"=>"","blfDirn"=>"","routePartition"=>"","label"=>"","associatedBlfSdFeatures"=>"")));

   $client = new SoapClient("AXLAPI.wsdl",
                array('trace'=>true,
               'exceptions'=>true,
               'location'=>"https://".$host.":8443/axl",
               'login'=>$login,
               'password'=>$password,
               'stream_context'=>$context
            ));
     try {
    $response = $client->getPhone(array("name"=>$phone,"returnedTags"=>$returnedTags));
    }
    catch (SoapFault $sf) {
        echo "SoapFault: " . $sf . "\n";
    }
    catch (Exception $e) {
        echo "Exception: ". $e ."\n";
    }


//print_r($response);

//Czy tablica?
if(gettype($response->return->phone->busyLampFields->busyLampField) != "array"){
echo "TYPE NOT ARRAY: " , gettype($response->return->phone->busyLampFields->busyLampField) , ". EXIT\n";
exit;

    }
//Formatowanie wyjscia

//echo("Index,BLF Dest,BLF Dirn,Partition,Label,Pickup\n");

 foreach($response->return->phone->busyLampFields->busyLampField as $blf) {
        echo($blf->index.",");
        echo($blf->blfDest.",");
        echo($blf->blfDirn.",");
	echo($blf->routePartition.",");
        echo($blf->label.",");
        if(isset($blf->associatedBlfSdFeatures->feature)) {
	    echo(true);
	    }
        echo("\n");
    }


?>