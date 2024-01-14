<table class="form-table">
  <tr>
    <td>
      <label for="callerId">Enter caller ID</label>
    </td>
  </tr>
  <tr>
    <td>
      <!--Input for caller ID-->
      <div class="input-con">
        <input id="callerId" />
        <span class="focus-bg"></span>
        <span class="focus-border"></span>
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <label for="callerDept">or find by department and name</label>
    </td>
  </tr>
  <tr>
    <td>
      <div class="input-con">
        <!--Dropdown for caller department-->
        <select id="callerDept" onchange="getNames()">
          <option disabled selected value style="display: none">
            Select department
          </option>
          <?php
          //Getting all the departments from the database
          include "dbConnect.php";
          $sql = "SELECT * FROM Department";
          $result = $conn->query($sql); while ($row = $result->fetch_assoc()) {
          echo "
          <option value='".$row["DepartmentID"]."'>
            " . $row["Department"] . "
          </option>
          "; } ?>
        </select>
        <span class="focus-bg"></span>
        <span class="focus-border"></span>
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <!--Dropdown for caller name-->
      <div class="input-con" style="display: none">
        <select id="callerName" onchange="fillId()"></select>
        <span class="focus-bg"></span>
        <span class="focus-border"></span>
      </div>
    </td>
  </tr>
  <tr>
    <td>
      <p id="errorP" style="display: none; color: red">Invalid ID</p>
    </td>
  </tr>
</table>

<div style="text-align: right">
  <button disabled id="cancelCall" onclick="cancelCall()" style="background-color: white; color: black">
    Cancel call
  </button>
  <button onclick="submitCaller()">Submit</button>
</div>
