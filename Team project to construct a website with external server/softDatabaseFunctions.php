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



                //update the software table
                if ($jsCommand == "update"){
                    $nameFilterFromJS = "\"".$_POST['nameFilter']."\"";
                    $IDFilterFromJS = "\"".$_POST['idFilter']."\"";
                    $sql = "SELECT SoftwareName, SoftwareID FROM `SoftwareTypes` WHERE SoftwareName LIKE $nameFilterFromJS AND SoftwareID LIKE $IDFilterFromJS";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $tableData = "<table id='DBTable'>";
                        $tableData .= "<tr>";
                        $tableData .= "<th>";
                        $tableData .= "Software ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Software Name";
                        $tableData .= "</th>";
                        $tableData .= "</tr>";

                        //add data and identifiers to table
                        $rowCount = 1;
                        while($row = $result->fetch_assoc()) {
                            $tempID = "$row[SoftwareID]";
                            $tempName = "$row[SoftwareName]";
                            $tableData .= "<tr>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowSID'.strval($rowCount)." value="."'".$tempID."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowSN'.strval($rowCount)." value="."'".$tempName."'"." readonly width=30>";
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

                //edit the name of a piece of software in the database
                elseif($jsCommand == "edit"){
                    $editingIDFromJS = $_POST['id'];
                    $nameChangeFromJS = $_POST['newName'];
                    $sql = "UPDATE SoftwareTypes SET SoftwareName='$nameChangeFromJS' WHERE SoftwareID = '$editingIDFromJS'" ;
                    if ($conn->query($sql) === TRUE) {
                        echo "Record updated successfully";
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }


                }

                //get rows in table in database
                elseif($jsCommand == "getRows"){
                    $sql = "SELECT * FROM `SoftwareTypes`";
                    $result = $conn->query($sql);
                    $row = mysqli_num_rows($result);
                    echo $row;




                }

                //check if the new software name is a dupe
                elseif($jsCommand == "checkDupe"){
                    $nameToCheck = $_POST['newName'];
                    $sql = "SELECT SoftwareName FROM `SoftwareTypes` WHERE SoftwareName = "."'".$nameToCheck."'";
                    //echo $sql;
                    $result = $conn->query($sql);
                    if($result->num_rows == 0) {
                        echo "false";
                   } else {
                       echo "true";
                   }


                }




                

                
?>