
<html>
<head>

<title>Home | Make-It-All</title>

<link rel="stylesheet" href="helpdesk-style.css">
<!-- For icons. -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- This displays the icon in the tab. -->
<link rel="shortcut icon" type="image/png" href="img/favicon.png">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
  $(document).ready(function()
  {
    var userName = "<?php echo (string)$_POST['username'] ?>";
    
    if(userName == "Alice")
    {
      <?php include 'navbarInclude.php'; echo($userNav); ?>;
    }
    else
    {
      <?php echo("poo"); ?>;
    }
  });
</script>

</head>

<body>


<div class="main">
<div class="contentContainer" style="text-align: center;">


<form action="updateProblemPage.php" method="post">
  <button type="submit" name="updateProblem" id="updateProblem">Update Problem</button>
  <br />
</form>

<form action="db-home.php" method="post">
  <button type="submit" name="editDatabses" id="editDatabses">Edit Databases</button>
  <br />
</form>

<form action="logProblemPage.php" method="post">
  <button type="submit" name="logNewProblem" id="logNewProblem">Log new problem</button>
  <br />
</form>

<form action="Login.php" method="post">
  <button type="submit" name="logout" id="logout">Logout</button>
</form>

</div>
</div>

</body>
</html>