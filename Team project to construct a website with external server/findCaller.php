<?php
include "dbConnect.php";
//Getting all the names and id from a given department
if ($_REQUEST["data"] == "names") {

    $sql = "SELECT FirstName, Surname, PersonnelID FROM PersonnelInfo ".
    "WHERE DepartmentID = ".$_REQUEST["department"].";";
    
    $result = $conn->query($sql);
    echo '<option disabled selected value style="display: none;">Select caller</option>';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='".$row["PersonnelID"]."'>";
        echo $row["FirstName"].' '.$row["Surname"].' - '.$row["PersonnelID"];
        echo "</option>";
    }
}

//Cheching if an ID is valid
if ($_REQUEST["data"] == "id") {
    $sql = "SELECT * FROM PersonnelInfo WHERE PersonnelID = ".$_REQUEST["id"].";";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "true";
    } else {
        echo "false";
    }
}
?>