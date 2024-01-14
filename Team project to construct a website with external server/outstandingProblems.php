<?php include "sql-login.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outstanding Problems</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="helpdesk-style.css">

    <script>
        let i = 0;
        function ShowAvailableSpecialists()
        {
            if(i == 0)
            {
                document.getElementById("specialistTable").style.display = "block";
                i = 1;
            }
            else
            {
                document.getElementById("specialistTable").style.display = "none";
                i = 0;
            }
        }
    </script>

</head>
<body>
    <div class="main">
        <div class="content">
            <button onclick="ShowAvailableSpecialists()"type="button" style="text-align: left;">Show Available Specialists</button>
            <hr>

            <div class="table-con">
                <table id="specialistTable" style="display:none;">

                    <tr>
                        <th>Specialist ID</th>
                        <th>Specialist Name</th>
                        <th>Telephone Number</th>
                        <th>Specialities</th>
                        <th>Currently Assigned Jobs</th>
                    </tr>

                    <?php
                        $SpecialistIDsArray = array();

                        $sql = "SELECT * FROM SpecialitiesOfSpecialistsTable
                                ORDER BY PersonnelID Asc;";
                        $result = mysqli_query($conn, $sql);

                        $id = 0;
                        while($specialistRow = mysqli_fetch_array($result))
                        {
                            $SpecialistID = strval($specialistRow["PersonnelID"]);
                            
                            if($id != $SpecialistID)
                            {
                                $SpecialistName = "";
                                $TelephoneNumber = "";
                                $sql2 = "SELECT * FROM PersonnelInfo WHERE PersonnelID = $SpecialistID;";
                                $result2 = mysqli_query($conn, $sql2);
                                if(mysqli_num_rows($result2) > 0)
                                {
                                    $personnelInfoRow = mysqli_fetch_array($result2);
                                    $fname = strval($personnelInfoRow["FirstName"]);
                                    $sname = strval($personnelInfoRow["Surname"]);
                                    $SpecialistName = $fname . ' ' . $sname;

                                    $TelephoneNumber = strval($personnelInfoRow["Telephone number"]);
                                }

                                $CurrentJobs = "";

                                //Get specialities of the specialists:
                                $sql3 = "SELECT * FROM SpecialitiesOfSpecialistsTable WHERE PersonnelID = $SpecialistID;";
                                $result3 = mysqli_query($conn, $sql3);
                                $Specialities = "";
                                while($SpecialityRow = mysqli_fetch_array($result3))
                                {
                                    $problemTypeID = intval($SpecialityRow["ProblemTypeID"]);
                                    $sql4 = "SELECT * FROM ProblemTypes WHERE ProblemTypeID = $problemTypeID;";
                                    $result4 = mysqli_query($conn, $sql4);
                                    while($problemTypeIDRow = mysqli_fetch_array($result4))
                                    {
                                        $Specialities .= strval($problemTypeIDRow["ProblemType"]) . ", ";
                                    }
                                }
                                $Specialities = rtrim($Specialities, ", ");

                                //Get currently assigned jobs of the specialists:
                                $sql5 = "SELECT * FROM ProblemLog WHERE CurrentAssignedSpecialist = $SpecialistID;";
                                $result5 = mysqli_query($conn, $sql5);
                                $CurrentJobs = "None";
                                if(mysqli_num_rows($result5) > 0) { $CurrentJobs = ""; }
                                while($currentJobRow = mysqli_fetch_array($result5))
                                {
                                    $CurrentJobs .= intval($currentJobRow["ProblemID"]) . ", ";
                                }
                                $CurrentJobs = rtrim($CurrentJobs, ", ");

                                $SpecialistIDsArray[] = $SpecialistID;

                                echo"<tr>
                                    <td><label>".$SpecialistID."</label></td>
                                    <td><label>".$SpecialistName."</label></td>
                                    <td><label>".$TelephoneNumber."</label></td>
                                    <td><label>".$Specialities."</label></td>
                                    <td><label>".$CurrentJobs."</label></td>
                                    </tr>";

                                $Specialities = "";
                            }
                            $id = $SpecialistID; 
                        }
                    ?>

                </table>
            </div>
            <hr>

            <h1>Outstanding Jobs</h1>
            <form id="outstandingProblems" action="" method="post">
                <div class="table-con">
                    <table id="outstandingProblemsTable">
                        <tr>
                            <th>Problem ID</th>
                            <th>Caller Issue</th>
                            <th>Operator Notes</th>
                            <th>Problem Type ID</th>
                            <th>Priority Value</th>
                            <th>Assign New Specialist</th>
                        </tr>

                        <?php
                            $ProblemIDs = array();

                            $sql = "SELECT * FROM ProblemLog WHERE (CurrentAssignedSpecialist IS NULL) AND (DateResolved = '0000-00-00');";
                            $result = mysqli_query($conn, $sql);

                            $iteration = 0;
                            while($problemRow = mysqli_fetch_array($result))
                            {
                                $ProblemID = strval($problemRow['ProblemID']);
                                $ProblemIDs[] = $ProblemID;
                                $CallerIssue = strval($problemRow['CallerIssue']);
                                $OperatorNotes = strval($problemRow['OperatorNotes']);
                                $ProblemTypeID = strval($problemRow['ProblemTypeID']);
                                $PriorityValue = strval($problemRow['PriorityValue']);

                                echo"<tr>
                                    <td><label>".$ProblemID."</label></td>
                                    <td><label>".$CallerIssue."</label></td>
                                    <td><label>".$OperatorNotes."</label></td>
                                    <td><label>".$ProblemTypeID."</label></td>
                                    <td><label>".$PriorityValue."</label></td>
                                    <td><select name = '$iteration-dd' style='width:500px'>
                                        <option value='$iteration-default' selected disabled hidden>Choose Specialist</option>";

                                    foreach ($SpecialistIDsArray as $key => $value) 
                                    {
                                        echo"<option value='$value'>$value</option>";
                                    }
                                echo"</select></td></tr>";
                                $iteration++;
                            }
                        ?>

                    </table>
                    <div style="text-align: left;">
                        <button type="submit" name="submit" id="submit">Confirm Assignments</button>
                    </div>
                </div>
            </form>
            </div>
            <?php
                if (isset($_POST['submit'])) 
                {
                    for ($i=0; $i < count($ProblemIDs); $i++) 
                    { 
                        $currentProblemID = strval($ProblemIDs[$i]);
                        $choiceString = $i."-dd";
                        if(isset($_POST[$choiceString]))
                        {
                            $currentSpecialistChoice = $_POST[$choiceString];
                            $sql = "UPDATE ProblemLog SET CurrentAssignedSpecialist = $currentSpecialistChoice WHERE ProblemID = $currentProblemID;";
                            if(mysqli_query($conn, $sql))
                            {
                                $sql = "SELECT * FROM PersonnelInfo WHERE PersonnelID = $currentProblemID";
                                $result = mysqli_query($conn, $sql);
                                $row = mysqli_fetch_array($result);
                                $PersonnelID = $_SESSION["personnelID"];
                                $currentDate = date("Y/m/d");
                                $currentTime = date("H:i:s");
                                $editEntry = $currentSpecialistChoice . " assigned to problem.";

                                // Checks whether the use is a specialist or a helpdesk operator
                                $sql2 = "INSERT INTO ProblemEditLog (ProblemID, PersonnelID, Date, Time, Edit) VALUES ($currentProblemID, $PersonnelID, \"$currentDate\", \"$currentTime\", \"$editEntry\");";
                                
                                if (mysqli_query($conn,$sql2)) {
                                    echo '<script>alert("Problems were successfully assigned a specialist.")</script>';
                                    echo '<script type="text/javascript">',
                                    'parent.LoadHome();',
                                    '</script>';
                                } else {
                                    echo "Error logging edit: " . $sql2 . "<br>" . $conn->error;
                                }
                                
                                }
                            else
                            {
                                echo "Error assigning specialist: " . $sql . "<br>" . $conn->error;
                            }
                        }
                    }
                }
            ?>
        </div>
    </div>
</body>
</html>