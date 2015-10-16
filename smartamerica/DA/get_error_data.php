<?php 
    if(isset($_GET['ID']))
    {
        $uid = $_GET['ID'];
    }
    
    try {

        $data = array();
        header("Content-type: application/json");
        
        // open connection to MongoDB server
        $conn = new Mongo('localhost');
  
        // access database
        $db = $conn->magi;
        $collection = $db->pronyerrors_a;
        
        $first = $collection->find(array("itr" => 22), 
                                   array("error" => 1, "created" => 1)
                                  )->limit(1);
        $array = iterator_to_array($first);
  
        foreach ($array as $value) {
            $first_ts = $value[created];
        }
  
        $newts = (float)$uid + $first_ts;
        $cursor = $collection->find(array("created" => array('$gt' => $newts,
                                                             '$lt' => $newts + 5
                                                            )
                                         ), 
                                    array("error" => 1, 
                                          "created" => 1
                                         )
                                   )->limit(500);
        //var_dump($cursor);

        foreach ( $cursor as $id => $value )
        {
            $diff = round($value["created"] - $first_ts,8);
            $error = round($value["error"],6);
            $ret = array($diff,$error);
            array_push($data, $ret);
        }

        echo json_encode($data);
        $conn->close();

    } catch (MongoConnectionException $e) {
      die('Error connecting to MongoDB server');
    } catch (MongoException $e) {
      die('Error: ' . $e->getMessage());
    }
?>