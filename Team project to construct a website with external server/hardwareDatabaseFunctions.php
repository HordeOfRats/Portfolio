<?php 
                //echo ("made it here");
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


                //if we need to update the table
                if ($jsCommand == "update"){
                    //get filters
                    $serialFilterFromJS = "\"".$_POST['serialFilter']."\"";
                    //echo $serialFilterFromJS;
                    $typeIDFilterFromJS = "\"".$_POST['idFilter']."\"";
                    $makeFilterFromJS = "\"".$_POST['makeFilter']."\"";
                    $modelFilterFromJS = "\"".$_POST['modFilter']."\"";
                    //generate the sql query
                    $sql = "SELECT SerialNumber, HardwareTypeID, HardwareMake, HardwareModel FROM Hardware WHERE SerialNumber LIKE $serialFilterFromJS AND HardwareTypeID LIKE $typeIDFilterFromJS AND HardwareMake LIKE $makeFilterFromJS AND HardwareModel LIKE $modelFilterFromJS";
                    $result = $conn->query($sql);
                    //generate the table
                    if ($result->num_rows > 0) {
                        $tableData = "<table id='DBTable'>";
                        $tableData .= "<tr>";
                        $tableData .= "<th>";
                        $tableData .= "Serial Number";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Hardware Type ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Hardware Make";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "Hardware Model";
                        $tableData .= "</th>";
                        $tableData .= "</tr>";

                        $rowCount = 1;
                        //add identifiers and data to each row of table
                        while($row = $result->fetch_assoc()) {
                            $tempSerial = "$row[SerialNumber]";
                            $tempID = "$row[HardwareTypeID]";
                            $tempMake = "$row[HardwareMake]";
                            $tempModel = "$row[HardwareModel]";
                            $tableData .= "<tr>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowHWSN'.strval($rowCount)." value="."'".$tempSerial."'"." readonly>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowHWTID'.strval($rowCount)." value="."'".$tempID."'"." readonly>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowHWMk'.strval($rowCount)." value="."'".$tempMake."'"." readonly>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowHWMod'.strval($rowCount)." value="."'".$tempModel."'"." readonly>";
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

                //if we are editing the make of a hardware
                elseif($jsCommand == "editMake"){
                    $editingSerialFromJS = $_POST['serial'];
                    $makeChangeFromJS = $_POST['newMake'];
                    $sql = "UPDATE Hardware SET HardwareMake='$makeChangeFromJS' WHERE SerialNumber = '$editingSerialFromJS'" ;
                    if ($conn->query($sql) === TRUE) {
                        echo "Record updated successfully";
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }


                }

                //if we are editing the model of a hardware
                elseif($jsCommand == "editModel"){
                    $editingSerialFromJS = $_POST['serial'];
                    $modelChangeFromJS = $_POST['newModel'];
                    $sql = "UPDATE Hardware SET HardwareModel='$modelChangeFromJS' WHERE SerialNumber = '$editingSerialFromJS'" ;
                    if ($conn->query($sql) === TRUE) {
                        echo "Record updated successfully";
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }


                }

                //check if the model is different
                elseif($jsCommand == "checkDupeModel"){
                    $serialToCheck = $_POST['serial'];
                    $modelToCheck = $_POST['newModel'];
                    $sql = "SELECT SerialNumber FROM `Hardware` WHERE SerialNumber = "."'".$serialToCheck."'"."AND HardwareModel = "."'".$modelToCheck."'" ;
                    //echo $sql;
                    $result = $conn->query($sql);
                    if($result->num_rows == 0) {
                        echo "false";
                   } else {
                       echo "true";
                   }


                }
                //check if the make is different
                elseif($jsCommand == "checkDupeMake"){
                    $serialToCheck = $_POST['serial'];
                    $makeToCheck = $_POST['newMake'];
                    $sql = "SELECT SerialNumber FROM `Hardware` WHERE SerialNumber = "."'".$serialToCheck."'"."AND HardwareMake = "."'".$makeToCheck."'" ;
                    //echo $sql;
                    $result = $conn->query($sql);
                    if($result->num_rows == 0) {
                        echo "false";
                   } else {
                       echo "true";
                   }


                }





                

                
?>