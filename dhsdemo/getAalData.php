<?php

	require '../util/helpers.php';

	try 
	{
	  	header("Content-type: application/json");
	  		
	  	$collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
	  	
	  	//Get the first time stamp
	  	$filter = array("agent" => "aal");
	  	$filter["aalSvg"] = array('$exists' => false);
	    $tsFirstRecord = getFirstRecordTimestamp($collection, $filter);
	    
	    //Calulate the new time stamp
	    $lastTimestamp = $tsFirstRecord + (float)$relativeLastTimeStamp;

	    $filter["created"] = array('$gt' => $lastTimestamp);
	    $cursor = $collection->find($filter)->sort(array("created" => 1))->limit($recordsLimit);
	    $records = iterator_to_array($cursor);
	    //print_r($records);
	
	    //Array for JSON data
	    $formatted_result = array();
	    
	    foreach ( $cursor as $id => $value )
	    {
	        $relativeCreatedTime = round(($value["created"] - $tsFirstRecord), 8);
	        $streamName = $value["streamName"];
	        $eventItr = $value["eventItr"];
	        $eventType = $value["eventType"];
	        $eventLabel = $value["eventLabel"];
	        $record = array($relativeCreatedTime, $streamName, $eventItr, $eventType, $eventLabel);
	        array_push($formatted_result, $record);
	    }   
	    
		echo json_encode($formatted_result);
	  	
	}catch (MongoConnectionException $e) {
		die("Error connecting to MongoDB server: ". $dbHost . ":" . $dbPort);
	}catch (MongoException $e) {
	 	die('Error: ' . $e->getMessage());
	}
	
?>
