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
                $allDataArray[] = $row[0];
            }
        }
        else
        {
            $allDataArray[] = "Incorrect";
        }
        return $allDataArray;
    }

    $allSpecialistIDs = query("SELECT PersonnelID FROM SpecialitiesOfSpecialistsTable", $conn);
    echo json_encode($allSpecialistIDs);

?>