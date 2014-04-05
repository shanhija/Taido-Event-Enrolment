<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<link href="css/tee.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.9.0.js"></script>
	<script type="text/javascript">
		$(function() {
			// Have to use AJAX to solve UTF8->native conversion automatically.
			$.ajax("gate.php", {
					dataType: "html",
					data: {
						loginid: "<? echo $person->loginid;?>",
						page: "report"
					},
					type: 'GET',
			})
			.done(function(response) {
				$("#report").html(response);
			})
			.fail(function(jqXHR, type, errorObj) {
				$("#report").html(type + "<br />"+errorObj);
			});
		});
	</script>

    <title><?php $str_event_short ?> Enrollment - Summary</title>
</head>
<body>
<div class="wrapper">
	<div class="logo"></div>
	<h1>Thank you for enrolling to the <?php $str_event_name ?> event!</h1>
	
	Below you will find a summary of your choices.

	<pre id="report">
	</pre>
	
	<b>Any corrections to the enrollment should be sent by email to <a href="mailto:<?php $contact_email ?>"><?php $contact_email ?></a>.</b>
</div>			
</body>
</html>