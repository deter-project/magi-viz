<?php 
 	//echo '<p>Hello World</p>';
	if(isset($_GET['last_timestamp'])) {
		$last_timestamp = $_GET['last_timestamp'];
		//echo $uid;
	}
	
	try {
		$data = array();
	  	header("Content-type: application/json");
	  	
	  	$conn = new Mongo('localhost:27020');
	  	$db = $conn->magi;
	  	$collection = $db->pktcounter;
	
	  	$cursor = $collection->find(array("type" => "pktcounter"))->sort(array("created" => 1))->limit(1);
	  	$result = iterator_to_array($cursor);
	 	$first_ts = $result[array_keys($result)[0]][created];
	 	//print_r($first_ts);
	  	$last_ts = (float)$last_timestamp + $first_ts;
	  	
	  	$cursor = $collection->find(array("type" => "pktcounter", "created" => array('$gt' => $last_ts),"port" => array('$in' => array('out-uc-0', 'out-c-0'))), 
	  													array("created" => 1, "bytes" => 1, "port" => 1, "_id" => 0))
	  													->sort(array("created" => 1));//->limit(2);
		$result = iterator_to_array($cursor);
		
		$formatted_result = array();
		
		foreach ($result as $record){
			$record["created"] = $record["created"] - $first_ts;
			$record["bytes"] = $record["bytes"] / 1000000;
			array_push($formatted_result, $record);
		}
		
		echo json_encode($formatted_result);
	  	$conn->close();
	  	
	} catch (MongoConnectionException $e){
		die('Error connecting to MongoDB server');
	} catch (MongoException $e){
		die('Error: ' . $e->getMessage());
	}
?>
