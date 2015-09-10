<?php 
 	//echo '<p>Hello World</p>';
	if(isset($_GET['last_timestamp'])) {
		$last_timestamp = $_GET['last_timestamp'];
		//echo $uid;
	}
	
	try {
		$data = array();
	  	header("Content-type: application/json");
	  	
	  	$conn = new Mongo('localhost');
	  	$db = $conn->magi;
	  	$collection = $db->heatmap;
	
	  	$cursor = $collection->find()->sort(array("created" => -1))->limit(1);;
		$result = iterator_to_array($cursor);
		
		echo json_encode($result[array_keys($result)[0]]['data']);
		
	  	$conn->close();
	  	
	} catch (MongoConnectionException $e){
		die('Error connecting to MongoDB server');
	} catch (MongoException $e){
		die('Error: ' . $e->getMessage());
	}
?>