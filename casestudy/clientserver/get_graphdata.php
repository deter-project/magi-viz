<?php
if(isset($_GET['ST']) && isset($_GET['ET']))
{
    $uid = $_GET['ST'];
    $uid1 = $_GET['ET'];
}
try
 {
    //Array for JSON data
    $data = array();

    //Creation of connection string for database
	header("Content-type: application/json");
    $conn = new MongoClient( "mongodb://localhost");
    $db = $conn->magi_cs;
    $collection = $db->experiment_data;

    //Get the first time stamp
    $first = $collection->find(array("type" => "pktcounter","host" => "servernode","port" =>"out-clientnode"))->sort(array("created" => 1))->limit(1);
    $array = iterator_to_array($first);
    //print_r($array);
    $index = array_keys($array)[0];
    $ts_first = $array[$index]["created"];
    //echo $ts_first."\n";  

    //Calulate the new time stamp
    $new_ts_1 = $ts_first + (float)$uid;
    $new_ts = $ts_first + (float)$uid1;
    //echo $new_ts."\n"; 
    //echo $new_ts_1."\n";  
    
    $cursor = $collection->find(array("type" => "pktcounter","host" => "servernode","port" =>"out-clientnode","created" =>array('$gt' => $new_ts_1,'$lt' => $new_ts)))->sort(array("created" => 1));
    //$cursor = $collection->find(array("type" => "pktcounter","created" =>array('$lt' => $new_ts)))->sort(array("created" => 1));
    $array1 = iterator_to_array($cursor);
    //print_r($array1);

    foreach ( $cursor as $id => $value )
    {
        $diff = round($value["created"] - $ts_first,8);
        $bytes = ($value["bytes"] * 8)/(1000000) ;
        //$bytes = $value["bytes"];
        $ret = array($diff,$bytes);
        array_push($data, $ret);
    }   
    echo json_encode($data);
    $conn->close();
 }
 catch (MongoConnectionException $e) 
 {
   die('Error connecting to MongoDB server');
 } 
 catch (MongoException $e) 
 {
     die('Error: ' . $e->getMessage());
 }

?>
