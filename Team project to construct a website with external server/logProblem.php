<?php include "sql-login.php"; ?>

<!--
Anant - To Do:
- Prevent form fields clearing on button click.
-->

<!DOCTYPE html>
<html>
<head>
<title>Log Problem | Make-It-All</title>
<link rel="stylesheet" href="helpdesk-style.css">
<!-- For icons. -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- This displays the icon in the tab. -->
<link rel="shortcut icon" type="image/png" href="img/favicon.png">

<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button{
-webkit-appearance:none;
margin:0;
}
input[type=number] {
  -moz-appearance: textfield;
}

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

<div class="main">
  <div class="flex-parent">
    <!-- Log Problem -->
    <div class="flex-child">
        <div class="content">
          <h1>Log Problem</h1>
          <hr>
          <div class="sub-content">
          
            <form id="logProblem" action="" method="post">
              <div class="form-table">
                <table>
                  <tr>
                  <!-- For every row, the first line, 'label' adds text to the left column of that row
                      and the second line, 'input'adds an input box to the right column of that row. -->
                  <td><label for="problemType">Problem Type</label></td>
                  <td><div class="input-con">
                      <span class="focus-bg"></span>
                      <span class="focus-border"></span>
                  
                    <!-- Creates a dropdown list with the following options. -->
                    <?php
                    $sqlProblemTypes = "SELECT * FROM ProblemTypes;";
                    $resultProblemTypes = mysqli_query($conn,$sqlProblemTypes);
                    $resultCheck = mysqli_num_rows($resultProblemTypes);
                    if ($resultCheck > 0) {
                      $select= '<select name="problemTypeID" id="problemTypeID">';
                      while($row=mysqli_fetch_array($resultProblemTypes)){
                        $select.='<option value="'.$row['ProblemTypeID'].'">'.$row['ProblemType'].'</option>';
                      }
                      $select.='</select>';
                      echo $select;
                    }
                    ?>
                  </div></td>
                  </tr>
                  <!--<tr>
                  <td><label for="callerID">Caller ID</label></td>
                  <td><div class="input-con">
                      <input name="callerID" type="number" id="callerID"
                      value="<php// if(isset($_POST['callerID'])) echo $_POST['callerID']; ?>" required/>
                      <span class="focus-bg"></span>
                      <span class="focus-border"></span>
                  </div></td>
                  </tr>-->
                  <tr>
                  <td><label for="callerIssue">Caller Issue</label></td>
                  <td><div class="input-con">
                    <span class="focus-bg"></span>
                    <span class="focus-border"></span>
                    <textarea name="callerIssue" id="callerIssue" style="height:60px"
                      value="<?php if(isset($_POST['callerIssue'])) echo $_POST['callerIssue']; ?>" required></textarea>
                  </div></td>
                  </tr>
                  <tr>
                  <td><label for="operatorNotes">Operator Notes</label></td>
                  <td><textarea name="operatorNotes" id="operatorNotes" style="height:50px" placeholder="optional"
                      value="<?php if(isset($_POST['operatorNotes'])) echo $_POST['operatorNotes']; ?>"></textarea></td>
                  </tr>
                  <tr>
                  <td><label for="hardware">Hardware Name</label></td>
                    <td><div class="input-con">
                    <span class="focus-bg"></span>
                    <span class="focus-border"></span>

                    <?php
                    $sqlHardwareTypes = "SELECT * FROM HardwareTypes;";
                    $resultHardwareTypes = mysqli_query($conn,$sqlHardwareTypes);
                    $resultCheck = mysqli_num_rows($resultHardwareTypes);
                    if ($resultCheck > 0) {
                      $select= '<select name="hardware" id="hardware">';
                      $select.= '<option value="">N/A</option>';
                      while($row=mysqli_fetch_array($resultHardwareTypes)){
                        $select.='<option value="'.$row['HardwareTypeID'].'">'.$row['HardwareType'].'</option>';
                      }
                      $select.='</select>';
                      echo $select;
                    }
                    ?>
                  </div></td>
                  </tr>
                  <tr>
                  <td><label for="operatingSystem">Operating System</label></td>
                  <td><div class="input-con">
                    <span class="focus-bg"></span>
                    <span class="focus-border"></span>
                    <?php
                    $sqlOSTypes = "SELECT * FROM OSTypes;";
                    $resultOSTypes = mysqli_query($conn,$sqlOSTypes);
                    $resultCheck = mysqli_num_rows($resultOSTypes);
                    if ($resultCheck > 0) {
                      $select= '<select name="operatingSystem" id="operatingSystem">';
                      $select.= '<option value="">N/A</option>';
                      while($row=mysqli_fetch_array($resultOSTypes)){
                        $select.='<option value="'.$row['OSID'].'">'.$row['OSType'].'</option>';
                      }
                      $select.='</select>';
                      echo $select;
                    }
                    ?>
                  </div></td>
                  </tr>
                  <tr>
                  <td><label for="software">Software</label></td>
                  <td><div class="input-con">
                    <span class="focus-bg"></span>
                    <span class="focus-border"></span>
                    <?php
                    $sqlSoftwareTypes = "SELECT * FROM SoftwareTypes;";
                    $resultSoftwareTypes = mysqli_query($conn,$sqlSoftwareTypes);
                    $resultCheck = mysqli_num_rows($resultSoftwareTypes);
                    if ($resultCheck > 0) {
                      $select= '<select name="software" id="software">';
                      $select.= '<option value="">N/A</option>';
                      while($row=mysqli_fetch_array($resultSoftwareTypes)){
                        $select.='<option value="'.$row['SoftwareID'].'">'.$row['SoftwareName'].'</option>';
                      }
                      $select.='</select>';
                      echo $select;
                    }
                    ?>
                  </div></td>
                  </tr>
              </table>
              <button onclick="displayRows()" type="submit" name="submit" id="submit">Log Problem</button>
              </div>
            </form>

          </div>
        </div>
    </div>
    <div class="flex-child">
      <div class="content">
    <?php
    if(isset($_REQUEST['submit'])) {
      echo "<h1>View Previous Solutions</h1>
      <hr>";
      $HardwareSerialNumber = intval($_REQUEST['hardware']);
      $OperatingSystemID = intval($_REQUEST['operatingSystem']);
      $SoftwareID = intval($_REQUEST['software']);
      $CallerIssue = '"' . strval($_REQUEST['callerIssue']) . '"';
      $OperatorNotes = '"' . strval($_REQUEST['operatorNotes']) . '"';
      $ProblemTypeID = intval($_REQUEST['problemTypeID']);
      
      $sqlSave = "INSERT INTO ProblemLog (HardwareSerialNumber,OperatingSystemID,SoftwareID,CallerIssue,OperatorNotes,ProblemTypeID) VALUES ($HardwareSerialNumber,$OperatingSystemID,$SoftwareID,$CallerIssue,$OperatorNotes,$ProblemTypeID);";
      $sqlLink = "INSERT INTO ProblemCall VALUES (".$_SESSION["callID"].", LAST_INSERT_ID(), 1);"; 
      if (mysqli_query($conn,$sqlSave)) {
        echo '<script>alert("Problem Logged.")</script>';
        $conn->query($sqlLink);
      } else {
        echo "Error: " . $sqlSave . "<br>" . $conn->error;
      }
      
      $sql = "SELECT * FROM ProblemLog WHERE ProblemTypeID = '$ProblemTypeID' AND DateResolved != '0000-00-00';";
      $result = mysqli_query($conn,$sql);
      
      //Checks the connection to the database.
      if (!$conn) {
        //The die() function prints a message and then terminates the current script.
        die("Connection failed: " . mysqli_connect_error());
      }

      //Closes the connection to the database.
      mysqli_close($conn);

      //Checks that the ProblemLog table isn't empty to prevent an error.
      $resultCheck = mysqli_num_rows($result);
      if ($resultCheck > 0) {
        ?>
        <!-- View Previous Problems -->
            <h2>Suggested Solutions</h2>
            <div class="sub-content">
            <!-- This form displays all of the previous prob  lems' info with the same problem type as the current problem
                  the helpdesk operator is dealing with. -->
              <form action="" method="post">
                <div class="table-con">
                  <table id="myTable">
                    <tr>
                      <th>Problem ID</th>
                      <th>Call Reason</th>
                      <th>Notes</th>
                      <th>Resolution</th>
                    </tr>
        <?php
                    while ($row = mysqli_fetch_array($result)) {
                      echo "<tr>
                              <td><label>".strval($row["ProblemID"])."</label></td>
                              <td><label>".$row["CallerIssue"]."</label></td>
                              <td><label>".$row["OperatorNotes"]."</label></td>
                              <td><label>".$row["ProblemResolution"]."</label></td>
                            </tr>";
        }
        ?>
                </table>
              </div>
            </form>
          </div>
          <br>
          <hr/>
          <?php
          } else {
            echo 'There are no previous problems similar to this one.';
          }
          ?>
          <div class="sub-content">
            <form action="SpecialistAllocation.php" method="post">
              <div class="table-con">
                <input type="text" name="pID" id = "pID" style="display:none;">
                <input type="text" name="pType" id = "pType" style="display:none;">
                <table>
                  <tr>
                    <td style="border-top:none;"><button type="submit" name="SpecialistAllocation" id="SpecialistAllocation">Use Specialist</button></td>
                  </tr>
                </table>
              </div>
            </form>
            <form action="updateProblem.php" method="post">
              <div class="table-con">
                <table>
                  <tr>
                    <td style="border-top:none;"><button type="submit" name="problemSolved" id="problemSolved">Problem Solved</button></td>
                  </tr>
                </table>
              </div>
            </form>
          </div>
        </div>
    </div>
    <?php
    }
    ?>
  </div>
</div>

</body>
</html>