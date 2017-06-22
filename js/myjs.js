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

$(document).ready(function(e) {
  		$("#call").fadeToggle();
		$("#call").fadeToggle("slow");
  		$("#call").fadeToggle(5000);
		
});

$(document).ready(function(e) {
  		$("#failed_login").fadeToggle();
		$("#failed_login").fadeToggle("slow");
  		$("#failed_login").fadeToggle(5000);
		
});
$(document).ready(function(e) {
  		$("#integerNotAllowed").fadeToggle();
		$("#integerNotAllowed").fadeToggle("slow");
  		$("#integerNotAllowed").fadeToggle(5000);
		
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
   $('#phone').css("color","#F0F"); 
  }, 3000);
});

//refreshing the reported cases
$(document).ready(
function(){
  setInterval(function(){ 
   $('#found_persons').text(); 
   $('#found_persons').text();
  }, 3000);
});

//picker calender
 $(document).ready(function() { 
 $("#datepicker").datepicker({dateFormat:'yy/mm/dd'}); 
 }); 
//glow on focus when the first element is focussed
$(document).ready(function() {
function showfocus(){
		$("#reportedpers").fadeToggle();
		$("#reportedpers").fadeToggle("slow");
  		$("#reportedpers").fadeToggle(5000);	
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

//refreshing reported missing person by user
$(document).ready(
function(){
  setInterval(function(){ 
   $('#mymissingperson').fadeToggle(); 
   $('#mymissingperson').fadeToggle(); 
  }, 3000);
});

//validating password entry
//validating lname
function validatepass() {
   var pass1, pass2 ,text;

    // Get the value of the input field with id="numb"
   pass1 = document.getElementById("password").value;
   pass2 = document.getElementById("pass").value;
    // If x is Not a Number or less than one or greater than 10
    if (pass1 !==pass2) {
        text = "password missmatch";
	}else{
		text = "Match!";	
	}
    document.getElementById("passvalidate").innerHTML=text; 
}
function onleave() {
	document.getElementById("passvalidate").innerHTML=text; 
}
//refresching the citizens home page
//function delayer(){
//window.location = "cit_prof.php";
//}
