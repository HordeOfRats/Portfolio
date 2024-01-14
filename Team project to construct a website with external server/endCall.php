<html>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Make-It-All</title>

  <link rel="stylesheet" href="helpdesk-style.css" />
  <link rel="shortcut icon" type="image/png" href="img/favicon.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="callFunctionality.js"></script>
  <style>
    .call {
      transition: 300ms;
    }

    .call i {
      transform: rotate(135deg);
      transition: 300ms;
    }

    .call:hover {
      color: white !important;
    }

    .call:hover i {
      transform: rotate(0deg);

    }

    .on {
      background-color: #04aa6d;
    }

    .on i {
      transform: rotate(0deg);
    }

    .on:hover {
      background-color: red !important;
    }

    .on:hover i {
      transform: rotate(135deg);
    }

    #popupCon {
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: rgb(0, 0, 0, 0.1);
      z-index: 10;
    }

    #popup {
      background-color: white;
      padding: 1%;
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <div id="popupCon" style="display: none;">
    <div id="popup" class="center-content"></div>
  </div>
  <div class="navbar" id="navbar">
    <a id="navHome" class="active" href="Home.php" onclick="return LoadHome();"><i class="fa fa-fw fa-home"></i>Home</a>
    <a id="navLogProb" href="logProblem.php" onclick="return LoadLogProblem();"><i class="fa fa-pencil-square-o"></i> Log Problem</a>
    <a id="navUpdProb" href="updateProblem.php" onclick="return LoadUpdateProblem();"><i class="fa fa-upload"></i> Update Problem</a>
    <a id="navViewData" href="viewDatabase.php" onclick="return LoadViewDatabase();"><i class="fa fa-database"></i> View Tables</a>
    <a id="navViewSpecTasks" href="viewSpecialistTasks.php" onclick="return LoadViewSpecialistTasks();"><i class="fa fa-tasks"></i> View Current Jobs</a>
    <div class="dropdown">
      <button class="dropbutton">
        <i class="fa fa-fw fa-user"></i> User <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-content">
        <a href="Login.php" onclick="return LoadInitialPage();">Logout</a>
      </div>
    </div>
    <div class="nav-right ">
      <a class="call" onclick="startCall()"><i class="fa fa-phone"></i> <span id="callStatus">Start Call</span></a>
    </div>
  </div>
  <div class="main">
    <div class="content">
      <h1>Call details</h1>
      <div class="sub-content" id="problems">
        <div class="table-con">
          <table>
            <tr>
              <th>Problem ID</th>
              <th>Problem Type</th>
              <th>Action taken</th>
            </tr>

            <?php
            //include 'databasePersonnelLoginInfo.php';
            $query = "select ";
            ?>
          </table>
        </div>
        <h1>End call</h1>
        <div class="sub-content">
          <label for="callReason"></label>
          <textarea id="callReason"></textarea>
        </div>
      </div>
    </div>
</body>

</html>