<?php include "sql-login.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Update Problem | Make-It-All</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" href="helpdesk-style.css">
<!-- For icons. -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- This displays the icon in the tab. -->
<link rel="shortcut icon" type="image/png" href="img/favicon.png">

<style>

.flex-parent { /* css for parent flexbox */
  display: flex;
  flex-direction: row; /* The flexbox goes from left to right. */
  justify-content: center; /* The boxes are centered horizontally */
  align-items: flex-start; /* Each box starts at the top (vertically). */
  column-gap: 0px; /* The space between each box. */
}

.flex-child { /* css for children flexboxes */
  flex-basis: 50%; /* Sets the width of the boxes */
}

</style>

</head>
<body>

<script>

function ProblemIDFilter() {
    var input, filter, table, tr, td, i, userInput;
    input = document.getElementById("ProblemIDInput");
    filter = input.value.toLowerCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            userInput = td.textContent || td.innerText;
            if (userInput.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function NameFilter() {
    var input, filter, table, tr, td, i, userInput;
    input = document.getElementById("NameInput");
    filter = input.value.toLowerCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
            userInput = td.textContent || td.innerText;
            if (userInput.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function DateFilter() {
    var input, filter, table, tr, td, i, userInput;
    input = document.getElementById("DateInput");
    filter = input.value.toLowerCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
            userInput = td.textContent || td.innerText;
            if (userInput.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

</script>

<div class="main">
    <div class = "flex-parent">
        <div class="flex-child">
            <div class="content">
                <h1>Select Problem</h1>
                <hr>
                <div class="sub-content">
                    <form id="updateProblem" action="" method="post">

                        <div class="form-table">
                            <input type="text" id="ProblemIDInput" onkeyup="ProblemIDFilter()" placeholder="Filter by Problem ID" title="Enter a Problem ID">
                            <input type="text" id="NameInput" onkeyup="NameFilter()" placeholder="Filter by Caller Name" title="Enter a name">
                            <input type="text" id="DateInput" onkeyup="DateFilter()" placeholder="Filter by Original Call Date" title="Enter a date">
                            <table id="myTable">
                                <tr>
                                    <th>Problem ID</th>
                                    <th>Caller Name</th>
                                    <th>Original Call Date</th>
                                    <th></th>
                                </tr>
                        <?php
                        $sql = "SELECT * FROM ProblemLog WHERE DateResolved<=0000-00-00";
                        $result = mysqli_query($conn,$sql);
                        while ($row = mysqli_fetch_array($result)) {
                            $ProblemID = $row["ProblemID"];
                            //I sort by date so the first record in the table is the first call, and I therefore obtain the original call date.
                            $sql2 = "SELECT * FROM CallData 
                            INNER JOIN ProblemCall on CallData.CallID = ProblemCall.CallID 
                            Where ProblemCall.ProblemID = $ProblemID 
                            ORDER BY CallData.CallDate ASC;";
                            //echo "Error: " . $sql2 . "<br>" . $conn->error;
                            $result2 = mysqli_query($conn,$sql2);
                            if (mysqli_num_rows($result2) > 0) {
                                $callRow = mysqli_fetch_array($result2);
                                $Date = strval($callRow["CallDate"]);
                                $callerID = $callRow['CallerID'];
                                $sql3 = "SELECT FirstName,Surname FROM PersonnelInfo WHERE PersonnelID = $callerID;";
                                $result3 = mysqli_query($conn,$sql3);
                                $nameRow = mysqli_fetch_array($result3);
                                $Name = $nameRow['FirstName'] . ' ' . $nameRow['Surname'];
                            } else {
                                $Name = "N/A";
                                $Date = "0000-00-00";
                            }
                            echo"<tr>
                                    <td><label>".strval($ProblemID)."</label></td>
                                    <td><label>".$Name."</label></td>
                                    <td><label>".$Date."</label></td>
                                    <td><input type=\"radio\" name=\"problemID\" value=".strval($ProblemID)."></td>
                                </tr>";
                        }?>
                            </table>
                            <button type="submit" name="submit" id="submit">Select</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="flex-child">
            <div class="content">
                <h1>Update Problem</h1>
                <hr>
                <?php
                if (isset($_REQUEST['submit'])) {
                    if (empty($_POST['problemID'])) {
                        echo '<script>alert("Please select a value.")</script>';
                    } else {
                        $SelectedProblemID = intval($_POST['problemID']);
                        
                        $sql = "SELECT * FROM ProblemLog WHERE ProblemID = $SelectedProblemID";
                        $result = mysqli_query($conn,$sql);
                        while ($ProblemRow = mysqli_fetch_array($result)) {

                            //I sort by date so the first record in the table is the first call, and I therefore obtain the original call date.
                            $sql2 = "SELECT * FROM CallData 
                            INNER JOIN ProblemCall on CallData.CallID = ProblemCall.CallID 
                            Where ProblemCall.ProblemID = $SelectedProblemID 
                            ORDER BY CallData.CallDate DESC;";
                            
                            $result2 = mysqli_query($conn,$sql2);
                            if (mysqli_num_rows($result2) > 0) {
                                $CallRow = mysqli_fetch_array($result2);
                                $Date = strval($CallRow["CallDate"]);
                                $CallerID = $CallRow["CallerID"];
                                $sql3 = "SELECT FirstName,Surname FROM PersonnelInfo WHERE PersonnelID = $CallerID;";
                                $result3 = mysqli_query($conn,$sql3);
                                $nameRow = mysqli_fetch_array($result3);
                                $Name = $nameRow['FirstName'] . ' ' . $nameRow['Surname'];
                            } else {
                                $Name = "N/A";
                                $Date = "0000-00-00";
                            }
                            is_null($ProblemRow["ProblemID"]) ? $ProblemID = "N/A" : $ProblemID = $ProblemRow["ProblemID"];
                            is_null($CallRow["CallerID"]) ? $CallerID = "N/A" : $CallerID = $CallRow["CallerID"];
                            is_null($CallRow["CallTime"]) ? $CallTime = "N/A" : $CallTime = $CallRow["CallTime"];
                            is_null($ProblemRow["CallerIssue"]) ? $CallerIssue = "N/A" : $CallerIssue = $ProblemRow["CallerIssue"];
                            is_null($ProblemRow["HardwareSerialNumber"]) ? $HardwareSerialNumber = "N/A" : $HardwareSerialNumber = $ProblemRow["HardwareSerialNumber"];
                            is_null($ProblemRow["OperatingSystemID"]) ? $OperatingSystemID = "N/A" : $OperatingSystemID = $ProblemRow["OperatingSystemID"];
                            is_null($ProblemRow["SoftwareID"]) ? $SoftwareID = "N/A" : $SoftwareID = $ProblemRow["SoftwareID"];
                            is_null($ProblemRow["OperatorNotes"]) ? $OperatorNotes = "N/A" : $OperatorNotes = $ProblemRow["OperatorNotes"];
                            is_null($_SESSION["personnelID"]) ? $ResolutionProvider = "" : $ResolutionProvider = $_SESSION["personnelID"];
                            $CurrentDate = date('Y-m-d');
                            $CurrentTime = date('H:i:s');
                        }
                        ?>

                        <div id="results">
                            <form action="updateProblemSave.php" method="post">
                                <div>
                                    <table>
                                        <tr>
                                            <th><label for ="probIDBox" id="labProbIDBox">Problem ID</label></th>
                                            <td><?php echo "<label>".$ProblemID."</label>";?></td>
                                            <th><label for ="opIDBox" id="labOpIDBox">Helpdesk Operator ID</label></th>
                                            <td><?php echo "<label>".$CallRow["HelpdeskOperatorID"]."</label>";?></td>
                                        </tr>
                                        <tr>
                                            <th><label for ="callerIDBox" id="labCallerIDBox">Caller ID</label></th>
                                            <td><?php echo "<label>".$CallerID."</label>";?></td>
                                            <th><label for ="callerNameBox" id="labCallerNameBox">Caller Name</label></th>
                                            <td><div class="input-con">
                                                <?php echo "<label>".$Name."</label>";?>
                                            </div></td>
                                        </tr>
                                        <tr>
                                            <th><label for ="callDateBox" id="labCallDateBox">Last Call Date</label></th>
                                            <td><?php echo "<label>".$Date."</label>";?></td>
                                            <!-- labTimeOfCallBox & timeOfCallBox -->
                                            <th><label for ="callTimeBox" id="labCallTimeBox">Last Call Time</label></th>
                                            <td><?php echo "<label>".$CallTime."</label>";?></td>
                                        </tr>   
                                        <tr>
                                            <!-- labHardSerialBox & hardSerialBox -->
                                            <th><label for ="hardSerialBox" id="labHardSerialBox">Hardware Serial Number</label></th>
                                            <td><?php echo "<label>".$HardwareSerialNumber."</label>";?></td>
                                            <!-- labOpSysBox & opSysBox -->
                                            <th><label for ="opSysBox" id="labOpSysBox">Operating System ID</label></th>
                                            <td><?php echo "<label>".$OperatingSystemID."</label>";?></td>
                                        </tr>
                                        <tr>
                                            <!-- labSoftwareBox & softwareBox -->
                                            <th><label for ="softwareBox" id="labSoftwareBox">Software ID</label></th>
                                            <td><?php echo "<label>".$SoftwareID."</label>";?></td>
                                            <!-- labCallReasonBox & callReasonBox -->
                                            <th><label for ="callerIssueBox" id="labCallerIssueBox">Problem</label></th>
                                            <td><?php echo "<label>".$CallerIssue."</label>";?></td>
                                        </tr>
                                        <tr>
                                            <!-- labOpNotesBox & opNotesBox -->
                                            <th><label for ="opNotesBox" id="labOpNotesBox">Operator Notes</label></th>
                                            <td><div class="input-con">
                                                <textarea type="text" style="height:60px" id="operatorNotes" name="operatorNotes" class="returnBox"><?php echo $OperatorNotes;?></textarea>
                                            </div></td>
                                            <!-- labDateResolvedBox & dateResolvedBox -->
                                            <th><label for ="dateResolvedBox" id="labDateResolvedBox">Date Resolved</label></th>
                                            <td><div class="input-con">
                                                <textarea type="text" id="dateResolved" name="dateResolved" class="returnBox"><?php echo $CurrentDate;?></textarea>
                                            </div></td>
                                            </tr>
                                            <tr>
                                            <!-- labTimeResolvedBox & timeResolvedBox -->
                                            <th><label for ="timeResolvedBox" id="labTimeResolvedBox">Time Resolved</label></th>
                                            <td><div class="input-con">
                                                <textarea type="text" id="timeResolvedBox" name="timeResolved" class="returnBox"><?php echo $CurrentTime;?></textarea>
                                            </div></td>
                                            <!-- labResolutionBox & resolutionBox -->
                                            <th><label for ="resolutionBox" id="labResolutionBox">Resolution</label></th>
                                            <td><div class="input-con">
                                                <textarea type="text" style="height:60px" id="problemResolution" name="problemResolution" class="returnBox"></textarea>
                                            </div></td>
                                        </tr>
                                        <tr>
                                            <!-- labResProviderBox & resProviderBox -->
                                            <th><label for ="resProviderBox" id="labResProviderBox">Resolution Provider ID</label></th>
                                            <td><div class="input-con">
                                                <textarea type="text" id="resolutionProvider" name="resolutionProvider" class="returnBox"><?php echo $ResolutionProvider;?></textarea>
                                            </div></td>
                                            <td><div class="input-con">
                                                <input type="hidden" id="problemID" name="problemID" class="returnBox" value="<?php echo $SelectedProblemID;?>">
                                            </div></td>
                                        </tr>
                                        <td><button type="submit" name="submit" id="updateProblemButton">Update</button></td>
                                    </table>
                                </div>
                            </form>
                        </div>
                    
                    <?php
                    }
                }
                ?>

            </div>
        </div>
    </div>
<div style="color: rgb(103, 103, 103); text-align: left; position: absolute; bottom: 5px;">
<hr>
Helpdesk telephone number: 07471 915391 <br>
Helpdesk email address: helpdesk@Make-It-All.co.uk <br>
Building location: 24 Royce Road
</div>
</div>

</body>
</html>