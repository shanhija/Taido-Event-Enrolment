<?php

// $INDATA, $person and $mysqli are made available by the calling script
	
if (array_key_exists("actor", $INDATA)) {
	// Someone is acting as the person in personid
	$actor = $INDATA["actor"];
} else {
	// The actor is the person in personid
	$actor = $person->personid;
}	

// Fetch person information
$result = $mysqli->query("SELECT * FROM tee_people WHERE personid=".$person->personid) or die($mysqli->error);
$person = $result->fetch_object();

// Fetch event (boolean) information
$result = $mysqli->query("SELECT event FROM tee_events WHERE personid=".$person->personid);
$events = array();
$ifgEvents = 0;
$wtcEvents = 0;
$hotelNights = 0;
while ($row = $result->fetch_object()) {
	$events[] = $row->event;

	if ($row->event[0] == 'A') {
		++$wtcEvents;
	} elseif ($row->event[0] == 'B') {
		++$ifgEvents;
	}
}

// Fetch variable information
$result = $mysqli->query("SELECT variable, value FROM tee_variables WHERE personid=".$person->personid);
$variables = array();
while ($row = $result->fetch_object()) {
	$variables[$row->variable] = $row->value;
}

$totalCost = 0;

?>
Personal information
	Name						<? echo $person->firstName . " ". $person->lastName; 
	if (!strcmp($person->nationality,"japanese"))
		echo " (". $person->firstNameGanji . " ". $person->lastNameGanji. ")";?>
	
	Nationality					<? echo $person->nationality; ?>
	
	Birthday						<? echo $person->birthDay.'.'.$person->birthMonth.'.'.$person->birthYear;?>
	
	Sex							<? echo $person->sex;?>
	
	Local association / Dojo		<? echo $variables["dojo"];?>
	
	Taido rank					<? 
	if ($person->taidoRank < 0)
		echo (-$person->taidoRank).' kyu';
	elseif ($person->taidoRank > 0)
		echo $person->taidoRank . ' dan';
	else
		echo "Don't practice";
	if (in_array("renshi", $events)) { ?>
	
	Renshi						Yes<?
	}
	if (in_array("wtcCompetitor", $events)) { ?>
	
	Role						WTC Competitor<?
	}
	if (in_array("staff", $events)) { ?>
	
	Role						Staff<?
	}
	?>

	Package						<? 
	if (strlen($person->package) < 1) {
		echo "none";
	} else {
		echo $person->package;
		switch ($person->package) {
		case "WTC Competitor": $totalCost += 185; break;
		case "Tourist": $totalCost += ($person->birthYear >= 2001)?145:155; break;
		case "Judge": $totalCost += 115; break;
		case "Staff": $totalCost += 150; break;
		}		
		if (array_key_exists("diet", $variables)) { ?>

	Diet							<?
			echo $variables["diet"];	
		}
		if (array_key_exists("tshirt", $variables)) { ?>

	T-shirt size					<?
			echo $variables["tshirt"];	
		}
	}?>
	

<?

