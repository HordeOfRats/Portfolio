
<?php           
                session_start();
                $loggedID = $_SESSION["personnelID"];
                $dbservername = "localhost";
                $dbusername = "team002";
                $dbpassword = "AhHGwGCUC0";

                $jsCommand = $_POST['command'];



                // Create connection
                $conn = new mysqli($dbservername, $dbusername, $dbpassword);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                else{
                    //echo "Connected successfully";
                }

                mysqli_select_db($conn, 'team002' );



                //update the problem log table
                if ($jsCommand == "update"){
                    $IDFilterFromJS = $_POST['idFilter'];
                    //echo $IDFilterFromJS;
                    //select the correct sql for the filter
                    if ($IDFilterFromJS == "all"){
                        $sql = "SELECT ProblemID, HardwareSerialNumber, OperatingSystemID, SoftwareID, CallerIssue, OperatorNotes, ProblemTypeID, PriorityValue, CurrentAssignedSpecialist FROM `ProblemLog` WHERE DateResolved LIKE '0000-00-00'";
                    }
                    else if ($IDFilterFromJS == "null"){
                        $sql = "SELECT ProblemID, HardwareSerialNumber, OperatingSystemID, SoftwareID, CallerIssue, OperatorNotes, ProblemTypeID, PriorityValue, CurrentAssignedSpecialist FROM `ProblemLog` WHERE (CurrentAssignedSpecialist IS NULL) AND DateResolved LIKE '0000-00-00'";
                    }
                    else{
                        $IDFilterFromJS = "\"".$IDFilterFromJS."\"";
                        $sql = "SELECT ProblemID, HardwareSerialNumber, OperatingSystemID, SoftwareID, CallerIssue, OperatorNotes, ProblemTypeID, PriorityValue, CurrentAssignedSpecialist FROM `ProblemLog` WHERE CurrentAssignedSpecialist LIKE $IDFilterFromJS AND DateResolved LIKE '0000-00-00'";
                    }
                    //echo $sql;
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $tableData = "<table id='DBTable'>";
                        $tableData .= "<tr>";
                        $tableData .= "<th>";
                        $tableData .= "Unassign";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Problem ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Hardware Serial Number";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Operating System ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Software ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Caller Issue";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Operator Notes";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Problem Type ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Priority Value";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Current Assigned Specialist";
                        $tableData .= "</th>";
                        $tableData .= "</tr>";

                        $rowCount = 1;
                        while($row = $result->fetch_assoc()) {
                            $tempPID = "$row[ProblemID]";
                            $tempHSerial = "$row[HardwareSerialNumber]";
                            $tempOSID = "$row[OperatingSystemID]";
                            $tempSID = "$row[SoftwareID]";
                            $tempCallerIssue = "$row[CallerIssue]";
                            $tempOpNotes = "$row[OperatorNotes]";
                            $tempPTID = "$row[ProblemTypeID]";
                            $tempPrio = "$row[PriorityValue]";
                            $tempCAS = "$row[CurrentAssignedSpecialist]";
                            $tableData .= "<tr>";
                            if ($loggedID == $tempCAS){
                                $tableData .= "<td>";
                                $tableData .= "<input type='button' value='Unassign' onclick='unassign(".$tempPID.")'>";
                                $tableData .= "</td>";
                            }
                            else{
                                $tableData .= "<td>";
                                $tableData .= "<input type='text' value='Cannot unassign' readonly width=30>";
                                $tableData .= "</td>";
                            }
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempPID."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempHSerial."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempOSID."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempSID."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempCallerIssue."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempOpNotes."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempPTID."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempPrio."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' value="."'".$tempCAS."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "</tr>";
                            $rowCount += 1;
                        }
                        $tableData .= "</table>";
                        echo $tableData;
                    }
                    else{
                        echo"no data";
                    }
                }

                //get number of rows in database table
                elseif($jsCommand == "getRows"){
                    $sql = "SELECT * FROM `ProblemLog`";
                    $result = $conn->query($sql);
                    $row = mysqli_num_rows($result);
                    echo $row;




                }

                //get the current logged user id
                elseif($jsCommand == "getUserID"){
                    trim($loggedID);
                    echo ($loggedID);
                }
                //unassign the currently logged specialist from a job
                elseif($jsCommand == "unassign"){
                    $probToUnass = $_POST['probIDToUnass'];
                    $sql = "UPDATE `ProblemLog` SET CurrentAssignedSpecialist=NULL WHERE ProblemID = '$probToUnass'" ;
                    if ($conn->query($sql) === TRUE) {
                        echo "Successfully unassigned specialist. ";
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }

                    $sql = "SELECT MAX(EditID) FROM `ProblemEditLog`";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    $maxEditID = $row['MAX(EditID)'];
                    $editID = intval($maxEditID) + 1;

                    //get the date and time
                    $currentDate = date("Y/m/d");
                    $currentTime = date("H:i:s");
                    $editEntry = $loggedID." unassigned from job ".$probToUnass;
                    //generate sql
                    $sql = "INSERT INTO ProblemEditLog (ProblemID, PersonnelID, Date, Time, Edit) VALUES ($probToUnass, $loggedID, \"$currentDate\", \"$currentTime\", \"$editEntry\");";


                    // UPDATING EDITLOG
                    if (mysqli_query($conn,$sql)) {
                        echo "Edit log updated successfully";
                    } 
                    else {
                        echo "Error updating record: " . $sql . "<br>" . $conn->error;
                    }
                }




                

                
?>