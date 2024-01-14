<?php

/*$problemTable = 
[[126, 76, "Tim Button", 3, "14:26", "21/10/2021",
"aJtlx23Al", "Embedded", "", "Printer not printing.",
"Caller stated the printer was not printing their work. I advised they turn it on and off, 
but it didn't work, so I assigned them to our hardware specialist: Bert",
"", "", "", ""],
[81, 76, "Tim Button", 3, "17:43", "12/03/2021",
"", "Windows", "Microsoft Excel", "Excel keeps crashing.",
"Microsoft Excel keeps crashing on startup and closing straight away. Turning the computer
on and off didn't work. Assigned to specialist: Clara",
"12/03/2021", "19:30", "Reinstalled affected software",
"Clara (ID: 21211)"],
[93, 76, "Tim Button", 2, "11:12", "19/04/2021",
"tfWoWn12", "", "", "Computer not turning on",
"Caller said the computer was not turning on. I suggested he turn it on at the plug,
which solved the problem.", "19/04/2021", "11:15",
"Plug switched on", "Sarah (ID: 12314)"]];
*/



// Database login details
$server = "localhost";
$username = "team002";
$password = "AhHGwGCUC0";
$dbname = "team002";
$conn = mysqli_connect($server, $username, $password, $dbname);


if (($_REQUEST["aproblemID"])) {
	$problemID = $_REQUEST["aproblemID"];
	$sql = "SELECT * FROM ProblemLog WHERE ProblemID = $problemID";
	$result = mysqli_query($conn, $sql);
	
	if (!$conn) {
		die("Connection failed: ". mysqli_connect_error());
	}
	$rowNum = mysqli_num_rows($result);
	
	$found = False;
    echo "<table>";
	while ($check = mysqli_fetch_array($result)) {
		echo $check[0];
        //echo "<span class=\"focus-bg\"></span><span class=\"focus-border\"></span></div></td>";

		echo "<tr>";
        // labProbIDBox (label for probIDBox) & probIDBox (shows the problem ID)
        echo "<th><label for =\"probIDBox\" id=\"labProbIDBox\">Problem ID</label></th>";
		echo "<td><div class=\"input-con\"><label id=\"probIDBox\" class=\"returnBox\">$check[0]</label>";
        // labCallerIDBox & callerIDBox
        echo "<th><label for =\"callerIDBox\" id=\"labCallerIDBox\">Caller ID</label></th>";
		echo "<td><div class=\"input-con\"><label id = \"callerIDBox\" class = \"returnBox\">$check[1]</label>";
		echo "</div></td>";
		echo "</tr>";
		
        echo "<tr>";
        
        // labOpIDBox & opIDBox
        echo "<th><label for =\"opIDBox\" id=\"labOpIDBox\">Helpdesk Operator ID</label></th>";
        echo "<td><div class=\"input-con\"><label id = \"opIDBox\" class = \"returnBox\" >$check[2]</label>";
        echo "</div></td>";
        echo "</tr>";
        
        echo "<tr>";
        // labTimeOfCallBox & timeOfCallBox
        echo "<th><label for =\"timeOfCallBox\" id=\"labTimeOfCallBox\">Call Duration: </label></th>";
        echo "<td><div class=\"input-con\"><label id = \"timeOfCallBox\" class = \"returnBox\" >$check[3]</lab>";
        echo "</div></td>";
        // labDateOfCallBox & dateOfCallBox
        /*echo "<th><label for =\"dateOfCallBox\" id=\"labDateOfCallBox\">Original Date of Call</label></th>";
        echo "<td><div class=\"input-con\"><label id = \"dateOfCallBox\" class = \"returnBox\" >$check[5]</label>";
        echo "</div></td>";
        echo "</tr>";*/
        
        echo "<tr>";
		// labHardSerialBox & hardSerialBox
		echo "<th><label for =\"hardSerialBox\" id=\"labHardSerialBox\">Hardware Serial Number</label></th>";
		echo "<td><div class=\"input-con\"><label id = \"hardSerialBox\" class = \"returnBox\" >$check[4]</label>";
		echo "</div></td>";
		// labOpSysBox & opSysBox
		echo "<th><label for =\"opSysBox\" id=\"labOpSysBox\">Operating System ID: </label></th>";
		echo "<td><div class=\"input-con\"><label id = \"opSysBox\" class = \"returnBox\" >$check[5]</label>";
		echo "</div></td>";
        echo "</tr>";

        echo "<tr>";
        // labSoftwareBox & softwareBox
        echo "<th><label for =\"softwareBox\" id=\"labSoftwareBox\">Software ID: </label></th>";
        echo "<td><div class=\"input-con\"><label id = \"softwareBox\" class = \"returnBox\" >$check[6]</label>";
        echo "</div></td>";
        // labCallReasonBox & callReasonBox
        echo "<th><label for =\"callReasonBox\" id=\"labCallReasonBox\">Reason for Call</label></th>";
        echo "<td><div class=\"input-con\"><label id = \"callReasonBox\" class = \"returnBox\" >$check[7]</label>";
        echo "</div></td>";
        echo "</tr>";

        echo "<tr>";
        // labOpNotesBox & opNotesBox
        echo "<th><label for =\"opNotesBox\" id=\"labOpNotesBox\">Operator Notes</label></th>";
        echo "<td><div class=\"input-con\"><textarea type=\"text\" style=\"height:60px\" id = \"opNotesBox\" class = \"returnBox\">$check[8]</textarea>";
        echo "</div></td>";
        // labDateResolvedBox & dateResolvedBox
        echo "<th><label for =\"dateResolvedBox\" id=\"labDateResolvedBox\">Date Resolved</label></th>";
        echo "<td><div class=\"input-con\"><textarea type=\"text\" id = \"dateResolvedBox\" class = \"returnBox\">$check[11]</textarea>";
        echo "</div></td>";
        echo "</tr>";

        echo "<tr>";
        // labTimeResolvedBox & timeResolvedBox
        echo "<th><label for =\"timeResolvedBox\" id=\"labTimeResolvedBox\">Time Resolved</label></th>";
        echo "<td><div class=\"input-con\"><textarea type=\"text\" id = \"timeResolvedBox\" class = \"returnBox\">$check[12]</textarea>";
        echo "</div></td>";
        // labResolutionBox & resolutionBox
        echo "<th><label for =\"resolutionBox\" id=\"labResolutionBox\">Resolution Provided</label></th>";
        echo "<td><div class=\"input-con\"><textarea type=\"text\" style=\"height:60px\" id = \"resolutionBox\" class = \"returnBox\">$check[13]</textarea>";
        echo "</div></td>";
        echo "</tr>";

        
        echo "<tr>";
        // labResProviderBox & resProviderBox
        echo "<th><label for =\"resProviderBox\" id=\"labResProviderBox\">Resolution Provider</label></th>";
        echo "<td><div class=\"input-con\"><textarea type=\"text\" id = \"resProviderBox\" class = \"returnBox\">$check[14]</textarea>";
        echo "</div></td>";
        echo "<th><label></label></th>";
        echo "<td><button type=\"button\" id=\"updateProblemButton\" onclick=\"<script>alert(document.getElementByID('probIDBox').val);</script>\">Update Problem</button></td>";
		/*echo "<td><button type=\"button\" id=\"updateProblemButton\" onclick=
		updateSQL(
		$conn,
		$_REQUEST['probIDBox'],
		)
		>Update Problem</button></td>";*/
        echo "</tr>";
        

		$found = True;
		break;
        echo "</table>";
	}
	if ($found == False) {
		echo "<p> No results found. </p>";
	}
	mysqli_close($conn);
}

