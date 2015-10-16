<?php
	
	$dbHost = "localhost";
	$dbPort = "27017";
	$dbName = "magi";
	$collectionName = "experiment_data";
	$relativeLastTimeStamp = -1;
	$recordsLimit = 0;
	
	if(isset($_GET['dbHost']))
	{
		$dbHost = $_GET['dbHost'];
	}
	if(isset($_GET['dbPort']))
	{
		$dbPort = $_GET['dbPort'];
	}
	if(isset($_GET['dbName']))
	{
		$dbName = $_GET['dbName'];
	}
	if(isset($_GET['collectionName']))
    {
        $collectionName = $_GET['collectionName'];
    }
	if(isset($_GET['lastTimestamp']))
	{
		$relativeLastTimeStamp = $_GET['lastTimestamp'];
	}
	if(isset($_GET['recordsLimit']))
	{
		$recordsLimit = $_GET['recordsLimit'];
	}
	
	function getCollection($dbHost, $dbPort, $dbName, $collectionName){
		$conn = new MongoClient( "mongodb://" . $dbHost . ":" . $dbPort);
		$db = $conn->$dbName;
		$collection = $db->$collectionName;
		return $collection;
	}
	
	function getFirstRecordTimestamp($collection, $filter) {
		$cursorFirstRecord = $collection->find($filter)->sort(array("created" => 1))->limit(1);
		$resultFirstRecord = iterator_to_array($cursorFirstRecord);
		$index = array_keys($resultFirstRecord)[0];
		$tsFirstRecord = $resultFirstRecord[$index]["created"];
		return $tsFirstRecord;
	}

?>