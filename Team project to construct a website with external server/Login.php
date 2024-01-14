<html>

<head>
  <title>Login</title>

  <link rel="stylesheet" href="helpdesk-style.css" />

  <!-- This displays the icon in the tab. -->
  <link rel="shortcut icon" type="image/png" href="img/favicon.png" />
  <script>
    var loginCredentials;
    var allSpecialistIDs;
    document.addEventListener('DOMContentLoaded', function() {
      
      get("databasePersonnelLoginInfo.php", function (responseData) {
        //Turn the string encoded responseData into an actual array.
        loginCredentials = JSON.parse(responseData);
      });
      
      get("specialistIDs.php", function (responseData) {
        allSpecialistIDs = JSON.parse(responseData);
      });
    });
    
    function checkForm(form) {
      var currentUser = form.username.value;
      for (var key in loginCredentials) {
        if (form.username.value == loginCredentials[key][1] && form.password.value == loginCredentials[key][2]) {
          for (var i = 0; i < allSpecialistIDs.length; i++) {
            //document.getElementById("test").innerHTML += currentUser;
            //document.getElementById("test2").innerHTML += (allSpecialistIDs[i] == loginCredentials[key][3]);
            if (loginCredentials[key][3] == allSpecialistIDs[i]) {
              //If user is a specialist, display the specialist navbar.
              parent.ShowNavbar(1);
              parent.setCurrentUser(currentUser);
              return true;
            }
            else {
              parent.ShowNavbar(0);
            }
          }
          parent.setCurrentUser(currentUser);
          return true;
        }
      }
      alert('Invalid username or password.');
      return false;
    }

    function get(url, onComplete) {
      var ajax = new XMLHttpRequest();
      ajax.onreadystatechange = function () {
        if (ajax.readyState == XMLHttpRequest.DONE && ajax.status == 200) {
          var response = ajax.responseText;
          if (onComplete) {
            onComplete(response);
          }
        }
      };
      ajax.open("GET", url, true);
      ajax.send();
    }
  </script>

  <style>
    .login {
      padding: 50px;
      border: solid #1597E5 2px;
      border-radius: 15px;
      box-shadow: rgba(0, 0, 0, 0.1) 4px 4px 12px;
    }

    .logo {
      position: absolute;
      left: 50%;
      transform: translatex(-50%);
    }

    h1 {
      text-align: center;
      color: #193498;
      margin-bottom: 5px;
      margin-top: 0;
    }

    .navbar a {
      color: #333;
    }

    .navbar a:hover {
      background-color: #333;
      color: #333;
    }
  </style>
</head>

<body>
  <!-- REMNANTS OF A LOST AGE
  <div class="navbar" id="navbar">
  <a>.</a>
  <img class="logo" src="img/banner.png">
</div> -->

  <div class="center-content login">
    <h1>Login</h1>

    <form action="" onsubmit="return checkForm(this);" method="post">
      <div class="input-con focus-lb">
        <input name="username" type="text" id="username" placeholder=" " required />
        <label for="username">Username</label>
        <span class="focus-border"></span>
        <span class="focus-bg"></span>
      </div>
      <br />
      <!-- 'required' means the form won't submit unless a value has been entered
        into the field. -->

      <div class="input-con focus-lb">
        <input name="password" type="password" id="password" placeholder=" " required />
        <label for="password">Password</label>
        <span class="focus-border"></span>
        <span class="focus-bg"></span>
      </div>
      <br />
      <button type="submit" name="submit" id="submit">Submit</button>
    </form>
    <?php
    include "sql-login.php";
    if(isset($_POST['submit'])){
        $username = mysqli_real_escape_string($conn,$_POST['username']);
        $password = mysqli_real_escape_string($conn,$_POST['password']);
        if ($username != "" && $password != ""){
            $sql = "select * from Credentials where Username='".$username."' and Password='".$password."'";
            $result = mysqli_query($conn,$sql);
            $row = mysqli_fetch_array($result);

            $resultCheck = mysqli_num_rows($result);
            if ($resultCheck > 0) {
                $_SESSION['username'] = $username;
                $personnelID = $row["PersonnelID"];
                
                $_SESSION['personnelID'] = $personnelID;
                header('Location: Home.php');
            } else {
                echo "Invalid username or password";
            }
        }
    }
    ?>

  </div>
  <p id="test"></p>
  </div>

</body>

</html>