else if (($_REQUEST["acallerName"])) {
	$callerName = $_REQUEST["acallerName"];
    $found2 = false;
	
	echo 
	"<div class = \"table-con\">
	<table id = \"suggestionTable\">
	<tr>
	<th>Caller Name</th>
	<th>Call Reason</th>
	<th>Date of Call</th>
	<th>Problem ID</th>
	</tr>";
	
	if (($_REQUEST["adateOfCall"])) {
		$dateOfCall = $_REQUEST["adateOfCall"];
		
		foreach ($problemTable as $check) {
			if ((strtolower($check[2]) == $callerName) && ($check[5] == $dateOfCall)) {
				echo "
				<tr>
				<td>$check[2]</td>
				<td>$check[9]</td>
				<td>$check[5]</td>
				<td>$check[0]</td>
				</tr>";
                $found2 = true;
			}
		}
        if(!$found2)
        {
            echo "<p> No results found. </p>";
        }
	}
    //This originally wasn't in an else, causing dateofCall entires to be disregarded.
	else
    {
        foreach ($problemTable as $check) {
		if (strtolower($check[2]) == $callerName) {
			echo "
			<tr>
			<td>$check[2]</td>
			<td>$check[9]</td>
			<td>$check[5]</td>
			<td>$check[0]</td>
			</tr>";
		}
	}
    }
	echo "</table>
	</div>";
}

function updateSQL($connection, $pID, $cID, $hoID, $cDur,
				$HSN, $osID, $softID, $reason4call, $notes, $pTypeID,
				$priority, $dateResolved, $timeResolved, $pRes,
				$resProv) {
	$sql = "UPDATE ProblemLog
	(SET ReasonForCall='$reason4call'
	SET OperatorNotes='$notes'
	SET PriorityValue='$priority'
	SET DateResolved='$dateResolved'
	SET TimeResolved='$timeResolved'
	SET ProblemResolution='$pRes'
	SET ResolutionProvider='$resProv'
	)
	WHERE ProblemID = $pID";
	
	if ($connection->query($sql) === TRUE) {
		alert("Problem Updated");
	}
	else {
		alert ("Problem failed to update");
	}
	$conn->close();
	
}
?>