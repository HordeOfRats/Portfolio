<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resolve Problem | Make-It-All</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="helpdesk-style.css">
    <!-- For icons. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- This displays the icon in the tab. -->
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">
</head>

<body>

<!-- <div class="navbar" id="navbar">
        <a href="Home.php"><i class="fa fa-fw fa-home"></i>Home</a> 
        <a class="active" href="logProblem.php"><i class="fa fa-pencil-square-o"></i> Log Problem</a> 
        <a href="updateProblem.php"><i class="fa fa-upload"></i> Update Problem</a> 
        <a href="viewDatabase.php"><i class="fa fa-database"></i> View Tables</a>
        <a href="viewSpecialistTasks.php"><i class="fa fa-tasks"></i> View Current Jobs</a>
        <div class="dropdown">
            <button class="dropbutton"><i class="fa fa-fw fa-user"></i> User <i class="fa fa-caret-down"></i></button>
            <div class="dropdown-content">
                <a href="Login.php">Logout</a>
            </div>
        </div>
        <div class="nav-right">
            <img class="logo" src="img/banner.png">
        </div>
    </div> -->

  <script>
    $(document).ready(function()
      {
      //Gets the problemID and problemType from the previous page, logProblem.php
      var problemID = "<?php echo (string)$_POST['problemID']?>";
      var problemType = "<?php echo (string)$_POST['problemType']?>";

      $("#pID").val(problemID);
      $("#pType").val(problemType);
      });

    let problemList = [
      //ProblemID, ProblemTypeID, ReasonForCall, OperatorNotes, ProblemResolution
      [1, 5, "Can't connect to printer", "Printer not displayed on network", "Updated operating system"],
      [2, 4, "Frequently get this randomly", "Caller didn't shutdown computer properly before", "OS corrupted, OS was re-installed"],
      [3, 6, "a", "", ""],
      [4, 4, "f", "", ""],
      [5, 1, "c", "", ""],
      [6, 5, "Printer won't print", "Printer doesn't receive printing requests", "Restarted printer"],
      [7, 4, "4", "23s", ""],
      [8, 5, "Frequent paper jams", "N/A", "There was a screw loose in the printer."],
      [9, 1, "asdf", "", "sfewr"],
      [10, 3, "gfd", "", "3rwwer"],
      [11, 7, "ku", "", "qwer"],
      [12, 2, "pokl", "12", "fa"],
    ];
    
    function displayRows(problemList) {
      for (var i = 0; i < (problemList.length); i++) {
        if (problemList[i][1] == 5) { //Hard-coded as 5 but after prototype, change to problemTypeID.
          document.getElementById("myTable").innerHTML = document.getElementById("myTable").innerHTML +
          "<tr> <td><label>"+problemList[i][0]+"</label></td> <td><label>"+problemList[i][2]+"</label></td> <td><label>"+problemList[i][3]+"</label></td> <td><label>"+problemList[i][4]+"</label></td> </tr>"; 
        }
      }
	  }
  </script>

<div class="main">
  <div class="content">
    <h1>View Previous Solutions</h1>
    <hr>
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
            <body onload="displayRows(problemList)">
            </table>
        </div>
      </form>
    </div>
    <br>
	<hr />
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
  <div style='color: rgb(103, 103, 103); margin-left:10px;'>
	<hr />
      Helpdesk telephone number: 07471 915391 <br>
      Helpdesk email address: helpdesk@Make-It-All.co.uk <br>
      Building location: 24 Royce Road
     </div>
</div>

</body>
</html>