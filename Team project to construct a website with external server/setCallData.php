<?php
    session_start();
    //Setting or clearing the start date of the call
    if ($_REQUEST["data"] == "date") {
        if($_REQUEST["set"] == true) {
            $_SESSION["callStart"] = Date("Y-m-d H:i:s");
            echo $_SESSION["callStart"];
        //Clearing the start date
        } else {
            $_SESSION["callStart"] = "";
        }
    }

    //Saving the initial call data to the database
    if ($_REQUEST["data"] == "id") {
        print("id");
        include "dbConnect.php";
        //Saving call data
        $dateTimeArr = explode(" ", $_SESSION["callStart"]);
        $insert = "INSERT INTO CallData(HelpdeskOperatorID, CallerID, CallDate, CallTime)".
        "VALUES (".$_SESSION["personnelID"].", ".$_REQUEST["id"].", '".
        $dateTimeArr[0]."', '".$dateTimeArr[1]."');";
        //Getting the id of the call
        $getId = "SELECT CallID FROM CallData WHERE HelpdeskOperatorID = ".$_SESSION["personnelID"].
        " ORDER BY CallDate DESC, CallTime DESC;";
        print($insert."\n".$getId);
        print($conn->query($insert));
        $result = $conn->query($getId);
        $first = $result->fetch_assoc();
        $_SESSION["callID"] = $first["CallID"];
    }
    //Ending the call and restting variables
    if ($_REQUEST["data"] == "endCall") {
        include 'dbConnect.php';
        //Saving the last parts of the call data
        $sql = "UPDATE CallData SET Duration = '".$_REQUEST["duration"].
        "', ReasonForCall = \"".$_REQUEST["reason"]."\" WHERE CallID = ".$_SESSION["callID"].";";
        $conn->query($sql);
        //Clearing call data
        $_SESSION["callId"] = "";
        $_SESSION["callStart"] = "";
    }
?>