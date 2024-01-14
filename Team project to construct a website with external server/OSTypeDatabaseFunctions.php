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
                    $sql = "SELECT OSID, OSType FROM `OSTypes` WHERE OSType LIKE $nameFilterFromJS AND OSID LIKE $IDFilterFromJS";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $tableData = "<table id='DBTable'>";
                        $tableData .= "<tr>";
                        $tableData .= "<th>";
                        $tableData .= "OS ID";
                        $tableData .= "</th>";
                        $tableData .= "<th>";
                        $tableData .= "OS Type";
                        $tableData .= "</th>";
                        $tableData .= "</tr>";

                        //add identifiers and data to the table
                        $rowCount = 1;
                        while($row = $result->fetch_assoc()) {
                            $tempID = "$row[OSID]";
                            $tempName = "$row[OSType]";
                            $tableData .= "<tr>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowOSID'.strval($rowCount)." value="."'".$tempID."'"." readonly width=30>";
                            $tableData .= "</td>";
                            $tableData .= "<td>";
                            $tableData .= "<input type='text' id=".'RowOST'.strval($rowCount)." value="."'".$tempName."'"." readonly width=30>";
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

                //edit name of operating systems
                elseif($jsCommand == "edit"){
                    $editingIDFromJS = $_POST['id'];
                    $nameChangeFromJS = $_POST['newName'];
                    $sql = "UPDATE OSTypes SET OSType='$nameChangeFromJS' WHERE OSID = '$editingIDFromJS'" ;
                    if ($conn->query($sql) === TRUE) {
                        echo "Record updated successfully";
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }


                }

                //get number of rows in the database
                elseif($jsCommand == "getRows"){
                    $sql = "SELECT * FROM `OSTypes`";
                    $result = $conn->query($sql);
                    $row = mysqli_num_rows($result);
                    echo $row;




                }

                //check if the name is a duplicate
                elseif($jsCommand == "checkDupe"){
                    $nameToCheck = $_POST['newName'];
                    $sql = "SELECT OSType FROM `OSTypes` WHERE OSType= "."'".$nameToCheck."'";
                    //echo $sql;
                    $result = $conn->query($sql);
                    if($result->num_rows == 0) {
                        echo "false";
                   } else {
                       echo "true";
                   }


                }




                

                
?>