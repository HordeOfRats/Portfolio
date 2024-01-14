

<!DOCTYPE html>

<head>

    <title>Software | Make-It-All</title>
    <link rel="stylesheet" href="helpdesk-style.css">

    <!-- For icons. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- This displays the icon in the tab. -->
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">

</head>

<body>
    <script>
        function getLoggedID(){
            var command = "getUserID";

            var updateXML = new XMLHttpRequest();
            var url = "specialistTaskFunctions.php";
            var updateParams = "command=getUserID";
            //alert (updateParams);
            updateXML.open("POST", url, false);

            //Send the proper header information along with the request
            updateXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            updateXML.send(updateParams);
            //alert (updateXML.responseText);
            return (updateXML.responseText);



        }

        function updateTableJS(filter){
            var logID = getLoggedID();
            logID = logID.replace(/(\r\n|\n|\r)/gm, "");
            //alert (logID);
            var idFilter = "all";
            if (filter == "all"){
                var idFilter = 'all';
            }
            else if (filter == "userJob"){
                var idFilter = logID +'%';
            }
            else if (filter == "currAss"){
                var idFilter = '%';
            }
            else if (filter == "unAss"){
                var idFilter = 'null';
            }
            var command = "update";
            //alert(idFilter);

            var updateXML = new XMLHttpRequest();
            var url = "specialistTaskFunctions.php";
            var updateParams = "command=update&idFilter="+idFilter;
            //alert (updateParams);
            updateXML.open("POST", url, false);

            //Send the proper header information along with the request
            updateXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            updateXML.send(updateParams);
            //alert (updateXML.responseText);
            document.getElementById("tableDiv").innerHTML = (updateXML.responseText)

        }

        function unassign(probIDFromFunc){
            //alert(idFilter);

            var unassXML = new XMLHttpRequest();
            var url = "specialistTaskFunctions.php";
            var unassParams = "command=unassign&probIDToUnass="+probIDFromFunc;
            //alert (unassParams);
            unassXML.open("POST", url, false);

            //Send the proper header information along with the request
            unassXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            unassXML.send(unassParams);
            //alert (unassXML.responseText);
            document.getElementById("logBox").value = (unassXML.responseText)
            updateTableJS();
        }



    </script>


    <div class="main">
        <div class="content">
            <input type="button" value="All jobs" onclick="updateTableJS('all')">
            <input type="button" value="Filter your jobs" onclick="updateTableJS('userJob')">
            <input type="button" value="Filter currently assigned jobs" onclick="updateTableJS('currAss')">
            <input type="button" value="Filter unassigned jobs" onclick="updateTableJS('unAss')">
            <br><br>

            <br>

            <div id='tableDiv'>


            </div>

            <input type="text" id="logBox" name="logBox" value="" readonly size="50">
            <script>
            updateTableJS();
            </script>
            <!-- <button onclick="updateTableJS()">Manually update table</button> -->

            






        </div>
        <div style='color: rgb(103, 103, 103);'>
            <hr />
            Helpdesk telephone number: 07471 915391 <br>
            Helpdesk email address: helpdesk@Make-It-All.co.uk <br>
            Building location: 24 Royce Road
        </div>
    </div>
</body>

</html>
