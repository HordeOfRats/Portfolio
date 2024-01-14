<?php
include "sql-login.php";
session_start();


$OperatorNotes = '"' . $_POST['operatorNotes'] . '"';
$DateResolved = "'" . $_POST['dateResolved'] . "'";
$TimeResolved = "'" . $_POST['timeResolved'] . "'";
$ProblemResolution = '"' .  $_POST['problemResolution'] . '"';
$ResolutionProvider = $_POST['resolutionProvider'];
$ProblemID = $_POST['problemID'];
$userID = $_SESSION['personnelID'];
$currentDate = date("Y/m/d");
$currentTime = date("H:i:s");


// EDIT LOG CHANGES HERE IN THIS SECTION
$editSQL = "SELECT * FROM ProblemLog WHERE ProblemID = $ProblemID";
$result2 = mysqli_query($conn, $editSQL);
$editLogRow = mysqli_fetch_array($result2);

foreach ($editLogRow as $value) {
	if (empty($value)) {
		$value = "";
	}
}

$editEntry = "";

// Showing what has been changed in the update
if ($editLogRow['DateResolved'] != $DateResolved) {
$editEntry = $editEntry . "Date Resolved changed from: ". $editLogRow['DateResolved'] . "\n to: " . $DateResolved . "\n";
}
if ($editLogRow['TimeResolved'] != $TimeResolved) {
	$editEntry = $editEntry . "Time Resolved changed from: ". $editLogRow['TimeResolved'] . "\n to: " . $TimeResolved . "\n";
}
if ($editLogRow['ResolutionProvider'] != $ResolutionProvider) {
	$editEntry = $editEntry . "Resolution Provider changed from: " . $editLogRow['ResolutionProvider'] . "\n to: " . $ResolutionProvider . "\n";
}
if ($editLogRow['ProblemResolution'] != $ProblemResolution) {
	$editEntry = $editEntry . "Problem Resolution changed from: ". $editLogRow['ProblemResolution'] . "\n to: " . str_replace('"', "", $ProblemResolution) . "\n";
}
if ($editLogRow['OperatorNotes'] != $OperatorNotes) {
	$editEntry = $editEntry . "Operator Notes changed from: " . $editLogRow['OperatorNotes'] . "\n to: " . str_replace('"', "", $OperatorNotes) . "\n";
}



// UPDATING THE PROBLEM LOG
$sql = "UPDATE ProblemLog SET OperatorNotes=$OperatorNotes, DateResolved=$DateResolved, TimeResolved=$TimeResolved,ProblemResolution=$ProblemResolution,ResolutionProvider=$ResolutionProvider WHERE ProblemID=$ProblemID";
//$sql = "INSERT INTO ProblemLog (OperatorNotes, DateResolved, TimeResolved, ProblemResolution, ResolutionProvider) VALUES ($OperatorNotes,$DateResolved,$TimeResolved,$ProblemResolution,$ResolutionProvider);";

if (mysqli_query($conn,$sql)) {
	echo '<script>alert("Record updated successfully")</script>';
	if ($_SESSION["callID"]) {
		$linkSql = "INSERT INTO ProblemCall VALUES (".$_SESSION["callID"].", $ProblemID, 0);";
		$conn->query($linkSql);
	}
} else {
    echo "Error updating record: " . $sql . "<br>" . $conn->error;
}

$sql = "SELECT * FROM PersonnelInfo WHERE PersonnelID = $userID";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);


// Checks whether the use is a specialist or a helpdesk operator
$sql = "INSERT INTO ProblemEditLog (ProblemID, PersonnelID, Date, Time, Edit) VALUES ($ProblemID, $userID, \"$currentDate\", \"$currentTime\", \"$editEntry\");";


// UPDATING EDITLOG
if (mysqli_query($conn,$sql)) {
    echo '<script>alert("Record updated successfully")</script>';
} else {
    echo "Error updating record: " . $sql . "<br>" . $conn->error;
}

?>