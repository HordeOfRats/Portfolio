<?php
  include "sql-login.php";

  // Check user login or not
  if(!isset($_SESSION['username'])){
      header('Location: Login.php');
  }

  // logout
  if(isset($_POST['logout'])){
      session_destroy();
      header('Location: Login.php');
  }

  $sqlTotal = "SELECT COUNT(ProblemID) as 'Total' FROM ProblemLog WHERE (CurrentAssignedSpecialist IS NULL) AND (DateResolved = '0000-00-00');";
  $res = mysqli_query($conn, $sqlTotal);
  $data = mysqli_fetch_assoc($res);
  $totalNum = $data['Total'];
  echo '<script>parent.SetTotalOutstandingProblemsCount('.$totalNum.')</script>';
?>

<html>
<head>

<title>Home | Make-It-All</title>

<link rel="stylesheet" href="helpdesk-style.css">
<!-- For icons. -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- This displays the icon in the tab. -->
<link rel="shortcut icon" type="image/png" href="img/favicon.png">

<style>

#someidentifier {
    position: fixed;
    z-index: 100; 
    bottom: 0; 
    left: 0;
    width: 100%;
}

</style>

<script>
  document.addEventListener("DOMContentLoaded", function() 
  {
    HideLogProblem();
  });

  let i = 0;
  function ShowPasswordChangeForm()
  {
    if(i == 0)
    {
      document.getElementById("passwordForm").style.display = "block";
      i = 1;
    }
    else
    {
      document.getElementById("passwordForm").style.display = "none";
      i = 0;
    }
  }

  function HideLogProblem()
  {
    if(parent.GetNavbarType() == 1)
    {
      document.getElementById("logNewProblem").style.display = "none";
    }
  }
</script>
</head>
<body>
<div class="main">
<div class="content" style="text-align: center;">

<h1 style="text-align: left; font-family:'helvetica';">
Home  | Make-It-All
</h1>
<hr />

<h2>
<?php
  echo "Welcome, " . $_SESSION['username'] . "!";
?>
</h2>

<h3 style="text-align: left;">Username: <?php echo $_SESSION['username']?></h3>
<h3 style="text-align: left;">ID: <?php echo $_SESSION['personnelID']?></h3>
<button onclick="ShowPasswordChangeForm()"type="button" style="text-align: left;">Change Password</button>

<form id="passwordForm" action = "" method="post" style="display:none">
  Enter current password: <input name ="currentPassword"id="currentPassword" type = "password"></input><br>
  Enter new password: <input name="newPassword1"id="newPassword1" type = "password"></input><br>
  Confirm new password: <input name="newPassword2"id="newPassword2" type = "password"></input><br>
  <button type="submit" name="submit" id="submit">Confirm Changes</button>
</form>

<?php
  if (isset($_REQUEST['submit']))
  {
    $CurrentEnteredPass = strval($_REQUEST['currentPassword']);
    $EnteredNewPass = strval($_REQUEST['newPassword1']);
    $ConfirmNewPass = strval($_REQUEST['newPassword2']);
    if ( empty($CurrentEnteredPass) || empty($EnteredNewPass) || empty($ConfirmNewPass) )
    {
      echo '<script>alert("Please fill in all fields.")</script>';
    }
    else if($EnteredNewPass != $ConfirmNewPass)
    {
      echo '<script>alert("Please ensure both new passwords match.")</script>';
    }
    else
    {
      $currentSignedIn = intval($_SESSION['personnelID']);
      $sql = "SELECT * FROM Credentials WHERE PersonnelID = ".$currentSignedIn.";";
      $result = mysqli_query($conn, $sql);
      $currentCredentialRow = mysqli_fetch_array($result);
      $currentCredentialPassword = strval($currentCredentialRow['Password']);
      if($currentCredentialPassword != $CurrentEnteredPass)
      {
        echo '<script>alert("Your entered current password does not match your actual password.")</script>';
      }
      else
      {
        $sql2 = "update Credentials set Password = '".$ConfirmNewPass."' where PersonnelID = '".$currentSignedIn."';";
        $sql = "select * from Credentials where Username='".$username."' and Password='".$password."'";
        if(mysqli_query($conn, $sql2))
        {
          echo '<script>alert("Password successfully changed.")</script>';
        }
        else
        {
          echo '<script>alert("Failed to update password")</script>';
        }
      }

    }
  }
?>

<hr />

<img src="Make-It-All Logo-White.png">
<p style="font-family: Lucida Handwriting;"> Here at Make-It-All, we Make-It-All count! </p>

<form action="updateProblem.php" method="post">
  <button type="submit" name="updateProblem" id="updateProblem">Update Problem</button>
  <br />
</form>

<form action="viewDatabase.php" method="post">
  <button type="submit" name="editDatabses" id="editDatabses">Edit Databases</button>
  <br />
</form>


  <button name="logNewProblem" id="logNewProblem" onclick="showPopup('startCallPopup.php')">Log new problem</button>
  <br />


<form action="Login.php" method="post">
  <button type="submit" name="logout" id="logout">Logout</button>
</form>
</div>
 
<div style="color: rgb(103, 103, 103); text-align: left; position: absolute; bottom: 5px;">
<hr>
Helpdesk telephone number: 07471 915391 <br>
Helpdesk email address: helpdesk@Make-It-All.co.uk <br>
Building location: 24 Royce Road
</div>
</div>
</body>

</html>