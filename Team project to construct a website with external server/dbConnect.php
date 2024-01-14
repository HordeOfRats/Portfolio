<?php
    $server = "localhost";
    $username = "team002";
    $password = "AhHGwGCUC0";
    $dbname = "team002";
    $conn = mysqli_connect($server,$username,$password,$dbname);
    
    //Checks the connection to the database.
    if (!$conn) {
        //The die() function prints a message and then terminates the current script.
        die("Connection failed: " . mysqli_connect_error());
    }
?>