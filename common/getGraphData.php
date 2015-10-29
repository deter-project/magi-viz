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
		
		try{
            $config = yaml_parse_file($graphConfigFile);
            //print_r($config);
        } catch(Exception $e) {
            throw new Exception('Error parsing config file: '.$e->getMessage());
        }
        		
        if (!is_array($config)) {
            throw new Exception('Invalid Config: '.$config);
        }
            
        if (!array_key_exists('db', $config)) {
            throw new Exception('No DB Config: '.json_encode($config));
        }
        
		$db_config = $config['db'];
		
		if (!is_array($db_config)) {
            throw new Exception('Invalid DB Config: '.$db_config);
        }
		
		if (array_key_exists('filter', $db_config)) {
		    $filter = $db_config['filter'];
		    if (!is_array($filter)) {
		        $filter = array();
		    }
		} else {
		    $filter = array();
		}
		
		$filter['agent'] = $db_config['agent'];
		
		//print_r($filter);
		
	    $collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
	    
	    //Get the first time stamp
	    $tsFirstRecord = getFirstRecordTimestamp($collection, $filter);
	    //print_r($tsFirstRecord);
	
	    //Calulate the new time stamp
	    $lastTimestamp = $tsFirstRecord + (float)$relativeLastTimeStamp;
	    
	    $filter["created"] = array('$gt' => $lastTimestamp);
	    $cursor = $collection->find($filter)->sort(array("created" => 1))->limit($recordsLimit);
	    $records = iterator_to_array($cursor);
	    //print_r($records);
	
	    //Array for JSON data
	    $formatted_result = array();
	    
	    $yColumn = $db_config['yValue'];
	    
	    foreach ( $cursor as $id => $value ) {
	        $relativeCreatedTime = round(($value["created"] - $tsFirstRecord), 8);
	        $yValue = $value[$yColumn];
	        $record = array($relativeCreatedTime, $yValue);
	        array_push($formatted_result, $record);
	    }   
	    
	    echo json_encode($formatted_result);
	    
	}catch (MongoConnectionException $e) {
	    http_response_code(500);
		die("Error connecting to MongoDB server: ". $dbHost . ":" . $dbPort);
	}catch (Exception $e) {
	    http_response_code(500);
	 	die('Error: ' . $e->getMessage());
	}
	
?>