if ($ifgEvents) {?>
International Friendship Games events
<?
$totalCost += 30;
if (in_array("B1", $events)) { echo "	B1 Hokei, men, <=1997, >=2 kyu, tai or in hokei only \n"; }
if (in_array("B2", $events)) { echo "	B2 Hokei, women, <=1997, >=2 kyu, tai or in hokei only \n"; }
if (in_array("B3", $events)) { echo "	B3 Jissen, men, <=1995, >=2 kyu\n"; }
if (in_array("B4", $events)) { echo "	B4 Jissen, women, <=1995, >=2 kyu\n"; }
if (in_array("B5", $events)) { echo "	B5 Hokei, men, <=1997, 6-3 kyu, tai or in hokei: only sen, un, hen \n"; }
if (in_array("B6", $events)) { echo "	B6 Hokei, women, <=1997, 6-3 kyu, tai or in hokei: only sen, un, hen \n"; }
if (in_array("B7", $events)) { echo "	B7 Jissen, men, <=1995, 6-3 kyu\n"; }
if (in_array("B8", $events)) { echo "	B8 Jissen, women, <=1995, 6-3 kyu \n"; }
if (in_array("B9", $events)) { echo "	B9 Sonen hokei, mixed, <=1978, >=6 kyu, tai, in or sei hokei only \n"; }
if (in_array("B10", $events)) { echo "	B10 Sonen hokei, mixed, <=1978, >=6 kyu, mei hokei only \n"; }
if (in_array("B11", $events)) { echo "	B11 Sonen jissen, men, <=1978, >=2 kyu\n"; }
if (in_array("B12", $events)) { echo "	B12 Sonen jissen, women, <=1978, >=2 kyu\n"; }
if (in_array("B13", $events)) { echo "	B13 Junior hokei, mixed, >=2001, no belt limitation, tai or in hokei: only sen, un, hen \n"; }
if (in_array("B14", $events)) { echo "	B14 Junior hokei, mixed, 1998-2000, no belt limitation, tai or in hokei: only sen, un, hen\n"; }
if (in_array("B15", $events)) { echo "	B15 Jissen, boys, 1999-2001, >=6 kyu, tai or in hokei: only sen, un, hen\n"; }
if (in_array("B16", $events)) { echo "	B16 Jissen, girls, 1999-2001, >=6 kyu, tai or in hokei: only sen, un, hen\n"; }
if (in_array("B17", $events)) { echo "	B17 Jissen, boys, 1996-1998, >=6 kyu\n"; }
if (in_array("B18", $events)) { echo "	B18 Jissen, girls, 1996-1998, >=6 kyu\n"; }
if (in_array("B19", $events)) { echo "	B19 Dantai hokei, mixed, <=2003, no belt limitation, team: 5 competitors, tai or in hokei only\n"; }
if (in_array("B20", $events)) { echo "	B20 Dantai jissen, men, <=1995, >= 2 kyu, team: 5 competitors and leader\n"; }
if (in_array("B21", $events)) { echo "	B21 Dantai jissen, women, <=1995, >= 2 kyu, team: 5 competitors and leader\n"; }
if (in_array("B22", $events)) { echo "	B22 Tenkai, mixed, <=2003, no belt limitation, team: 6 competitors \n"; }
if (in_array("B23", $events)) { echo "	B23 Taido trick-track for Juniors, >=2001, no belt limitation, rules and directions will be given later\n"; }
echo "\n";
$teamsResult = $mysqli->query("SELECT * FROM tee_teams JOIN tee_players WHERE tee_players.teamid=tee_teams.teamid AND tee_players.personid=".$person->personid) or die($mysqli->error);
if ($teamsResult->num_rows) {
?>
International Friendship Games teams
<?
	$teamEvents = array("B19", "B20", "B21", "B22");
	while ($team = $teamsResult->fetch_object()) {
		switch ($team->event) {
		case "B19": echo "\tB19 Dantai hokei"; break;
		case "B20": echo "\tB20 Dantai jissen, men"; break;
		case "B21": echo "\tB21 Dantai jissen, women"; break;
		case "B22": echo "\tB22 Tenkai"; break;		
		}	
		echo ": ". $team->name ."\n";
//		echo "\t". $team->name." (".$team->event .")\n";
		$result = $mysqli->query("SELECT concat(firstName, ' ', lastName) AS name, position FROM tee_people JOIN tee_players ON tee_people.personid=tee_players.personid WHERE tee_players.teamid=".$team->teamid ." ORDER BY position") or die($mysqli->error);
		while ($row = $result->fetch_object()) {
			switch ($row->position[0]) {
				case 'l': echo "\t\tLeader\t"; break;
				case 'p': echo "\t\tPlayer ". $row->position[1]."\t"; break;
				case 'r': echo "\t\tReserve ". $row->position[1]."\t"; break;
				default: continue;
			}
			echo $row->name . "\n";
		}
	}
	echo "\n";
}

}

if ($wtcEvents) {
}

if (array_key_exists("ifgJudge",$variables) && strcmp($variables["ifgJudge"],"yes")==0) {
	// Judge duty?>
Judge
	<? 
		if (array_key_exists("wtcJudge",$variables)) echo "World Taido Championships, ";
		echo "International Friendship Games\n";?>
	National seminars				<? echo $variables["judgeNationalSeminars"]; ?>	
	International seminars			<? echo $variables["judgeInternationalSeminars"]; ?>	
	National championships		<? echo $variables["judgeNationalCount"]; ?>	
	Friendship games				<? echo $variables["judgeIFGCount"]; ?>	
	European championships		<? echo $variables["judgeECCount"]; ?>	
	World championships			<? echo $variables["judgeWCCount"]; ?>	
	Will complete 4 dan			<? echo $variables["willComplete4dan"]; ?>		
	
<?
}	

$volunteerCount = 0;
foreach ($variables as $key => $value) {
	if (strcmp(substr($key, 0, 9), "volunteer")==0 && $value) {
		++$volunteerCount;
	}
}

