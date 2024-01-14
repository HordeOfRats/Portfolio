<!DOCTYPE html>
<html>
<head>
<title>View Tables | Make-It-All</title>
<link rel="stylesheet" href="helpdesk-style.css">

<!-- For icons. -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>

<!-- This displays the icon in the tab. -->
<link rel="shortcut icon" type="image/png" href="img/favicon.png" />
</head>

<body>

<!-- <div class="navbar" id="navbar">
        <a href="Home.php"><i class="fa fa-fw fa-home"></i>Home</a> 
        <a href="logProblem.php"><i class="fa fa-pencil-square-o"></i> Log Problem</a> 
        <a href="updateProblem.php"><i class="fa fa-upload"></i> Update Problem</a> 
        <a class="active" href="viewDatabase.php"><i class="fa fa-database"></i> View Tables</a>
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

    <!-- generate the buttons to go to each table -->
    <div class="main">
      <div class="content">
        <h1>View Tables</h1>
        <hr>
        <div class="table-con">

            <button onclick="goHardTable()" style="padding: 50px; font-size: 30px; width: 450px;">Hardware Table</button>
            <button onclick="goSoftTable()" style="padding: 50px; font-size: 30px; width: 450px;">Software Table</button> <br>
            <button onclick="goProbTable()" style="padding: 50px; font-size: 30px; width: 450px;">Problem Type Table</button>
			<button onclick="goOSTable()" style="padding: 50px; font-size: 30px; width: 450px;">OS Type Table</button>

          
        </div>

        <?php


    ?>
        
        <script>
          //functions for buttons
          function goOSTable() {
            window.location.href =
              "https://make-it-all.co.uk/viewOSTypeTable.php";
          }

          function goHardTable() {
            window.location.href =
              "https://make-it-all.co.uk/viewHardwareTable.php";
          }

          function goSoftTable() {
            window.location.href =
              "https://make-it-all.co.uk/viewSoftwareTable.php";
          }

          function goProbTable() {
            window.location.href =
              "https://make-it-all.co.uk/viewProblemTypeTable.php";
          }
        </script>
      </div>
	  <div class = "footer" style="color: rgb(103, 103, 103); text-align: left;">
    </div>
	<div style='color: rgb(103, 103, 103); margin-left:10px;'>
	<hr />
      Helpdesk telephone number: 07471 915391 <br>
      Helpdesk email address: helpdesk@Make-It-All.co.uk <br>
      Building location: 24 Royce Road
     </div>
  </body>
</html>
