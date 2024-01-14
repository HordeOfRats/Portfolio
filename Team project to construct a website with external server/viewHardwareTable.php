<!DOCTYPE html>

<head>

    <title>Hardware | Make-It-All</title>
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
            //get selected filters
            var serialFilter = (document.getElementById("fHWSN").value)+'%';
            var idFilter = (document.getElementById("fHWTID").value)+'%';
            var makeFilter = (document.getElementById("fHWMk").value)+'%';
            var modFilter = (document.getElementById("fHWMod").value)+'%';


            var updateXML = new XMLHttpRequest();
            var url = "hardwareDatabaseFunctions.php";
            var updateParams = "command=update&serialFilter="+serialFilter+"&idFilter="+idFilter+"&makeFilter="+makeFilter+"&modFilter="+modFilter;
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
            var getHWSN = (document.getElementById('RowHWSN'+rowSelected).value);
            var getHWTID = (document.getElementById('RowHWTID'+rowSelected).value);
            var getHWMk = (document.getElementById('RowHWMk'+rowSelected).value);
            var getHWMod = (document.getElementById('RowHWMod'+rowSelected).value);
            //document.write(getHTID);
            //document.write(getHT);
            document.getElementById("editDiv").style.visibility="visible";
            document.getElementById("eHWSN").value = getHWSN;
            document.getElementById("eHWTID").value = getHWTID;
            document.getElementById("eHWMk").value = getHWMk;
            document.getElementById("eHWMod").value = getHWMod;
        }

        //attempt to pass changes from the edit div into the database
        function submitChanges(){
            //alert("beggining edit")
            var outputMsg = "";
            var url = "hardwareDatabaseFunctions.php";
            var serial = document.getElementById("eHWSN").value;
            var newMake = document.getElementById("eHWMk").value;
            var newModel = document.getElementById("eHWMod").value;
            var noOfRows = 0;
            //alert("here 0")
            var dupeMakeXML = new XMLHttpRequest();
            var dupeMakeParams = "command=checkDupeMake&serial="+serial+"&newMake="+newMake;
            //alert("here 1")
            dupeMakeXML.open("POST", url, false);
            //Send the proper header information along with the request
            dupeMakeXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            dupeMakeXML.send(dupeMakeParams);
            //alert("make: "+dupeMakeXML.responseText);
            //alert("here 2")
            if(dupeMakeXML.responseText == "false"){
                //alert ("here")
                var editMakeXML = new XMLHttpRequest();
                var editMakeParams = "command=editMake&serial="+serial+"&newMake="+newMake;
                editMakeXML.open("POST", url, false);
                //Send the proper header information along with the request
                editMakeXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                editMakeXML.send(editMakeParams);
                outputMsg += "Make updated. "
                           

            }
            else{
                outputMsg += "Duplicate make. "
                
            }
            

            var dupeModelXML = new XMLHttpRequest();
            var dupeModelParams = "command=checkDupeModel&serial="+serial+"&newModel="+newModel;
            dupeModelXML.open("POST", url, false);
            //Send the proper header information along with the request
            dupeModelXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            dupeModelXML.send(dupeModelParams);
            //alert("model: "+dupeModelXML.responseText);
            if(dupeModelXML.responseText == "false"){
                var editModelXML = new XMLHttpRequest();
                var editModelParams = "command=editModel&serial="+serial+"&newModel="+newModel;
                editModelXML.open("POST", url, false);
                editModelXML.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                editModelXML.send(editModelParams);
                outputMsg += "Model updated. "
                
                            
            }
            else{
                outputMsg += "Duplicate model. "
                
            }
            document.getElementById("logBox").value = outputMsg; 
            updateTableJS();
                            
        }

        
            





    </script>


    <div class="main">

        <div class="content">
            <!--filters -->
            <label for="fHWName">Hardware Serial:</label>
            <input type="text" id="fHWSN" name="fHWSN" oninput="updateTableJS()">
            <label for="fHWTID">Hardware Type ID:</label>
            <input type="text" id="fHWTID" name="fHWTID" oninput="updateTableJS()">
            <label for="fHWTID">Hardware Make:</label>
            <input type="text" id="fHWMk" name="fHWMk" oninput="updateTableJS()">
            <label for="fHWTID">Hardware Model:</label>
            <input type="text" id="fHWMod" name="fHWMod" oninput="updateTableJS()">
            <br><br>

            <button onclick="returnToDbSelect()">Back to database selection</button>
            <br>

            <div id='tableDiv'>


            </div>
            <!--empty editing div -->
            <div id='editDiv' style="visibility: hidden ;">
                <input type="text" id="eHWSN" name="eHWSN" readonly>
                <input type="text" id="eHWTID" name="eHWTID" readonly>
                <input type="text" id="eHWMk" name="eHWMk">
                <input type="text" id="eHWMod" name="eHWMod">
                <button onclick="submitChanges()">Submit change</button>
                


            </div>
            <input type="text" id="logBox" name="logBox" value="" readonly size="80">
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
