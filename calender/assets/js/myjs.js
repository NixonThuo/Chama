// JavaScript Document

/*setInterval("my_function();",3000);
function delayer(){
	//window.location=location.href;
	window.location.assign("cit_prof.php");
	}
	*/
/*
setInterval("my_function();",5000);
function my_function(){
	function my_function(){
		$('#refresh').load(location.href);
		}
	}


function autorefresh_div(){
	$("#refresh").load("doctor_p.php");	
	}
setInterval("autorefresh_div();",5000);
*/

/*$(document).ready(function(e) {
  		$("#call").fadeToggle();
		$("#call").fadeToggle("slow");
  		$("#call").fadeToggle(5000);
		
});
*/
$(document).ready(function(e) {
  		$("#message").fadeToggle();
		$("#message").fadeToggle("slow");
  		$("#message").fadeToggle(5000);
		
});
$(document).ready(function(e) {
  		$("#messagereg").fadeToggle();
		$("#messagereg").fadeToggle("slow");
  		$("#messagereg").fadeToggle(5000);
		
});
$(document).ready(function(e) {
  		$("#message1").fadeToggle();
		$("#message1").fadeToggle("slow");
	// window.location = "../citizens/cit_prof.php";
});

//refreshing a div tag
$(document).ready(
function(){
  setInterval(function(){ 
   $('#phone').fadeToggle(); 
   $('#phone').fadeToggle("slow"); 
  }, 3000);
});

//refreshing welcome
$(document).ready(
function(){
  setInterval(function(){ 
   $('#welcome').fadeToggle(); 
   $('#welcome').fadeToggle("slow");
   	$("#welcome").fadeToggle(5000); 
  }, 3000);
});

//glow on focus when the first element is focussed
$(document).ready(function() {
function showfocus(){
		$("#reportedpers").fadeToggle();
		$("#reportedpers").fadeToggle("slow");
  		$("#reportedpers").fadeToggle(5000);	
}
});

//glow on focus when the first element is focussed
$(document).ready(function() {
function showfocus(){
		$("#missingcardNo").fadeToggle();
		$("#missingcardNo").fadeToggle("slow");
  		$("#missingcardNo").fadeToggle(5000);	
}
});

//refreshing div after reporting as found
$(document).ready(
function(){
  setInterval(function(){ 
   //function comes here
   $("#report").click(function(){
	   $("#reported").fadeToggle(); 
	   $("#reported").fadeToggle("fast"); 
	   });
  }, 3000);
});
mymissingperson
//refreshing reported missing person by user
$(document).ready(
function(){
  setInterval(function(){ 
   $('#mymissingperson').fadeToggle(); 
   $('#mymissingperson').fadeToggle(); 
  }, 3000);
});
//refresching the citizens home page
//function delayer(){
//window.location = "cit_prof.php";
//}

//validating weight
function myweight() {
    var x, text;

    // Get the value of the input field with id="numb"
    x = document.getElementById("weight").value;

    // If x is Not a Number or less than one or greater than 10
    if (isNaN(x) || x < 1 || x > 100) {
        text = "Maximum of 100 allowed";
    } else {
        text = "Input OK";
    }
    document.getElementById("demo").innerHTML = text;
}
//validating password
function password() {
    var username, text;

    // Get the value of the input field with id="numb"
    username = document.getElementById("user").value;

    // If x is Not a Number or less than one or greater than 10
    if (isNaN(username) || username < 1 || username > 6) {
        text = "Not allowed";
    } else {
        text = "Input OK";
    }
    document.getElementById("error").innerHTML = text;
}
//validationg fname
function validatefname() {
    var name, text;

    // Get the value of the input field with id="numb"
   name = document.getElementById("fname").value;
    // If x is Not a Number or less than one or greater than 10
    if (isNaN(name)) {
        text = "";
    } else {
        text = "Numbers Not allowed";
    }
    document.getElementById("failed").innerHTML = text;
}
//validating lname
function validatelname() {
    var name, text;

    // Get the value of the input field with id="numb"
   name = document.getElementById("lname").value;
    // If x is Not a Number or less than one or greater than 10
    if (isNaN(name)) {
        text = "";
    } else {
        text = "Numbers Not allowed";
    }
    document.getElementById("failedlname").innerHTML = text;
}
//validating username
function validateusername() {
    var name, text;

    // Get the value of the input field with id="numb"
   name = document.getElementById("patientuser").value;
    // If x is Not a Number or less than one or greater than 10
    if (!isNaN(name)) {
        text = "";
    } else {
        text = "Only numbers allowed!!!";
    }
    document.getElementById("failed").innerHTML = text;
}
function onleave() {
    document.getElementById("failed").innerHTML= " ";
}
//validate search
//validating username
function validatesearch() {
    var name, text;

    // Get the value of the input field with id="numb"
   name = document.getElementById("cardNo").value;
    // If x is Not a Number or less than one or greater than 10
    if (!isNaN(name)) {
        text = "not allowed!!!";
    } else {
        text = "";
    }
    document.getElementById("nomatch").innerHTML = text;
}
//channing the color of botton
$(document).ready(function(){
  $("#book_app").click(function(){
   $("#book_app").hide();
  });
}); 