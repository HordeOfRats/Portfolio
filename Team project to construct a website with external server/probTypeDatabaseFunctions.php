<?php 
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



                //update the table
                if ($jsCommand == "update"){
                    $nameFilterFromJS = "\"".$_POST['nameFilter']."\"";
                    $IDFilterFromJS = "\"".$_POST['idFilter']."\"";
                    //generate sql according to filters
                    $sql = "SELECT ProblemType, ProblemTypeID, ParentProblemTypeID FROM `ProblemTypes` WHERE ProblemType LIKE $nameFilterFromJS AND ProblemTypeID LIKE $IDFilterFromJS";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $tableData = "<table id='DBTable'>";
                        $tableData .= "<tr>";
                        $tableData .= "<th>";
                        $tableData .= "Problem Type ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Problem Type";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Parent Problem Type";
                        $tableData .= "</th>";
                        $tableData .= "</tr>";

                        //add identifiers and data to table
                        $rowCount = 1;
                        while($row = $result->fetch_assoc()) {
                            $tempID = "$row[ProblemTypeID]";
                            $tempName = "$row[ProblemType]";
                            $tempParent = "$row[ParentProblemTypeID]";
                            $tableData .= "<tr>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowPTID'.strval($rowCount)." value="."'".$tempID."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowPT'.strval($rowCount)." value="."'".$tempName."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowPPTID'.strval($rowCount)." value="."'".$tempParent."'"."readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .="<td>";
                            $tableData .= "<button onclick=selectRow("."'".strval($rowCount)."'".")>select</button>";
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

                //edit the name of a problem type
                elseif($jsCommand == "edit"){
                    $editingIDFromJS = $_POST['id'];
                    $nameChangeFromJS = $_POST['newName'];
                    $sql = "UPDATE ProblemTypes SET ProblemType='$nameChangeFromJS' WHERE ProblemTypeID = '$editingIDFromJS'" ;
                    if ($conn->query($sql) === TRUE) {
                        echo "Record updated successfully";
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }


                }

                //get rows in database
                elseif($jsCommand == "getRows"){
                    $sql = "SELECT * FROM `ProblemTypes`";
                    $result = $conn->query($sql);
                    $row = mysqli_num_rows($result);
                    echo $row;




                }

                //check if the new name is a duplicate
                elseif($jsCommand == "checkDupe"){
                    $nameToCheck = $_POST['newName'];
                    $sql = "SELECT ProblemType FROM `ProblemTypes` WHERE ProblemType = "."'".$nameToCheck."'";
                    //echo $sql;
                    $result = $conn->query($sql);
                    if($result->num_rows == 0) {
                        echo "false";
                   } else {
                       echo "true";
                   }


                }




                

                
?>