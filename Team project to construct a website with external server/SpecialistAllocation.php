<?php include "sql-login.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Specialist | Make-It-All</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="helpdesk-style.css">

    <!-- This displays the icon in the tab. -->
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">
</head>
<body>
    <div class = "main">
        <div class = "content">
            <h1>Assign a Specialist</h1>
            <hr>

            <!-- Holds all specialist info -->
            <form id="assignSpecialist" action="" method="post">
            <div class = "table-con">
                <table id="myTable">
                    <tr>
                        <th>Specialist ID</th>
                        <th>Specialist Name</th>
                        <th>Telephone Number</th>
                        <th>Specialities</th>
                        <th>Currently Assigned Jobs</th>
                        <th>Assign to Problem?</th>
                    </tr>

                <?php
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

                            echo"<tr>
                                <td><label>".$SpecialistID."</label></td>
                                <td><label>".$SpecialistName."</label></td>
                                <td><label>".$TelephoneNumber."</label></td>
                                <td><label>".$Specialities."</label></td>
                                <td><label>".$CurrentJobs."</label></td>
                                <td style='text-align:center;'><input type='radio' name='assign' value=".strval($SpecialistID)."></td>
                                </tr>";
                            $Specialities = "";
                        }

                        $id = $SpecialistID;
                        
                    }

                ?>

                </table>

                <!-- You can style this button a bit if you want Harry xx. -->
                <div style="text-align: left;">
                    <button type="submit" name="submit" id="submit">Assign Selected Specialist</button>
                </div>
            </div>
        </form>
        <button onclick="location.reload()"type="reload" name="reload" id="reload">Refresh Table</button>

        <?php
            if (isset($_REQUEST['submit'])) 
            {
                if (empty($_POST['assign'])) 
                {
                    echo '<script>alert("Please select a Specialist.")</script>';
                }
                else
                {
                    $SelectedSpecialistID = intval($_POST['assign']);

                    //Get current problem ID:
                    $sql = "SELECT * FROM ProblemLog ORDER BY ProblemID DESC;";
                    $result = mysqli_query($conn,$sql);
                    $ProblemRow = mysqli_fetch_array($result);
                    $currentProblemID = $ProblemRow["ProblemID"];

                    $sql = "SELECT * FROM ProblemLog WHERE ProblemID = $currentProblemID;";
                    $result = mysqli_query($conn,$sql);
                    while ($ProblemRow = mysqli_fetch_array($result)) 
                    {
                        $sql2 = "UPDATE ProblemLog SET CurrentAssignedSpecialist = $SelectedSpecialistID WHERE ProblemID = $currentProblemID;";
                        if (mysqli_query($conn,$sql2)) 
                        {
                            echo '<script>alert("Specialist ID '.$SelectedSpecialistID.' Successfully assigned to Problem ID '.$currentProblemID.'")</script>';
                            echo '<script type="text/javascript">',
                                'parent.LoadLogProblem();',
                                '</script>';
                        }
                        else
                        {
                            echo '<script>alert("There was an error assigning the specialist.")</script>';
                        }
                    }
                }
            }
        ?>

        </div>
	<div style='color: rgb(103, 103, 103); margin-left:10px;'>
	<hr />
      Helpdesk telephone number: 07471 915391 <br>
      Helpdesk email address: helpdesk@Make-It-All.co.uk <br>
      Building location: 24 Royce Road
     </div>
    </div>

</body>
</html> 