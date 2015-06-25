<?php

require('config.php');

//connect to database
$link  = mysqli_connect($db_host, $db_user, $db_pass, $db_database) or die ('Error when connecting to database');
$time_start = round(microtime(true) * 1000);

if(isset($_GET['height'])){
    //$query = "SELECT y, COUNT(*) AS `count` FROM co_block GROUP BY `y`";
    $query = "SELECT * FROM vertical_data";

    $result = mysqli_query($link, $query);

    $output = array();

    while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $output['data'][$row['y']]['height'] = (int) $row['y']; 
        $output['data'][$row['y']]['value'] = (int) $row['count'];
    }

    $time_end = round(microtime(true) * 1000);
    $output['process'] = $time_end - $time_start;

    mysqli_free_result($result);
    echo json_encode($output);
} else if(isset($_GET['hex'])){
    if(isset($_GET['x'])){
        $x = floor($_GET['x']) * 128;
        $y = floor($_GET['y']) * 128;
        $offset = 40 * 32;

        $xMin = ($x - $offset);
        $xMax = ($x + $offset);

        $yMin = ($y - $offset);
        $yMax = ($y + $offset);

        $query = "SELECT FLOOR(x / 32) AS `x`, FLOOR(z / 32) AS `z`, SUM(count) AS `count` 
                  FROM horizontal_data 
                  WHERE `x` > $xMin AND `x` < $xMax AND `z` > $yMin AND `z` < $yMax
                  GROUP BY FLOOR(x / 32), FLOOR(z / 32) 
                  HAVING SUM(count) > 0";/**/

        $result = mysqli_query($link, $query) or die(mysqli_error($link));

        $output = array();

        $i = 0;
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            //$output['data'][$i]['x'] = (int) $row['x'] ; 
            //$output['data'][$i]['y'] = (int) $row['z']  ; 
            $output['data'][$i]['x'] = (int) $row['x'] - floor($x / 32); 
            $output['data'][$i]['y'] = (int) $row['z'] - floor($y / 32); 
            $output['data'][$i]['value'] = (int) $row['count'];

            $i++;
        }

        $time_end = round(microtime(true) * 1000);
        $output['process'] = $time_end - $time_start;

        mysqli_free_result($result);
        echo json_encode($output);


    } else {
        //$query = "SELECT FLOOR(x / 16) AS `x`, FLOOR(z / 16) AS `z`, COUNT(*) AS `count` FROM co_block WHERE time >= 1393632000 AND time <= 1393718400 GROUP BY FLOOR(x / 16), FLOOR(z / 16) HAVING COUNT(*) > 0 ";
        /*$query = "SELECT FLOOR(x / 128) AS `x`, FLOOR(z / 128) AS `z`, COUNT(*) AS `count` 
                  FROM co_block 
                  GROUP BY FLOOR(x / 128), FLOOR(z / 128) 
                  HAVING COUNT(*) > 0 ";/**/
        /*$query = "SELECT FLOOR(x / 128) AS `x`, FLOOR(z / 128) AS `z`, SUM(count) AS `count` 
                  FROM horizontal_data 
                  GROUP BY FLOOR(x / 128), FLOOR(z / 128) 
                  HAVING SUM(count) > 0 ";/**/
        $query = "SELECT * FROM horizontal_data_128";

        $result = mysqli_query($link, $query) or die(mysqli_error($link));

        $output = array();

        $i = 0;
        while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
            $output['data'][$i]['x'] = (int) $row['x']; 
            $output['data'][$i]['y'] = (int) $row['z']; 
            $output['data'][$i]['value'] = (int) $row['count'];

            $i++;
        }

        $time_end = round(microtime(true) * 1000);
        $output['process'] = $time_end - $time_start;

        mysqli_free_result($result);
        echo json_encode($output);
    }
}


mysqli_close($link);

?>
