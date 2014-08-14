<?php
	require '../../util/helpers.php';
	
	try 
	{
	  	header("Content-type: application/json");
	  	
	  	$collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
	  	
	  	//Get the first time stamp
	  	$filter = array("agent" => "monitor_agent",
	  					"trafficDirection" => "out",
	  					"peerNode" => array('$in' => array('uc-0', 'c-0')));
	  	$tsFirstRecord = getFirstRecordTimestamp($collection, $filter);
	  	//print_r($tsFirstRecord);
	  	
	  	//Calulate the new time stamp
	  	$lastTimestamp = $tsFirstRecord + (float)$relativeLastTimeStamp;
	  	 
	  	$filter["created"] = array('$gt' => $lastTimestamp);
	  	$cursor = $collection->find($filter, 
	  								array("created" => 1, "bytes" => 1, "peerNode" => 1, "_id" => 0))
	  								->sort(array("created" => 1))->limit($recordsLimit);
		$result = iterator_to_array($cursor);
		
		$formatted_result = array();
		
		foreach ($result as $record){
			$record["created"] = $record["created"] - $tsFirstRecord;
			$record["bytes"] = $record["bytes"] / 1000000;
			array_push($formatted_result, $record);
		}
		
		echo json_encode($formatted_result);
	  	
	}catch (MongoConnectionException $e) {
		die("Error connecting to MongoDB server: ". $dbHost . ":" . $dbPort);
	}catch (MongoException $e) {
	 	die('Error: ' . $e->getMessage());
	}
	
?>
