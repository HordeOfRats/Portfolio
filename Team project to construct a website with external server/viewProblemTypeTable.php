<!DOCTYPE html>

<head>

    <title>Problem Types| Make-It-All</title>
    <link rel="stylesheet" href="helpdesk-style.css">

    <!-- For icons. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- This displays the icon in the tab. -->
    <link rel="shortcut icon" type="image/png" href="img/favicon.png">

</head>


<body>
    <script>
        //return to the database selection table
        function returnToDbSelect() {
            window.location.href = "viewDatabase.php";
        };
        //function to populate the table div
        function updateTableJS(){
            var command = "update";
            var nameFilter = (document.getElementById("fPT").value)+'%';
            var idFilter = (document.getElementById("fPTID").value)+'%';
            //alert(nameFilter);

            var updateXML = new XMLHttpRequest();
            var url = "probTypeDatabaseFunctions.php";
            var updateParams = "command=update&nameFilter="+nameFilter+"&idFilter="+idFilter;
            //alert (updateParams);
            updateXML.open("POST", url, false);

            //Send the proper header information along with the request
            updateXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            updateXML.send(updateParams);
            //alert (updateXML.responseText);
            document.getElementById("tableDiv").innerHTML = (updateXML.responseText)
        

        }
        //put the selected row into the editing div
        function selectRow(rowSelected){

            //var getHTID = document.getElementById('select'.rowSelected).RowHTID.value;
            rowSelected = String(rowSelected);
            //alert('RowHT'+rowSelected);
            var getPTID = (document.getElementById('RowPTID'+rowSelected).value);
            var getPT = (document.getElementById('RowPT'+rowSelected).value);
            //alert(getPTID);
            //alert(getPT);
            document.getElementById("editDiv").style.visibility="visible";
            document.getElementById("ePTID").value = getPTID;
            document.getElementById("ePT").value = getPT;
        }

        //attempt to pass changes from the edit div into the database
        function submitChanges(){
            //alert("beggining edit")
            var outputMsg = "";
            var url ="probTypeDatabaseFunctions.php"
            var command = "checkDupe";
            var id = document.getElementById("ePTID").value;
            var newName = document.getElementById("ePT").value;
            //alert (id);
            //alert (newName);
            var noOfRows = 0;

            var dupeNameXML = new XMLHttpRequest();
            var dupeNameParams = "command=checkDupe&newName="+newName;
            //alert("here 1")
            dupeNameXML.open("POST", url, false);
            //Send the proper header information along with the request
            dupeNameXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            dupeNameXML.send(dupeNameParams);
            //alert("name: "+dupeNameXML.responseText);
            //alert("here 2")
            if(dupeNameXML.responseText == "false"){
                //alert ("here")
                var editNameXML = new XMLHttpRequest();
                var editNameParams = "command=edit&id="+id+"&newName="+newName;
                //alert (editNameParams);
                editNameXML.open("POST", url, false);
                //Send the proper header information along with the request
                editNameXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                editNameXML.send(editNameParams);
                outputMsg += "Name updated. "
                           

            }
            else{
                outputMsg += "Duplicate name. "
                
            }
            document.getElementById("logBox").value = outputMsg; 
            updateTableJS();


        }



    </script>


    <div class="main">
        <div class="content">
            <!--filters -->
            <label for="fPTID">Problem type ID:</label>
            <input type="text" id="fPTID" name="fPTID" oninput="updateTableJS()">
            <label for="fPT">Problem type:</label>
            <input type="text" id="fPT" name="fPT" oninput="updateTableJS()">
            <br><br>

            <button onclick="returnToDbSelect()">Back to database selection</button>
            <br>

            <div id='tableDiv'>


            </div>
            <!--empty editing div -->
            <div id='editDiv' style="visibility: hidden ;">
                <input type="text" id="ePTID" name="ePTID" readonly>
                <input type="text" id="ePT" name="ePT">
                <button onclick="submitChanges()">Submit name change</button>
                


            </div>
            <input type="text" id="logBox" name="logBox" value="" readonly size="50">
            <script>
            updateTableJS();
            </script>
            <button onclick="updateTableJS()">Manually update table</button>

            






        </div>
        <div style='color: rgb(103, 103, 103);'>
            <hr />
            Helpdesk telephone number: 07471 915391 <br>
            Helpdesk email address: helpdesk@Make-It-All.co.uk <br>
            Building location: 24 Royce Road
        </div>

</body>

</html>