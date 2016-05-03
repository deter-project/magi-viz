<?php 
    require '../../util/helpers.php';

    try {
        header("Content-type: application/json");
        
        $collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
        
        //Get the first time stamp
        $filter = array();
        $tsFirstRecord = getFirstRecordTimestamp($collection, $filter);
        
        //echo $tsFirstRecord;
  
        //Calculate the absolute time stamp
        $lastTimestamp = $tsFirstRecord + (float)$relativeLastTimeStamp;
        
        $filter["created"] = array('$gt' => $lastTimestamp, '$lt' => $lastTimestamp + 5);
        $cursor = $collection->find($filter, 
                                    array("created" => 1, "error" => 1, "_id" => 0)
                                   )->sort(array("created" => 1))->limit($recordsLimit);
                                   
        $result = iterator_to_array($cursor);
        
        $formatted_result = array();
        
        foreach ($result as $record) {
            $record["created"] = round($record["created"] - $tsFirstRecord, 8);
            $record["error"] = round($record["error"], 6);
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