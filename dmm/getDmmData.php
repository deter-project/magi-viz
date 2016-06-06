<?php

	set_error_handler("warning_handler", E_WARNING);
    
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
	require "$root/magi-viz/util/helpers.php";
	
	function warning_handler($errno, $errstr) { 
        throw new Exception($errstr);
    }

	try 
	{
	  	header("Content-type: application/json");
	  		  	
	  	$collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
	  	
	  	$filter = array("agent" => "iso_agent");
	  	
	  	//Get the first time stamp
	  	$tsFirstRecord = 0; #getFirstRecordTimestamp($collection, $filter);
	  	//print_r($tsFirstRecord);
	  	
	  	//Calculate the new time stamp
	  	$lastTimestamp = $tsFirstRecord + (float)$relativeLastTimeStamp;
	  	//print_r($lastTimestamp);
	  	
	  	$sort = 1;
	  	$skip = $recordsLimit - 1;
	  	
	  	if ($recordsLimit == 0) {
	  		$sort = -1;
	  		$skip = 0;
	  	}
	  	
	  	// Fetching Generator Status
	  	$filter = array("agent" => "iso_agent");
	  	$filter["statusGens"] = array('$exists' => true);
	  	$filter["k"] = array('$gt' => $lastTimestamp);
	  	//$cursor = $collection->find($filter)->sort(array("created" => -1))->limit(1);
	  	$cursor = $collection->find($filter)->sort(array("created" => $sort));
	  	$cursor->skip($skip)->limit(1);
	  	$records = iterator_to_array($cursor);
	  	//print_r($records);
	  	//print_r(sizeof($records));
	  	if (sizeof($records) == 0){
	  		echo json_encode(array());
	  		return;
	  	}
	  	foreach ( $cursor as $id => $value ) {
	  		$statusGens = $value["statusGens"];
	  		//print_r($value["k"]); print "\n";
	  	}
	  	
	  	// Fetching last repsonse
	  	$filter = array("agent" => "iso_agent");
	  	$filter["lastResponse"] = array('$exists' => true);
	  	$filter["k"] = array('$gt' => $lastTimestamp);
	  	$cursor = $collection->find($filter)->sort(array("created" => $sort));
	  	$cursor->skip($skip)->limit(1);
	  	//$records = iterator_to_array($cursor);
	  	//print_r($records);
	  	foreach ( $cursor as $id => $value ) {
	  		$lastResponse = $value["lastResponse"];
	  		//print_r($value["k"]); print "\n";
	  	}
	  	//print_r($lastResponse);
	  	
	  	// Fetching deception
	  	$filter = array("agent" => "gen_agent");
	  	$filter["gradFDeception"] = array('$exists' => true);
	  	$filter["k"] = array('$gt' => $lastTimestamp);
	  	$cursor = $collection->find($filter)->sort(array("created" => $sort));
	  	$cursor->skip($skip*54)->limit(54);
	  	$records = iterator_to_array($cursor);
	  	//print_r($records);
	  	$deception = array();
	  	foreach ( $cursor as $id => $value ) {
	  		$genNum = explode("-", $value["host"])[1];
	  		$deception[$genNum] = $value["gradFDeception"];
	  		//print_r($value["k"]); print "\n";
	  	}
	  	//print_r($deception);
	  	
	  	// Fetching commanded generation
	  	$filter = array("agent" => "iso_agent");
	  	$filter["pg"] = array('$exists' => true);
	  	$filter["k"] = array('$gt' => $lastTimestamp);
	  	$cursor = $collection->find($filter)->sort(array("created" => $sort));
	  	$cursor->skip($skip)->limit(1);
	  	//$records = iterator_to_array($cursor);
	  	//print_r($records);
	  	foreach ( $cursor as $id => $value ) {
	  		$pg = $value["pg"];
	  		//print_r($value["k"]); print "\n";
	  	}
	  	//print_r($pg);
	  	
	  	// Fetching actual generation
	  	$filter = array("agent" => "grid_agent");
	  	$filter["y"] = array('$exists' => true);
	  	$filter["k"] = array('$gt' => $lastTimestamp);
	  	$cursor = $collection->find($filter)->sort(array("created" => $sort));
	  	$cursor->skip($skip)->limit(1);
	  	//$records = iterator_to_array($cursor);
	  	//print_r($records);
	  	foreach ( $cursor as $id => $value ) {
	  		$y = $value["y"];
	  		$created = $value["k"];
	  		//print_r($value["k"]); print "\n";
	  	}
	  	//print_r($y);
	  	
		$nodes = array();
		$node_data = array("commanded" => "Undefined",
				           "actual" => "Undefined",
				           "active" => true
		);
		$nodes[0] = $node_data;
		
		foreach (range(1, 54) as $node_num) {
			$node_data = array("commanded" => round($pg[$node_num-1], 2),
					           "actual" => round($y[118*2+$node_num-1], 2),
					           "active" => $statusGens[$node_num-1]
			);
			$nodes[$node_num] = $node_data;
		}

		
		$links = array();
		foreach (range(1, 54) as $node_num) {
			$link_data = array("lastResponse" => $lastResponse[$node_num-1], 
					           "deception" => getDefault($deception[$node_num-1], 0)
			);
			$links[$node_num] = $link_data;
		}
		
		//print_r($nodes);
		//print_r($links);
		
		$formatted_result = array(
				    'created' => $created,
					'nodes' => $nodes,
					'links' => $links
		);
		
		echo json_encode($formatted_result);
	  	
	}catch (MongoConnectionException $e) {
		die("Error connecting to MongoDB server: ". $dbHost . ":" . $dbPort);
	}catch (MongoException $e) {
	 	die('Error: ' . $e->getMessage());
	}
	
?>
