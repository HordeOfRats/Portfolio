<?php
    $username = "team002";
	$password = "AhHGwGCUC0";
	$server = "localhost";
	$dbname = "team002";
    $conn = mysqli_connect($server, $username, $password, $dbname);
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
                $allDataArray[] = array($row[0], $row[1], $row[2], $row[3]);
            }
        }
        else
        {
            $allDataArray[] = "Incorrect";
        }
        return $allDataArray;
    }

    $allData = query("SELECT * FROM Credentials", $conn);

    echo json_encode($allData);
?>