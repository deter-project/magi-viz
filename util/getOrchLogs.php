<?php
	require './helpers.php';
	
	try 
	{
	  	header("Content-type: application/json");
	  	
	  	$collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
	  	//print_r($collection);
	  	
	  	//Get the first time stamp
	  	$filter = array("agent" => "orchdisplay");
	  	
	  	//Calculate the new time stamp
	  	$lastTimestamp = (float)$relativeLastTimeStamp;
	  	
	  	$filter["created"] = array('$gt' => $lastTimestamp);
	  	$cursor = $collection->find($filter, 
	  								array("created" => 1, "stream" => 1, "type" => 1, "msg" => 1, "_id" => 0))
	  								->sort(array("created" => 1))->limit($recordsLimit);
		
		$result = iterator_to_array($cursor);
		
		//var_dump($result);
		//print_r($result);
		
		$formatted_result = array();
		
		foreach ($result as $record){
			array_push($formatted_result, $record);
		}
		
		echo json_encode($formatted_result);
	  	
	}catch (MongoConnectionException $e) {
		die("Error connecting to MongoDB server: ". $dbHost . ":" . $dbPort);
	}catch (MongoException $e) {
	 	die('Error: ' . $e->getMessage());
	}
	
?>
