<?php
	
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	
	$dbHost = getDefault('dbHost', 'localhost');
    $dbPort = getDefault('dbPort', '27017');
    $dbName = getDefault('dbName', 'magi');
    $collectionName = getDefault('collectionName', 'experiment_data');
    $graphConfigFile = getDefault('graphConfigFile', 'magi_graph.conf');
    if (startsWith($graphConfigFile, "/")) {
        $graphConfigFile = $root.$graphConfigFile;
    }
	
	$relativeLastTimeStamp = getDefault('lastTimestamp', -1);
    $recordsLimit = getDefault('recordsLimit', 0);
    
	function getCollection($dbHost, $dbPort, $dbName, $collectionName){
		$conn = new MongoClient( "mongodb://" . $dbHost . ":" . $dbPort);
		$db = $conn->$dbName;
		$collection = $db->$collectionName;
		return $collection;
	}
	
	function getFirstRecordTimestamp($collection, $filter) {
		$cursorFirstRecord = $collection->find($filter)->sort(array("created" => 1))->limit(1);
		$resultFirstRecord = iterator_to_array($cursorFirstRecord);
		if(sizeof($resultFirstRecord) == 0){
		    return 0;
		}
		$index = array_keys($resultFirstRecord)[0];
		$tsFirstRecord = $resultFirstRecord[$index]["created"];
		return $tsFirstRecord;
	}
	
	function getDefault($name, $defaultValue) {
	    if(isset($_GET[$name])) {
	        $val = $_GET[$name];
	        if ($val != 'undefined') {
	            return $_GET[$name];
	        }
        }
        return $defaultValue;
	}
	
	function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
    function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

?>