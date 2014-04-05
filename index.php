<?php
include "settings.php"; 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.9.0.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.0.custom.min.js"></script>
	<link href="css/jquery-ui-1.10.1.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="js/tee_register.js"></script>		
	<link href="css/tee.css" rel="stylesheet" type="text/css" />
    
    <title><?php echo $str_event_short; ?> Enrollment - Start</title>
</head>
<body>

<div class="wrapper">

	<div class="logo"></div>
	
	<div class="page">
	
		<h1><?php echo $str_event_name; ?> Enrollment System Registration</h1>
		<p>
            Registering to enrollment system happens in two steps:
		</p>
		<ol>
			<li>Enter your email address here and press submit. The system will send an email to your address.</li>
			<li>Open the sent email and follow the link contained in the email. You will be identified by your email address and it's validity is confirmed by you following the link.</li>
		</ol>			
		<br />

		<p>
			If you have already registered but need the login email to be sent again, you may use the above procedure to retrieve another login 
            email. You may continue from where you left off. Note that you must use the same email address as before, since you are identified by 
            your address.
		</p>
		
		<h2>Registeration has now ended!</h2>
<!--
	<div class="registerinfo" id="loginForm">
		<div class="title">Email address</div>
		<div id="emailaddress">
			<input type="text" id="email" name="email">
		</div>
		<div id="submitLogin">Submit</div>
	</div>
	<div class="registerinfo" id="result">
		<div class="title" id="resultTitle"></div>
		<div class="details" id="resultDetails">
		</div>
	</div>
-->
	</div>
</div>
    		
<div id="log"></div>

</body>
</html>