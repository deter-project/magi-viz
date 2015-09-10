<?php

	require '../util/helpers.php';

	try 
	{
	  	header("Content-type: application/json");
	  	
	  	$collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
	  	
	  	//Get the first time stamp
	  	$filter = array("agent" => "topo_agent");
	  	$cursor = $collection->find($filter);
		$result = iterator_to_array($cursor);
		$index = array_keys($result)[0];
		$record = $result[$index];
		
		//print_r($record);
		
		$db_nodes = $record["nodes"];
		$db_edges = $record["edges"];
		
		//$db_nodes = '["Node1", "Node2", "Node3", "Node4", "Node5"]';
		//$db_edges = '[["Node1", "Node2"], 
		//              ["Node1", "Node3"],
		//              ["Node1", "Node4"],
		//              ["Node4", "Node5"] ]';
		
		$db_nodes = json_decode($db_nodes);
		$db_edges = json_decode($db_edges);
		
		$nodes = array();
		foreach ($db_nodes as $node_name){
			$node_data = array("name" => $node_name,
								"group" => "1");
			$nodes[$node_name] = $node_data;
		}
		
		$links = array();
		foreach ($db_edges as $db_edge){
			$link_data = array("source" => $db_edge[0],
			                   "target" => $db_edge[1],
			                   "value" => 2);
			array_push($links, $link_data);
		}
		
		//print_r($nodes);
		//print_r($links);
		
		$formatted_result = array(
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
