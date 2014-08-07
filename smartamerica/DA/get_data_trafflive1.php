<?php 
 //echo '<p>Hello World</p>';
if(isset($_GET['ID']))
{
$uid = $_GET['ID'];
//echo $uid;
}
try {

  $data = array();
  header("Content-type: application/json");

  $conn = new Mongo('localhost');
  
  // access database
  $db = $conn->magi;
  $collection = $db->pktcounter_da;

  $first = $collection->find(array("port" => "out-node-0"), array("created" => 1, "bytes" => 1))->sort(array("created" => 1))->limit(1);
  $array = iterator_to_array($first);
 // print_r($array);

  foreach ($array as $value) {
    //echo "<b><i>" . $value[created] . "</i></b>";
    //echo $value;
    $first_ts = $value[created];
  }
  $newts = (float)$uid + $first_ts;
  $cursor = $collection->find(array("created" => array('$gt' => $newts),"port" => "out-node-0"), array("created" => 1, "bytes" => 1))->sort(array("created" => 1))->limit(1);
  $array1 = iterator_to_array($cursor);
  //print_r($array1);

  //var_dump($cursor);

  foreach ( $cursor as $id => $value )
  {
  		//echo $value["created"],$value["bytes"];

  		$diff = round($value["created"] - $first_ts,8);
  		//echo $diff;
  		$bytes = ($value["bytes"] * 8)/(1000000) ;
  		$ret = array($diff,$bytes);

  }
  echo json_encode($ret);
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