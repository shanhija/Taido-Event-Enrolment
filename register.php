<?php

$email = $_REQUEST["email"];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo "Invalid email address.";
	exit;
}

include 'settings.php';
include 'database.php';

if ($mysqli->connect_errno) {
    echo("Connect failed: " .$mysqli->connect_error);
    exit;
}

// Make sure there are not SQL injects

$email = $mysqli->real_escape_string($email);


// Find if person already exists in the system.
$result  = query($mysqli, "SELECT personid, manager, nationality FROM tee_people WHERE email=\"".$email."\"");

$msg = "
Dear recipient,

You have requested login information. To login to the enrollment system, click on the following link:
" . $enrollment_base_url . "gate.php?page=welcome&loginid=";

if ($result->num_rows > 0) {
	// Database contains the email address
	$i = 0;	
	
	while ($person = $result->fetch_object()) {
		// Loop through all found records

		$id = md5($str_event_short . date("F j, Y, g:i a") . $email . $person->personid);
		if ($i == 0) {
			$msg .= $id;
		}
		if ($person && $person->manager) {
			$msg .= "

You have been marked as a manager of ".$person->nationality." enrolments. You can access the manager console by following the link:
".$enrollment_base_url."gate.php?page=manage&loginid=".$id;

		}
		query($mysqli, "UPDATE tee_people SET loginid=\"".$id."\" WHERE personid=".$person->personid);
		++$i;
	}
} else {
	$id = md5($str_event_short . date("F j, Y, g:i a") . $email);
	$msg .= $id;
	query($mysqli, "INSERT INTO tee_people(email,loginid) VALUES (\"".$email."\",\"".$id."\")");	
}

$msg .="

If you did not receive this email on purpose, please contact ".$contact_email."

Sincerely yours,
".$str_event_short." web team
";

$headers = 'From: ' . $contact_email . "\r\n" .
    'Reply-To: ' . $contact_email . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

if (!mail($email, $str_event_short . " - enrollment login info", $msg, $headers)) {
	$error = error_get_last();
	echo "Send unsuccessfull: " + $error["message"];
	exit;
}

echo "true";

function query($mysqli, $query) {
	$result = $mysqli->query($query);
	if (!$result) {
		echo($mysqli->error .":". $query);
		exit();
	}		
	return $result;
}
?>