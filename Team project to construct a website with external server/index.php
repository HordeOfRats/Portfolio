
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make-It-All</title>

    <link rel="stylesheet" href="helpdesk-style.css" />
    <link rel="shortcut icon" type="image/png" href="img/favicon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
    <script src="callFunctionality.js"></script>
    <script>
        document.getElementById("frame").style.display = "block";
        HideNavbar();
    </script>
</head>
<body>
<div id="popupCon" style="display: none;">
    <div id="popup" class="center-content"></div>
  </div>
    <div class="navbar" id="navbar">
        <a id="navHome" class="active" href ="Home.php" onclick="return LoadHome();"><i class="fa fa-fw fa-home"></i>Home</a> 
        <a id="navLogProb"  onclick="return LoadLogProblem();"><i class="fa fa-pencil-square-o"></i> Log Problem</a> 
        <a id="navUpdProb" href="updateProblem.php" onclick="return LoadUpdateProblem();"><i class="fa fa-upload"></i> Update Problem</a> 
        <a id="navOutProb" href="updateProblem.php" onclick="return LoadOutstandingProblems();"><i class="fa fa-upload"></i> Outstanding Problems</a> 
        <a id="navViewData" href="viewDatabase.php" onclick="return LoadViewDatabase();"><i class="fa fa-database"></i> View Tables</a>
        <a id="navViewSpecTasks" href="viewSpecialistTasks.php" onclick="return LoadViewSpecialistTasks();"><i class="fa fa-tasks"></i> View Current Jobs</a>
        <!-- href is kept so the hovering and clicking cursors are maintained. -->
        <div class="dropdown">
            <button class="dropbutton"><i class="fa fa-fw fa-user"></i> <label id="navLogout"> User</label> <i class="fa fa-caret-down"></i></button>
            <div class="dropdown-content">
                <a href="Login.php" onclick="return LoadInitialPage();">Logout</a>
            </div>
        </div>
        <div class="nav-right ">
      <a id="navStartCall" class="call" onclick="startCall()"><i class="fa fa-phone"></i> <span id="callStatus">Start Call</span></a>
    </div>
    </div>
    <script>
        let navbarType = 0;
        function ShowNavbar(userType)
        {
            //User Type 0 denotes a Desk Operator, User Type 1 denotes a Specialist.
            document.getElementById("navbar").style.display = "block";
            if(userType == 0)
            {
                document.getElementById("navLogProb").style.display = "inline-block";
                document.getElementById("navUpdProb").style.display = "inline-block";
                document.getElementById("navOutProb").style.display = "inline-block";
                document.getElementById("navStartCall").style.display = "inline-block";
                document.getElementById("navViewSpecTasks").style.display = "none";
                navbarType = 0;
            }
            else
            {
                document.getElementById("navViewSpecTasks").style.display = "inline-block";
                document.getElementById("navLogProb").style.display = "none";
                document.getElementById("navUpdProb").style.display = "inline-block";
                document.getElementById("navOutProb").style.display = "none";
                document.getElementById("navStartCall").style.display = "none";
                navbarType = 1;
            }
        }
        function GetNavbarType()
        {
            return navbarType;
        }

        function setCurrentUser(username)
        {
            document.getElementById("navLogout").innerHTML = username;
        }

        function HideNavbar()
        {
            document.getElementById("navbar").style.display = "none"
        }

        //There's probably a better way to remove the active class but I ain't bothered
        function LoadHome()
        {
            document.getElementById("frame").src = "Home.php";

            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navOutProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navHome").classList.add("active");
            return false;
            //returning false means the href of the navbar doesn't get loaded.
        }
        function LoadLogProblem()
        {
            if (callStartTime == undefined) {
                startCall()
            } else {
            document.getElementById("frame").src = "logProblem.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navOutProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navLogProb").classList.add("active");
            return false;
            }
        }
        function LoadUpdateProblem()
        {
            document.getElementById("frame").src = "updateProblem.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navOutProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navUpdProb").classList.add("active");
            return false;
        }
        function LoadOutstandingProblems()
        {
            document.getElementById("frame").src = "outstandingProblems.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navOutProb").classList.add("active");
            return false;
        }
        function SetTotalOutstandingProblemsCount(i)
        {
            if(i == 0)
            {
                document.getElementById("navOutProb").innerHTML = "Outstanding Problems";
            }
            else
            {
                document.getElementById("navOutProb").innerHTML = "Outstanding Problems " +'('+i+')';
            }
        }
        function LoadViewDatabase()
        {
            document.getElementById("frame").src = "viewDatabase.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navOutProb").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navViewData").classList.add("active");
            return false;
        }
        function LoadViewSpecialistTasks()
        {
            document.getElementById("frame").src = "viewSpecialistTasks.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navOutProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");

            document.getElementById("navViewSpecTasks").classList.add("active");
            return false;
        }
        function LoadInitialPage()
        {
            document.getElementById("frame").src = "Login.php";

            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navOutProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navHome").classList.add("active");

            HideNavbar();
            return false;
        }
        function CheckNavbarStatus()
        {
            //This fixes the problem of the back arrow messing up the navbar.
            if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/Login.php")
            {
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navOutProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navHome").classList.add("active");

                HideNavbar();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/viewSpecialistTasks.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navOutProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");

                document.getElementById("navViewSpecTasks").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/viewDatabase.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navOutProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navViewData").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/updateProblem.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navOutProb").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navUpdProb").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/outstandingProblems.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navOutProb").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/logProblem.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navOutProb").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navLogProb").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/Home2.php"
            || document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/Home.php")
            {
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navOutProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navHome").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/viewSoftwareTable.php"
            || document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/viewProblemTypeTable.php"
            || document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/viewOSTypeTable.php"
            || document.getElementById("frame").contentWindow.location.href == "https://make-it-all.co.uk/viewHardwareTable.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navOutProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navViewData").classList.add("active");
                CheckIfNavbarHidden();
            }
        }

        function CheckIfNavbarHidden()
        {
            if ((document.getElementById("navbar").style.display == "none") == true)
            {
                //Prevents the forward arrow surpassing login.
                LoadInitialPage();
            }
        }

    </script>

    <div class="main">
        <div>
            <iframe id="frame" src="Login.php" onLoad="CheckNavbarStatus();" width="100%" height="100%" style="overflow:none; border:0; position:absolute;"></iframe>
        </div>
    </div>
</body>
</html>