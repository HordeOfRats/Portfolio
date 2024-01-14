<?php
session_start();
?>
<div class="table-con">
  <!--Table for associated problems-->
    <table>
        <tr>
            <th>Problem ID</th>
            <th>Problem Type</th>
            <th>Action taken</th>
        </tr>

        <?php
            include 'dbConnect.php';
            //Getting associatied problems
            $query = "SELECT ProblemID, (SELECT ProblemType FROM ProblemTypes ".
            "WHERE ProblemTypeID = (SELECT ProblemTypeID FROM ProblemLog WHERE ".
            "ProblemLog.ProblemID = ProblemCall.ProblemID)".
            ") as 'Type', ".
            "Created FROM ProblemCall WHERE CallID = ".$_SESSION["callID"].";";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
              //Putting results into table
              echo "<tr>";
              echo "<td>".$row["ProblemID"]."</td>";
              echo "<td>".$row["Type"]."</td>";
              echo "<td>".$row["Created"]."</td>";
              echo "</tr>";
            }
            ?>
    </table>
</div>
<table>
    <tr><td><label for="callReason">Call reason</label></td></tr>
    <tr><td><textarea id="callReason"></textarea></td></tr>
</table>
<div style="text-align: right">
  <button onclick="closePopup()" style="background-color: white; color: black">
    Cancel
  </button>
  <button onclick="endCall()">End Call</button>
</div>