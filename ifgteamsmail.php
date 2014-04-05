<?php 

// $INDATA, $person and $mysqli are made available by gate.php

$personResult = $mysqli->query("
	SELECT 
		*
	FROM tee_people 
	WHERE status='Submitted' AND personid IN 
		(SELECT 
			personid 
		FROM tee_events 
		WHERE event IN ('B19', 'B20', 'B21', 'B22'))") or die($mysqli->error);

while ($person = $personResult->fetch_object()) {

	// if (strcmp($person->email, "sami.hanhijarvi@iki.fi"))
		// continue;
	
	if (!strpos($person->email, "@"))
		// Not an email address.
		return;
	
	ob_start();
	include "report.php";
	$report = ob_get_contents();
	ob_end_clean();	
	
	$message =
'Dear '. $person->firstName . ' ' . $person->lastName .',
	
As you might have noticed, the enrollment system had problems when enrolling teams to the International Friendship Games. Because of this, some of the teams were not recorded in our system. We kindly ask you recheck the summary of your enrollment, especially the IFG teams, which has been included at the end of this email.
 
If there are any problems in any of your teams, their names or compositions, we would like to ask for the team leader to send us complete information about the correct team to ' . $contact_email . ', including the name, event (dantai hokei, dantai jissen men, dantai jissen women, tenkai), leader, players and reserves. Note also that if you have enrolled in a team event but there is no corresponding team in the summary, or no IFG teams at all, you have not been selected to a team according to the system. This is likely caused by the problem in the system. If this is the case, please request the team leader to provide us with the complete information about your team as described above. 
 
We are truly sorry for the errors in the system and the inconvenience it has caused.
 
Sincerely yours,
' . $str_event_short . ' web team

-----------------

'.$report;

	$topic = $str_event_short + " - IFG teams";
	$headers = array(
		'From: ' . $contact_email,
        'Content-type: text/plain; charset="UTF-8";',		
//		'Content-type: text/plain; charset=iso-8859-1',	
		'Reply-To: ' . $contact_email,
		'X-Mailer: PHP/' . phpversion());	
	
	if (!mail($person->email, $topic, $message, implode("\r\n", $headers))) {
		$error = error_get_last();
		echo "Send unsuccessfull: " + $error["message"];
		exit;
	}	else {
		echo "Email sent to: " . $person->firstName ." ".$person->lastName ." <".$person->email.">\n";
	}
}

/// CUSTOM FUNCTIONS
?>