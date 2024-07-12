// JavaScript Document

//If Doc is rdy hide form on page load
var l = 0;
var r = 0;

$(document).ready(function(){
$(".loginForm").hide();
$("#iframeReg").css("display", "none");
//If click Login Container then show Login Form
$("#loginContainer").click(function(){
	if (r > 0) {
	$("#loginTitle").css("font-size", "8em");
	$("#loginTitle").css("padding-top", "10px");
	$("#loginContainer").css("background-color", "#3F51B5");
	$("#registerTitle").show();
	$("#registerImg").show();
	}
	$(".registerForm").hide();
	$(".loginForm").fadeIn(3000);
	$("#loginTitle").hide();
	$("#loginImg").hide();
	$("#registerContainer").css("width", "15%");
	$("#registerContainer").css("left", "85%");
	$("#loginContainer").css("width", "85%");
	$("#registerTitle").css("font-size", "3em");
	$("#registerTitle").css("padding-top", "30px");
	$("#registerContainer").css("background-color", "#1C1C1C");
	
	l = (l + 1);
	});
});


// JavaScript Document

//If Doc is rdy hide form on page load
$(document).ready(function(){
//If click Login Container then show Login Form
$("#registerContainer").click(function(){
	if(l > 0) {
	$("#registerTitle").css("font-size", "8em");
	$("#registerTitle").css("padding-top", "10px");
	$("#registerContainer").css("background-color", "#303F9F");
	$("#loginTitle").show();
	$("#loginImg").show();
	}
	$("#iframeReg").css("display", "inline");
	$(".loginForm").hide();
	$("#registerTitle").hide();
	$("#registerImg").hide();
	$("#loginContainer").css("width", "15%");
	$("#registerContainer").css("left", "15%");
	$("#registerContainer").css("width", "85%");
	$("#loginTitle").css("font-size", "3em");
	$("#loginTitle").css("padding-top", "30px");
	$("#registerTitle").css("display", "none");
	$("#loginContainer").css("background-color", "#1C1C1C");
	r = r + 1;
	
	});
});

