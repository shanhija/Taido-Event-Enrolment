<?php

$email = $_REQUEST["email"];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo "Invalid email address.";
	exit;
}

include 'database.php';

if ($mysqli->connect_errno) {
    echo("Connect failed: " .$mysqli->connect_error);
    exit;
}

// Make sure there are not SQL injects

$email = $mysqli->real_escape_string($email);


// Find if person already exists in the system.
$result  = query($mysqli, "SELECT personid, manager, nationality FROM tee_people WHERE email=\"".$email."\"");

$msg = '
Dear recipient,

You have requested login information. To login to the enrolment system, click on the following link:
http://www.taido.org/wtc2013/gate.php?page=welcome&loginid=';

if ($result->num_rows > 0) {
	// Database contains the email address
	$i = 0;	
	
	while ($person = $result->fetch_object()) {
		// Loop through all found records

		$id = md5("wtc2013" . date("F j, Y, g:i a") . $email . $person->personid);
		if ($i == 0) {
			$msg .= $id;
		}
		if ($person && $person->manager) {
			$msg .= '

You have been marked as a manager of '.$person->nationality.' enrolments. You can access the manager console by following the link:
http://www.taido.org/wtc2013/gate.php?page=manage&loginid='.$id;

		}
		query($mysqli, "UPDATE tee_people SET loginid=\"".$id."\" WHERE personid=".$person->personid);
		++$i;
	}
} else {
	$id = md5("wtc2013" . date("F j, Y, g:i a") . $email);
	$msg .= $id;
	query($mysqli, "INSERT INTO tee_people(email,loginid) VALUES (\"".$email."\",\"".$id."\")");	
}

$msg .='

If you did not receive this email on purpose, please contact stl@taido.fi

Sincerely yours,
WTC2013 web team
';

$headers = 'From: stl@taido.fi' . "\r\n" .
    'Reply-To: stl@taido.fi' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

if (!mail($email, "WTC2013 - enrollment login info", $msg, $headers)) {
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