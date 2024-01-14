<html>

<head>
  <title>Login</title>

  <link rel="stylesheet" href="helpdesk-style.css" />

  <!-- This displays the icon in the tab. -->
  <link rel="shortcut icon" type="image/png" href="img/favicon.png" />

  <style>
    .login {
      padding: 50px;
      border: solid #1597E5 2px;
      border-radius: 15px;
      box-shadow: rgba(0, 0, 0, 0.1) 4px 4px 12px;
    }

    h1 {
      text-align: center;
      color: #193498;
      margin-bottom: 5px;
      margin-top: 0;
    }
  </style>
</head>

<body>
  <script>
    function checkForm(form) {
      if (
        (form.username.value == "Alice" && form.password.value == "cake") ||
        (form.username.value == "Bert" && form.password.value == "dog") ||
        (form.username.value == "Clara" && form.password.value == "orange")
      ) {
        return true;
      } else {
        alert("Error Password or Username"); /*displays error message*/
        return false;
      }
    }
  </script>

  <div class="center-content login">
    <h1>Login</h1>

    <form action="HomepageNavTest.php" onsubmit="return checkForm(this);" method="post">
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
  </div>
  </div>
</body>

</html>