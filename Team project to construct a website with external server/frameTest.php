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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="callFunctionality.js"></script>
    <script>
        $(document).ready(function() 
        {
            $('#frame').show();
            HideNavbar();
        });
</script>
</head>
<body>
<div id="popupCon" style="display: none;">
    <div id="popup" class="center-content"></div>
    </div>
    <div class="navbar" id="navbar">
        <a id="navHome" class="active" href ="Home.php" onclick="return LoadHome();"><i class="fa fa-fw fa-home"></i>Home</a> 
        <a id="navLogProb" href="logProblem.php" onclick="return LoadLogProblem();"><i class="fa fa-pencil-square-o"></i> Log Problem</a> 
        <a id="navUpdProb" href="updateProblem.php" onclick="return LoadUpdateProblem();"><i class="fa fa-upload"></i> Update Problem</a> 
        <a id="navViewData" href="viewDatabase.php" onclick="return LoadViewDatabase();"><i class="fa fa-database"></i> View Tables</a>
        <a id="navViewSpecTasks" href="viewSpecialistTasks.php" onclick="return LoadViewSpecialistTasks();"><i class="fa fa-tasks"></i> View Current Jobs</a>
        <div class="dropdown">
            <button class="dropbutton"><i class="fa fa-fw fa-user"></i> <label id="navLogout"> User</label> <i class="fa fa-caret-down"></i></button>
            <div class="dropdown-content">
                <a href="Login.php" onclick="return LoadInitialPage();">Logout</a>
            </div>
        </div>
        <div class="nav-right ">
            <a id="navStartCall"class="call" onclick="startCall()"><i class="fa fa-phone"></i> <span id="callStatus">Start Call</span></a>
        </div>
    </div>
    <script>
        //returning false means the href of the navbar doesn't get loaded.
        //href is kept so the hovering and clicking cursors are maintained.

        function ShowNavbar(userType)
        {
            //User Type 0 denotes a Desk Operator, User Type 1 denotes a Specialist.
            $(".navbar").show();
            if(userType == 0)
            {
                $("#navLogProb").show();
                $("#navUpdProb").show();
                $("#navStartCall").show();
                $("#navViewSpecTasks").hide();
            }
            else
            {
                $("#navViewSpecTasks").show();
                $("#navLogProb").hide();
                $("#navUpdProb").hide();
                $("#navStartCall").hide();
            }
        }

        function setCurrentUser(username)
        {
            document.getElementById("navLogout").innerHTML = username;
        }

        function HideNavbar()
        {
            $(".navbar").hide();
        }

        //There's probably a better way to remove the active class but I ain't bothered
        function LoadHome()
        {
            document.getElementById("frame").src = "Home2.php";

            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navHome").classList.add("active");
            return false;
        }
        function LoadLogProblem()
        {
            document.getElementById("frame").src = "logProblem.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navLogProb").classList.add("active");
            return false;
        }
        function LoadUpdateProblem()
        {
            document.getElementById("frame").src = "updateProblem.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navUpdProb").classList.add("active");
            return false;
        }
        function LoadViewDatabase()
        {
            document.getElementById("frame").src = "viewDatabase.php";

            document.getElementById("navHome").classList.remove("active");
            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
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
            document.getElementById("navViewData").classList.remove("active");

            document.getElementById("navViewSpecTasks").classList.add("active");
            return false;
        }
        function LoadInitialPage()
        {
            document.getElementById("frame").src = "Login.php";

            document.getElementById("navLogProb").classList.remove("active");
            document.getElementById("navUpdProb").classList.remove("active");
            document.getElementById("navViewData").classList.remove("active");
            document.getElementById("navViewSpecTasks").classList.remove("active");

            document.getElementById("navHome").classList.add("active");

            HideNavbar();
            return false;
        }
        function CheckNavbarStatus()
        {
            //This fixes the problem of the back arrow messing up the navbar.
            if(document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/Login.php")
            {
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navHome").classList.add("active");

                HideNavbar();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/viewSpecialistTasks.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");

                document.getElementById("navViewSpecTasks").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/viewDatabase.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navViewData").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/updateProblem.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navUpdProb").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/logProblem.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navLogProb").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/Home2.php"
            || document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/Home.php")
            {
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewData").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navHome").classList.add("active");
                CheckIfNavbarHidden();
            }
            else if(document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/viewSoftwareTable.php"
            || document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/viewProblemTypeTable.php"
            || document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/viewOSTypeTable.php"
            || document.getElementById("frame").contentWindow.location.href == "http://team002.sci-project.lboro.ac.uk/viewHardwareTable.php")
            {
                document.getElementById("navHome").classList.remove("active");
                document.getElementById("navLogProb").classList.remove("active");
                document.getElementById("navUpdProb").classList.remove("active");
                document.getElementById("navViewSpecTasks").classList.remove("active");

                document.getElementById("navViewData").classList.add("active");
                CheckIfNavbarHidden();
            }
        }

        function CheckIfNavbarHidden()
        {
            if($(".navbar").is(':hidden'))
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