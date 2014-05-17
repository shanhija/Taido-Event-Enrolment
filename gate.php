<?
if (!strcmp($_SERVER['REQUEST_METHOD'],'POST'))
	$INDATA = $_POST;
else 
	$INDATA = $_GET;

include 'settings.php';
include 'database.php';
session_start();

if (array_key_exists("person", $_SESSION)) {
	$person = $_SESSION["person"];
} else {
	$id = $INDATA["loginid"];
	$id = $mysqli->real_escape_string($id);
	$result = $mysqli->query("SELECT * FROM tee_people WHERE loginid=\"".$id."\"") or die($mysqli->error);
	$person = $result->fetch_object();
	
	if (!$person) {
    ?>
<html>
<head>
    <script type="text/javascript">
	    var seconds = 10;
	    setInterval(function() {
		    --seconds;
		    document.getElementById("remaining").innerHTML = seconds;
		    if (seconds == 0)
			    window.location.href= '.';			
	    }, 1000);
    </script>
    <title></title>
</head>
<body>
	Your login information could not be found. Please request new login information from the register page. Redirecting in <div id="remaining" style="display: inline;">10</div> seconds...
</body>
</html>
	<?
		exit();
	}
}

// error_reporting(E_ALL);
// ini_set("display_errors", 1);	
	
if (array_key_exists("page", $INDATA)) {
	// Request for a page
	$page = $INDATA["page"];

	switch($page) {
		case "manage":
			if ($person->manager >= 1) {
				include 'manage.php';  
			} else {
				echo "You do not have permission to acces this page. Please contact " + $contact_email + ".";
			}
			break;
		case "managereport":
			if ($person->manager >= 1) {
				include 'managereport.php';  
			} else {
				echo "You do not have permission to acces this page. Please contact " + $contact_email + ".";
			}
			break;
		case "enroll":
			include 'enroll.php';  
			break;
		case "report":
			include 'report.php';
			break;
		case "ifgteams":
			include 'ifgteamsmail.php';
			break;
		case "welcome":
			include 'welcome.php';
			break;
		case "showreport":
			include 'showreport.php';
			break;
		default:
			echo "requested page not found.";
	}
} elseif (array_key_exists("operation", $INDATA)) {
	// Request a database operation	
	include 'interface.php';
} else {
	include 'welcome.php';
	break;
}

?>