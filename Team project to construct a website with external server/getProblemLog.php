<?php

    $username = "team002";
	$password = "AhHGwGCUC0";
	$server = "localhost";
	$dbname = "team002";
    $conn = mysqli_connect($server, $username, $password, $dbname);

    $tableToReturn;

    if (!$conn) 
    {
        die("Connection failed: " . mysqli_connect_error());
    }

    function query($statement, $connector)
    {
        $result = mysqli_query($connector, $statement);
        $allDataArray = array();
        if(mysqli_num_rows($result) > 0)
        {
            while($row = mysqli_fetch_array($result))
            {
                $allDataArray[] = array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12]);
            }
        }
        else
        {
            $allDataArray[] = "Incorrect";
        }
        return $allDataArray;
    }

    $allProblemLogs = query("SELECT * FROM ProblemLog", $conn);
    //Returns a HTML table:
    $stringToReturn = "";
    $numRows = 0;
    foreach ($allProblemLogs as $key => $value) 
    {
        $numRows++;
    }

    echo "<table><tr><th><td>what are u sayin</td></th></tr></table>";
    // for ($i=0; $i < $numRows; $i++) 
    // { 
        
    // }

    //echo $stringToReturn;

?>