if ($volunteerCount > 0) {
?>
Volunteer
<? 
if (array_key_exists("volunteerTatami307",$variables) &&  $variables["volunteerTatami307"]) { echo "	Carrying tatamis Tue 30.7.\n";}
if (array_key_exists("volunteerTatami028",$variables) &&  $variables["volunteerTatami028"]) { echo "	Carrying tatamis Fri 2.8. evening\n";}
if (array_key_exists("volunteerTatami048",$variables) &&  $variables["volunteerTatami048"]) { echo "	Carrying tatamis Sun 4.8.\n";}
if (array_key_exists("volunteerKiosk018",$variables) &&  $variables["volunteerKiosk018"]) { echo "	Kiosk clerk Thu 1.8.\n";}
if (array_key_exists("volunteerKiosk028",$variables) &&  $variables["volunteerKiosk028"]) { echo "	Kiosk clerk Fri 2.8.\n";}
if (array_key_exists("volunteerKiosk038",$variables) &&  $variables["volunteerKiosk038"]) { echo "	Kiosk clerk Sat 3.8.\n";}
if (array_key_exists("volunteerSecurity028",$variables) &&  $variables["volunteerSecurity028"]) { echo "	Security officer Fri 2.8.\n";}
if (array_key_exists("volunteerSecurity038",$variables) &&  $variables["volunteerSecurity038"]) { echo "	Security officer Sat 3.8.\n";}
if (array_key_exists("volunteerIT038",$variables) &&  $variables["volunteerIT038"]) { echo "	Technical help Sat 3.8.\n";}
echo "\n";
}

$hotelResult = $mysqli->query("SELECT * FROM tee_hotel WHERE personid=".$person->personid) or die($mysqli->error);
if ($hotel = $hotelResult->fetch_object()) {
?>
Hotel
	Days						<?	
	$nights = "";
	$nrnights = 0;
	$isStaff = strcmp($person->package,"Staff") == 0;
	if ($hotel->nights[0] == '1') { ++$nrnights; $nights .= "Tue 30.7., ";}
	if ($hotel->nights[1] == '1') { ++$nrnights; $nights .= "Wed 31.7., ";}
	if ($hotel->nights[2] == '1' || $isStaff) { ++$nrnights; $nights .= "Thu 1.8., ";}
	if ($hotel->nights[3] == '1' || $isStaff) { ++$nrnights; $nights .= "Fri 2.8., ";}
	if ($hotel->nights[4] == '1' || $isStaff) { ++$nrnights; $nights .= "Sat 3.8., ";}
	if ($hotel->nights[5] == '1') { ++$nrnights; $nights .= "Sun 4.8., ";}

	$payNights = $nrnights - (($isStaff)?3:0);
	$accompany = true;
	switch ($hotel->type) 
	{
		case "Standard Single": $totalCost += $payNights * 95; $accompany = false; break;
		case "Standard Double": $totalCost += $payNights * 50; break;
		case "Superior": $totalCost += $payNights * 70; break;
		case "Junior Suite": $totalCost += $payNights * 90; break;
	}	
	
	echo substr($nights,0,-2); ?>
	
	Type						<? echo $hotel->type; ?>
	
<?	if ($accompany) { ?>
	Request for room mate			<? echo ($hotel->accompany)?$hotel->accompany:"none"; ?>
	
	Separate beds				<? echo ($variables["separateBeds"])?"Yes":"No"; ?>
<?	} ?>
	
	Address						<? echo $hotel->address; ?>
	
	Passport number				<? echo $hotel->passportNumber; ?>
	
	Additional information			<? echo $hotel->additional; ?>
	
	
<?
}

$optionals = array("helsinkiTourThursday", "helsinkiTourWednesday", "hikingTour", "optionalBanquette", "optionalWTCticket", "porvooTour", "tallinTour", "ultimateSauna");
$hasOptionals = false;
foreach ($optionals as $key) {
	if (array_key_exists($key, $variables)) {
		$hasOptionals = true;
		break;
	}
}

if ($hasOptionals) {
?>
Optional services
<?
if (array_key_exists("optionalBanquette",$variables) &&  $variables["optionalBanquette"]) { echo "	Banquette Sat 3.8.\n"; $totalCost += 50;}
if (array_key_exists("optionalWTCticket",$variables) &&  $variables["optionalWTCticket"]) { echo "	WTC ticket Sat 3.8.\n"; $totalCost += 20;}
if (array_key_exists("ultimateSauna",$variables) &&  $variables["ultimateSauna"]) { echo "	Ultimate Sauna Experience Thu 1.8.\n"; $totalCost += 35;}
if (array_key_exists("hikingTour",$variables) &&  $variables["hikingTour"]) { echo "	Hiking in Nuuksio Sun 5.8.\n"; $totalCost += 70;}
if (array_key_exists("helsinkiTourWednesday",$variables) &&  $variables["helsinkiTourWednesday"]) { echo "	Sightseeing Helsinki Wed 30.7.\n"; $totalCost += 35;}
if (array_key_exists("helsinkiTourThursday",$variables) &&  $variables["helsinkiTourThursday"]) { echo "	Sightseeing Helsinki Thu 1.8.\n"; $totalCost += 35;}
if (array_key_exists("tallinTour",$variables) &&  $variables["tallinTour"]) { echo "	Day trip to Tallinn, Estonia\n"; $totalCost += 70;}
if (array_key_exists("porvooTour",$variables) &&  $variables["porvooTour"]) { echo "	Sightseeing Porvoo 5.8.\n"; $totalCost += 70;}	
}
?>

Total estimated cost				<? echo $totalCost;?> €
