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
        //return to the database selection table
        function returnToDbSelect() {
            window.location.href = "viewDatabase.php";
        };
        //function to populate the table div
        function updateTableJS(){
            var command = "update";
            var nameFilter = (document.getElementById("fSN").value)+'%';
            var idFilter = (document.getElementById("fSID").value)+'%';
            //alert(nameFilter);

            var updateXML = new XMLHttpRequest();
            var url = "softDatabaseFunctions.php";
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
            //document.write('RowHT'+rowSelected);
            var getSID = (document.getElementById('RowSID'+rowSelected).value);
            var getSN = (document.getElementById('RowSN'+rowSelected).value);
            //document.write(getHTID);
            //document.write(getHT);
            document.getElementById("editDiv").style.visibility="visible";
            document.getElementById("eSID").value = getSID;
            document.getElementById("eSN").value = getSN;
        }
        //attempt to pass changes from the edit div into the database
        function submitChanges(){
            //alert("beggining edit")
            var outputMsg = "";
            var url ="softDatabaseFunctions.php"
            var id = document.getElementById("eSID").value;
            var newName = document.getElementById("eSN").value;
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
            <label for="fSID">Software ID:</label>
            <input type="text" id="fSID" name="fSID" oninput="updateTableJS()">
            <label for="fSN">Software Name:</label>
            <input type="text" id="fSN" name="fSN" oninput="updateTableJS()">
            <br><br>

            <button onclick="returnToDbSelect()">Back to database selection</button>
            <br>

            <div id='tableDiv'>


            </div>
            <!--empty editing div -->
            <div id='editDiv' style="visibility: hidden ;">
                <input type="text" id="eSID" name="eSID" readonly>
                <input type="text" id="eSN" name="eSN">
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
