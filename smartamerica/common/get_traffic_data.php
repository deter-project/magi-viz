<?php 
    require '../../util/helpers.php';
    
    $port = $_GET['port'];

    try 
    {
        header("Content-type: application/json");
        
        $collection = getCollection($dbHost, $dbPort, $dbName, $collectionName);
        
        //Get the first time stamp
        $filter = array("agent" => "intfsensor_agent",
                        "peerNode" => $port,
                        "trafficDirection" => "out");
        $tsFirstRecord = getFirstRecordTimestamp($collection, $filter);
        
        //Calculate the absolute time stamp
        $lastTimestamp = $tsFirstRecord + (float)$relativeLastTimeStamp;
        
        $filter["created"] = array('$gt' => $lastTimestamp);
        $cursor = $collection->find($filter, 
                                    array("created" => 1, "bytes" => 1)
                                   )->sort(array("created" => 1))->limit($recordsLimit);
        $result = iterator_to_array($cursor);

        $formatted_result = array();
        
        foreach ($result as $record) {
            $record["created"] = round($record["created"] - $tsFirstRecord, 8);
            $record["bytes"] = ($record["bytes"] * 8)/(1000000);
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