let callStartTime;
let timeInterval;


//Starts a call
function startCall() {
  let callBtn = document.getElementsByClassName("call")[0];
  let callStatus = document.getElementById("callStatus");
  //Open the popup menu for start call
  openPopup("startCallPopup.php", function () {
    //Preventing an error with call duration by disabling cancel button
    setTimeout(function () {
      document.getElementById("cancelCall").disabled = false;
    }, 2000);
  });

  //Setting the start date and time in the session
  post("setCallData.php?data=date&set=true", function (response) {
    timeInterval = setTimeout(showCallTime, 1000);
  });
  callStartTime = Date.now();
  callBtn.classList.add("on");
  callBtn.setAttribute("onclick", "openPopup('endCallPopup.php')");
  fadeOut(callStatus, function () {
    callStatus.innerHTML = "Call started";
    fadeIn(callStatus);
  });
}

//Showing the duration of the call
function showCallTime() {
  let callStatus = document.getElementById("callStatus");
  fadeOut(callStatus, function () {
    fadeIn(callStatus);
    updateCallTime();
    //Updating the duration every second
    timeInterval = setInterval(updateCallTime, 1000);
  });
}

//Calculating and displaying the current call duration
function updateCallTime() {
  let callStatus = document.getElementById("callStatus");
  let now = Date.now() - callStartTime;
  let mins = Math.floor(now / 60000);
  let secs = Math.floor((now - mins * 60000) / 1000);

  //String formatting
  if (mins < 10) {
    mins = "0" + mins;
  }
  if (secs < 10) {
    secs = "0" + secs;
  }

  callStatus.innerHTML = mins + ":" + secs;
}

//Ending the call and saving call data
function endCall() {
  let callStatus = document.getElementById("callStatus");
  let callBtn = document.getElementsByClassName("call")[0];
  //Getting call duration and resetting values
  clearInterval(timeInterval);
  let callDuration = "00:" + callStatus.innerHTML;
  callStartTime = undefined;
  callBtn.classList.add("on");
  callBtn.setAttribute("onclick", "startCall()");
  fadeOut(callStatus, function () {
    callStatus.innerHTML = "Call ended";
    fadeIn(callStatus);
    setTimeout(resetCallStatus, 1000);
  });

  //Creating post request
  let request =
    "setCallData.php?data=endCall&reason=" +
    document.getElementById("callReason").value +
    "&duration=" +
    callDuration;
  post(request, function () {
    callBtn.classList.remove("on");
    callBtn.onclick = startCall;
    closePopup();
    LoadHome();
  });
}

//Cancelling the call
function cancelCall() {
  let callStatus = document.getElementById("callStatus");
  let callBtn = document.getElementsByClassName("call")[0];
  //Resetting data
  clearInterval(timeInterval);
  callStartTime = undefined;
  callBtn.classList.remove("on");
  closePopup();
  fadeOut(callStatus, function () {
    callStatus.innerHTML = "Call cancelled";
    fadeIn(callStatus);
    setTimeout(resetCallStatus, 1000);
  });

  //Clearing start date saved in php session
  post("setCallData.php?data=date&set=true", function () {
    callBtn.setAttribute("onclick", "startCall()");
  });
}

//Resets the call button text to start call
function resetCallStatus() {
  let callStatus = document.getElementById("callStatus");
  fadeOut(callStatus, function () {
    callStatus.innerHTML = "Start call";
    fadeIn(callStatus);
  });
}

//Opens the popup window with the given file
function openPopup(popupLoc, onComplete) {
  let popup = document.getElementById("popup");
  let popupCon = document.getElementById("popupCon");
  fadeIn(popupCon);
  //Getting the popup html
  post(popupLoc, function (response) {
    popup.innerHTML = response;
    if (onComplete) {
      onComplete(response); //Callback function
    }
  });
}

//Closing the popup and clearing the html
function closePopup() {
  let popup = document.getElementById("popup");
  let popupCon = document.getElementById("popupCon");
  fadeOut(popupCon, function () {
    popup.innerHTML = "";
  });
}

//AJAX post function with callback
function post(url, onComplete) {
  var ajax = new XMLHttpRequest();
  //Setting the callback function
  ajax.onreadystatechange = function () {
    if (ajax.readyState == XMLHttpRequest.DONE && ajax.status == 200) {
      var response = ajax.responseText;
      if (onComplete) {
        onComplete(response);
      }
    }
  };
  //Making the request
  ajax.open("POST", url, true);
  ajax.send();
}

//Getting list of names for start call popup
function getNames() {
  let request =
    "findCaller.php?data=names&department=" +
    document.getElementById("callerDept").value;
  post(request, function (response) {
    let names = document.getElementById("callerName");
    names.innerHTML = response;
    names.parentElement.style.display = "inline-block";
  });
}

//Setting caller id when a name is selected
function fillId() {
  let id = document.getElementById("callerId");
  let name = document.getElementById("callerName");
  id.value = name.value;
}

//Saving initial data for the current call
function submitCaller() {
  let id = document.getElementById("callerId");
  let args = "data=id&id=" + id.value;
  //Checking if ID is valid
  post("findCaller.php?" + args, function (response) {
    if (response == "true") {
      //Saving data to database
      post("setCallData.php?" + args, closePopup());
    } else {
      let errorP = document.getElementById("errorP");
      errorP.style.display = "inline-block";
    }
  });
}

//Fade an element in
function fadeIn(elem, onComplete) {
  elem.style.opacity = 0;
  elem.style.filter = "alpha(opacity=0)";
  elem.style.display = "inline-block";
  elem.style.visibility = "visible";

  var opacity = 0;
  //Increase opacity every 50ms until fully visable
  var timer = setInterval(function () {
    opacity += 50 / 300;
    if (opacity >= 1) {
      clearInterval(timer);
      opacity = 1;
      if (onComplete) onComplete();
    }
    elem.style.opacity = opacity;
    elem.style.filter = "alpha(opacity=" + opacity * 100 + ")";
  }, 50);
}

//Fade an element out 
function fadeOut(elem, onComplete) {
  var opacity = 1;
  var timer = setInterval(function () {
    opacity -= 50 / 300;
    //Decrease opacity every 50ms until fully visable
    if (opacity <= 0) {
      clearInterval(timer);
      opacity = 0;
      elem.style.display = "none";
      elem.style.visibility = "hidden";
      if (onComplete) onComplete();
    }
    elem.style.opacity = opacity;
    elem.style.filter = "alpha(opacity=" + opacity * 100 + ")";
  }, 50);
}
