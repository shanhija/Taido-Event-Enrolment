<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.9.0.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.0.custom.min.js"></script>
	<link href="css/jquery-ui-1.10.1.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="js/tee_welcome.js"></script>		
	<link href="css/tee.css" rel="stylesheet" type="text/css" />

    <title><?php $str_event_short ?> Enrollment - Welcome</title>
</head>
<body>

<div class="wrapper">

	<div class="logo"></div>
	
	<div class="page">
	
		<h1>Welcome to the <?php $str_event_name ?> enrollment system!</h1>
		<p>	
			Enrollment to the <?php $str_event_short ?> event is handled through this system. You will be guided through the enrollment 
            process, gathering information about your participation to the event. At each step, please read carefully the directions to 
            understand your choices and to input as accurate information as possible.
		</p>			
		<p>
			When you have entered all information and checked it, you may submit your enrollment. Submission is open 
            until <?php $submission_deadline ?>. Before the deadline is over, you may revisit your enrollment, change it 
            if necessary, and resubmit. You may also retract your submission before the end of the deadline. 
		</p>
		<p>
			After the submission deadline is over, the national representatives will go through all enrollment information. 
			The national representatives will also handle the billing of the participation fees.
		</p>
		<p>
			Competitors and staff for <?php $str_event_short ?> are assigned by the national representatives, and the enrollment forms 
			are updated to match the assignments. The national representatives also select the competitors for each event 
            of <?php $str_event_short ?> and builds the corresponding teams.
		</p>
		<p>
			If you have any questions, please send email to <a href="mailto:<?php $contact_email ?>"><?php $contact_email ?></a>.
		</p>
		
	</div>
	
	<div class="registerinfo">
		<div id="proceedButton">Proceed</div>
	</div>
</div>
		
</body>
</html>