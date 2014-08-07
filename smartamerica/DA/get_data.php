<?php 
 //echo '<p>Hello World</p>';
if(isset($_GET['ID']))
{
$uid = $_GET['ID'];
//echo $uid;
}
//$copy = (int) $uid;
 try {

  $data = array();
  header("Content-type: application/json");
  // open connection to MongoDB server
  $conn = new Mongo('localhost');
  
  // access database
  $db = $conn->magi;
  $collection = $db->pronyerrors_a;
  //$query = array('error' => 1);
 $first = $collection->find(array("itr" => 22), array("error" => 1, "created" => 1))->limit(1);
 //$cursor = $collection->find(array("itr" => array('$gt' => (int)$uid, '$lte' => (int)$uid + 100)), array("error" => 1, "created" => 1))->limit(100);
 

  //var_dump($first);
  $array = iterator_to_array($first);
  //print_r($array);
  //echo $array[created];
  foreach ($array as $value) {
     //echo "<b><i>" . $value[created] . "</i></b>";
    //echo $value;
    $first_ts = $value[created];
  }
  $newts = (float)$uid + $first_ts;
  //echo $newts;
  $cursor = $collection->find(array("created" => array('$gt' => $newts,'$lt' => $newts + 1)), array("error" => 1, "created" => 1))->limit(500);
  //var_dump($cursor);

foreach ( $cursor as $id => $value )
{
    //echo "$id: ";
    //echo $value["created"],$value["error"];
    //$x = time() * 1000;
    $diff = round($value["created"] - $first_ts,8);
    //echo $diff;
    $error = round($value["error"],6);
    $ret = array($diff,$error);
    //$ret = array($copy,$error);
    //echo json_encode($ret);
    array_push($data, $ret);
    //$copy++;
}

 echo json_encode($data);
  $conn->close();

} catch (MongoConnectionException $e) {
  die('Error connecting to MongoDB server');
} catch (MongoException $e) {
  die('Error: ' . $e->getMessage());
}
?>


