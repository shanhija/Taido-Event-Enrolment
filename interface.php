<?php 

// $INDATA, $person and $mysqli are made available by gate.php

if (array_key_exists("actor", $INDATA)) {
	// Someone is acting as the person in personid
	$result = $mysqli->query("SELECT * FROM tee_people WHERE loginid=\"".$INDATA["actor"]."\"") or die($mysqli->error);
	$actor = $result->fetch_object();
} else {
	// The actor is the person in personid
	$actor = $person;
}

switch($INDATA['operation']) {

case "teamname":
	$query = "SELECT teamid FROM tee_teams WHERE".
		" event=\"".$mysqli->real_escape_string($INDATA["event"])."\" AND ".
		" name LIKE \"%". $mysqli->real_escape_string(urldecode($INDATA["name"])) ."%\"";
	if (array_key_exists("teamid", $INDATA)) {
		$query .= " AND teamid != ". $mysqli->real_escape_string($INDATA["teamid"]);
	}
	$result = $mysqli->query($query) or die($mysqli->error . ":" . $query);
	echo $result->num_rows; 	
	break;

case "players": 
	
	$query = "SELECT CONCAT(tee_people.firstName, ' ',tee_people.lastName) AS value, tee_people.personid AS personid FROM tee_people JOIN tee_events WHERE tee_people.personid=tee_events.personid AND tee_events.event='".$mysqli->real_escape_string($INDATA["event"])."' AND CONCAT(tee_people.firstName,' ', tee_people.lastName) LIKE '%".$mysqli->real_escape_string($INDATA["term"])."%';";
	$result = $mysqli->query($query) or die($mysqli->error);
	
	$response = array();	
	$hasCurrent = false;
	while ($obj = $result->fetch_object()) {
		$response[] = $obj;
		$hasCurrent |= ($obj->personid == $person->personid);
	}
	if (!$hasCurrent) {
		$name = $person->firstName. ' '. $person->lastName;
		if (!(stripos($name,$INDATA["term"]) === false)) {
			$response[] = array('value' => $name, 'personid' => $person->personid);
		}
	}
	echo json_encode($response);	

	break;
	
case "leaders": 
	
	$query = '
		SELECT 
		CONCAT(firstName, " ",lastName) AS value, 
		personid 
		FROM tee_people 
		WHERE CONCAT(firstName," ", lastName) LIKE "%'.$mysqli->real_escape_string($INDATA["term"]).'%"';
	$result = $mysqli->query($query) or die($mysqli->error);
	
	$response = array();	
	$hasCurrent = false;
	while ($obj = $result->fetch_object()) {
		$response[] = $obj;
		$hasCurrent |= ($obj->personid == $person->personid);
	}
	if (!$hasCurrent) {
		$name = $person->firstName. ' '. $person->lastName;
		if (!(stripos($name,$INDATA["term"]) === false)) {
			$response[] = array('value' => $name, 'personid' => $person->personid);
		}
	}
	echo json_encode($response);	

	break;	
	
case "fetch":
/*	if (array_key_exists("data",$_SESSION))
		echo $_SESSION["data"];
	else {			*/
		// Construct data from storage
		$data = $person;

		// Add event data
		$result = $mysqli->query("SELECT event FROM tee_events WHERE personid=" . $person->personid) or die($mysqli->error);	
		while ($row = $result->fetch_object()) {
			$data->{"event" . $row->event} = "yes";
		}
		
		// Add variable data
		$result = $mysqli->query("SELECT variable, value FROM tee_variables WHERE personid=" . $person->personid) or die($mysqli->error);	
		while ($row = $result->fetch_object()) {
			$data->{$row->variable} = $row->value;
		}
		
		// Add hotel data
		$result = $mysqli->query("SELECT * FROM tee_hotel WHERE personid=".$person->personid) or die($mysqli->error);
		if ($hotel = $result->fetch_object()) {
			// Person had selected a hotel
			$nightNames  = array("307", "317", "018", "028", "038", "048");
			for ($i = 0; $i < count($nightNames); ++$i) {	
				if ($hotel->nights[$i] == "1") {
					$data->{"hotel".$nightNames[$i]} = "yes";
				}
			}
			
			$data->roomType = $hotel->type;
			$data->hotelAccompany = $hotel->accompany;
			$data->address = $hotel->address;
			$data->passportNumber = $hotel->passportNumber;
			$data->hotelAdditional = $hotel->additional;
		}

		// Add team data
		$teamsResult = $mysqli->query("SELECT tee_teams.* FROM tee_teams JOIN tee_players WHERE tee_teams.teamid=tee_players.teamid AND tee_players.personid=". $person->personid) or die($mysqli->error);
		while ($team = $teamsResult->fetch_object()) {		
			$data->{"team".$team->event."_name"} = $team->name;
			$data->{"team".$team->event."_modified"} = $team->modified;
			$data->{"team".$team->event."_id"} = $team->teamid;
			$playerResult = $mysqli->query("SELECT CONCAT(tee_people.firstName, ' ', tee_people.lastName) AS label, tee_people.personid AS value, tee_players.position AS position FROM tee_players JOIN tee_people WHERE tee_players.personid=tee_people.personid AND tee_players.teamid=".$team->teamid) or die($mysqli->error);	
			while ($player = $playerResult->fetch_object()) {
				$data->{'team'.($team->event).'_'.$player->position} = array('label' => $player->label, 'value' => $player->value);
			}
		}
		echo json_encode($data);
//	}
	break;
	
	
case "fetchManage":
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}
	
	$data = new stdClass();
	
	$data->nationality = $person->nationality;
	
	$peopleResult = $mysqli->query("SELECT * FROM tee_people WHERE nationality='". $person->nationality ."' ORDER BY lastName") or die($mysqli->error);
	
	$data->people = array();
	while ($row = $peopleResult->fetch_assoc()) {
		$data->people[] = $row;
	}
	
	$personalResult = $mysqli->query("SELECT tee_events.personid AS person, tee_events.event AS event FROM tee_events JOIN tee_people ON tee_people.personid=tee_events.personid WHERE tee_people.nationality='".$person->nationality."' AND tee_events.event LIKE 'A%' ORDER BY tee_people.lastName") or die($mysqli->error);
	$data->personal = array();
	while ($row = $personalResult->fetch_object()) {
		$data->personal[] = $row;
	}		
	
	$teamResult = $mysqli->query("SELECT * FROM tee_teams WHERE nationality='".$person->nationality."' AND event LIKE 'A%'") or die($mysqli->error);
	$data->teams = array();
	while ($row = $teamResult->fetch_object()) {
		$data->teams[] = $row;
	}	
	
	$data->positions = array();
	for($i = 0; $i < count($data->teams); ++$i) {
		$team = $data->teams[$i];
		$playersResult = $mysqli->query("SELECT * FROM tee_players WHERE teamid=".$team->teamid) or die($mysqli->error);		
		$team->players = array();
		while ($row = $playersResult->fetch_object()) {
			$team->players[$row->position] = $row->personid;
		}
	}
	echo json_encode($data);

	break;
	
case "fetchPerson":
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}
	
	$data = new stdClass();
	
	$data->nationality = $person->nationality;
	$id = $mysqli->real_escape_string($INDATA['data']);
	$personResult = $mysqli->query("SELECT * FROM tee_people WHERE nationality='". $person->nationality ."' AND personid='".$id."'") or die($mysqli->error);
	echo json_encode($personResult->fetch_object());
	break;
	
case "removeFromEvent":
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}
		
	$id = $mysqli->real_escape_string($INDATA["personid"]);
	$event = $mysqli->real_escape_string($INDATA["event"]);
	$mysqli->query("DELETE FROM tee_events WHERE personid='". $id ."' AND event='".$event."'") or die($mysqli->error);
	if ($mysqli->affected_rows == 1) {
		echo "true";
	} else {
		echo "Didn't remove correct number of rows: ".$mysqli->affected_rows;
	}
	break;	

case "addToEvent":
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}
		
	$id = $mysqli->real_escape_string($INDATA["personid"]);
	$event = $mysqli->real_escape_string($INDATA["event"]);
	$mysqli->query("INSERT INTO tee_events SET personid='". $id ."', event='".$event."'") or die($mysqli->error);
	if ($mysqli->affected_rows == 1) {
		echo "true";
	} else {
		echo "Didn't add correct number of rows: ".$mysqli->affected_rows;
	}
	break;	
	
case "addTeam":
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}
	$data = json_decode($INDATA["data"]);	
	$mysqli->query("INSERT INTO tee_teams SET event='". $data->event ."', name='".$data->name."', nationality='". $person->nationality ."'") or die($mysqli->error);
	if ($mysqli->affected_rows != 1) {
		echo "Didn't add correct number of rows: ".$mysqli->affected_rows;
		exit();
	}
	$teamid = $mysqli->insert_id;
	foreach(($data->players) as $position=>$id) {
		if ($id != null) {
			$mysqli->query("INSERT INTO tee_players SET teamid='".$teamid."', personid='".$id."', position='".$position."'") or die($mysqli->error);
			$mysqli->query("INSERT INTO tee_events SET personid=".$id.", event='".$data->event."'") or die($mysqli->error);
		}
	}
	
	echo json_encode($teamid);
	
	break;	
	
case "removeTeam":
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}
	$teamid = $mysqli->real_escape_string($INDATA["data"]);	
	$teamResult = $mysqli->query("SELECT * FROM tee_teams WHERE teamid='".$teamid."'") or die ($mysqli->error);
	$team = $teamResult->fetch_object();
	if (!$team) {
		echo "Could not find team";
		exit();
	}
	// Delete player enrollments to event
	$mysqli->query("DELETE tee_events FROM tee_events INNER JOIN tee_players ON tee_events.personid=tee_players.personid WHERE tee_players.teamid='".$teamid."' AND tee_events.event='".$team->event."'") or die ($mysqli->error);
	// Delete players
	$mysqli->query("DELETE FROM tee_players WHERE teamid='".$teamid."'") or die ($mysqli->error);
	// Delete team
	$mysqli->query("DELETE FROM tee_teams WHERE teamid='".$teamid."'") or die ($mysqli->error);
	
	echo "true";
	
	break;	

case "removePerson": 					
	
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}		
	
	$id = $mysqli->real_escape_string($INDATA['data']);
	
	$mysqli->query("DELETE FROM tee_events WHERE personid=".$id);
	$mysqli->query("DELETE FROM tee_players WHERE personid=".$id);
	$mysqli->query("DELETE FROM tee_variables WHERE personid=".$id);
	$mysqli->query("DELETE FROM tee_hotel WHERE personid=".$id);
	$mysqli->query("DELETE FROM tee_people WHERE personid=".$id);
	
	echo "true";
	
	break;
	
case "storePerson": 					
	
	if (!($person->manager > 0)) {
		echo "You do not have permission to access this page. Please contact stl@taido.fi.";
		exit();
	}	
	
	// Update personal data
	$data = json_decode($INDATA["data"]);
	
	if ($data->personid != -99) {
		$stmt = $mysqli->prepare("UPDATE tee_people SET lastName=?, firstName=?, email=?, birthDay=?, birthMonth=?, birthYear=?, sex=?, taidoRank=?, lastNameGanji=?, firstNameGanji=?, nationality=?, role=? WHERE personid=?") or die($mysqli->error);	
		$stmt->bind_param("sssiiissssssi", 
			$data->lastName, 
			$data->firstName, 
			$data->email,
			$data->birthDay,
			$data->birthMonth,
			$data->birthYear,
			$data->sex,
			$data->taidoRank,
			$data->lastNameGanji,
			$data->firstNameGanji,
			$person->nationality, // A manager can only handle his/her own countrymen
			$data->role,
			$data->personid);

	} else {
		$stmt = $mysqli->prepare("INSERT tee_people SET loginid=?, lastName=?, firstName=?, email=?, birthDay=?, birthMonth=?, birthYear=?, sex=?, taidoRank=?, lastNameGanji=?, firstNameGanji=?, nationality=?, role=?") or die($mysqli->error);	

		$loginid = md5("wtc2013" . date("F j, Y, g:i a") . $mysqli->real_escape_string($data->email));
		
		$stmt->bind_param("ssssiiissssss", 
			$loginid,
			$data->lastName, 
			$data->firstName, 
			$data->email,
			$data->birthDay,
			$data->birthMonth,
			$data->birthYear,
			$data->sex,
			$data->taidoRank,
			$data->lastNameGanji,
			$data->firstNameGanji,
			$person->nationality, // A manager can only handle his/her own countrymen
			$data->role);			
	}
	
		
	$stmt->execute() or die($mysqli->error);
	
	if ($data->personid == -99) {
		$data->personid = $mysqli->insert_id;
	}
	
	// Personal information update successful	
	$result = $mysqli->query("SELECT * FROM tee_people WHERE personid=".$data->personid) or die($mysqli->error);
	echo json_encode($result->fetch_object());
	break;
case "submit": 				
	
	// Store submitted data just in case
	$stmt = $mysqli->prepare("REPLACE tee_cache SET personid=?, data=?") or die($mysqli->error);
	$stmt->bind_param("is", $person->personid, $INDATA["data"]);
	$stmt->execute() or die($mysqli->error);
	
	// Update personal data
	$data = json_decode($INDATA["data"]);

	$stmt = $mysqli->prepare("UPDATE tee_people SET lastName=?, firstName=?, birthDay=?, birthMonth=?, birthYear=?, sex=?, taidoRank=?, lastNameGanji=?, firstNameGanji=?, nationality=?, package=?, status=\"Submitted\" WHERE personid=?") or die($mysqli->error);	

	$stmt->bind_param("ssiiissssssi", 
		$data->lastName, 
		$data->firstName, 
		$data->birthDay,
		$data->birthMonth,
		$data->birthYear,
		$data->sex,
		$data->taidoRank,
		$data->lastNameGanji,
		$data->firstNameGanji,
		$data->nationality,
		$data->package,
		$person->personid);
		
	$stmt->execute() or die($mysqli->error);
	
	// Personal information update successful	
	$result = $mysqli->query("SELECT * FROM tee_people WHERE personid=".$person->personid) or die($mysqli->error);
	$person = $result->fetch_object();
	$modified = new stdClass();
	$modified->status = $person->status;
	$modified->modified = $person->modified;

	
	// Update event information and storable variables
	$mysqli->query("DELETE FROM tee_variables WHERE personid=".$person->personid);
	$storable = array("diet", "dojo", "helsinkiTourThursday", "helsinkiTourWednesday", "hikingTour", "ifgJudge", "judgeECCount", "judgeIFGCount", "judgeNationalCount", "judgeNationalSeminars", "judgeInternationalSeminars", "judgeWCCount", "optionalBanquette", "optionalWTCticket", "porvooTour", "separateBeds", "tallinTour", "tshirt", "ultimateSauna", "volunteerIT038", "volunteerKiosk018", "volunteerKiosk028", "volunteerKiosk038", "volunteerSecurity028", "volunteerSecurity038", "volunteerTatami028", "volunteerTatami048","volunteerTatami307", "willComplete4dan", "wtcJudge");
	// Remove all IFG event enrollments
	$mysqli->query("DELETE FROM tee_events WHERE personid=".$person->personid." AND event LIKE 'B%'");			
	foreach ($data as $key => $value) {
		if (strcmp(substr($key,0,5),"event") == 0) {
			$code = $mysqli->real_escape_string(substr($key,5));
			if (strcmp($value,"yes")==0) {
				$mysqli->query("INSERT INTO tee_events(personid, event) VALUES (". $person->personid .", \"". $code . "\")") or die($mysqli->error);
			}
		} elseif (in_array($key, $storable) && ($value)) {
			$mysqli->query("INSERT INTO tee_variables(personid, variable, value) VALUES (". $person->personid .",\"". $mysqli->real_escape_string($key). "\",\"". $mysqli->real_escape_string($value) ."\")");
		}
	}
	
	// Update hotel information
	$nightNames  = array("307", "317", "018", "028", "038", "048");
	$nights = "000000";
	$nrnights = 0;	
	for ($i = 0; $i < count($nightNames); ++$i) {	
		$value = $data->{"hotel".$nightNames[$i]};
		if (is_string($value) && !strcmp($value, "yes")) {
			$nights[$i] = "1";
			++$nrnights;
		}
	}
	if ($nrnights > 0) {
		// Wants hotel nights
		$stmt = $mysqli->prepare("REPLACE tee_hotel(personid, nights, type, accompany, address, passportNumber, additional) VALUES (?, ?, ?, ?, ?, ?, ?)") or die($mysqli->error);
		
		$stmt->bind_param("issssss",
			$person->personid,
			$nights,
			$data->roomType,
			$data->hotelAccompany,
			$data->address,
			$data->passportNumber,
			$data->hotelAdditional);
			
		$stmt->execute() or die($mysqli->error);				
	} else {
		// Doesn't want hotel nights
		$mysqli->query("DELETE FROM tee_hotel WHERE personid=".$person->personid) or die($mysqli->error);
	}
	
	// Update team information
	
	// Ensure no-one edits the data while we are editing it	
	$mysqli->query("LOCK TABLES tee_teams WRITE tee_players WRITE");
	$teamsResult = $mysqli->query("SELECT * FROM tee_teams JOIN tee_players WHERE tee_players.teamid=tee_teams.teamid AND tee_players.personid=".$person->personid) or die($mysqli->error);		
	$conflicts = array();
	$teamEvents = array("B19", "B20", "B21", "B22");	
	
	$teamPositions = array("p1","p2","p3","p4","p5","p6","lead","r1","r2");
	while ($team = $teamsResult->fetch_object()) {
	
		// The database is up to date.
		$selected = $data->{"event".$team->event};
		$selected = (is_string($selected) && (!strcmp($selected,"yes")));
		
		if (!$selected) {
			// If the person has not selected the event, only he will be removed from the team and no other changes are made
			sendRemoveReport($mysqli, $person->personid, $team, $actor);
			$mysqli->query("DELETE FROM tee_players WHERE teamid=".$team->teamid." AND personid=".$person->personid) or die($mysqli->error);
		} elseif (strlen($data->{'team'.$team->event.'_name'}) == 0) {
			// No name for the team, remove it.
			$result = $mysqli->query("SELECT personid FROM tee_players WHERE teamid=".$team->teamid) or die($mysqli->error);
			while ($player = $result->fetch_object()) {
				sendRemoveReport($mysqli, $player->personid, $team, $actor);
			}
			$mysqli->query("DELETE FROM tee_players WHERE teamid=".$team->teamid) or die($mysqli->error);
		} else {		
			// Find people in old team
			$oldteam = array();
			$result = $mysqli->query("SELECT personid,position FROM tee_players WHERE teamid=".$team->teamid) or die($mysqli->error);
			while ($player = $result->fetch_object()) {
				$oldteam[$player->position] = $player->personid;
			}
						
			// Find people in new team
			$newteam = array();
			foreach ($data as $name => $value) {
				// Find the player names from the data
				if (strpos($name, 'team'.$team->event)===false) {
					continue;
				}
				$position = substr($name, 8);
				if (in_array($position, $teamPositions)) {
					if ($value) {
						$newteam[$position] = $value->value;
					}
				}
			}

			$toremove = array_diff_assoc($oldteam, $newteam);
			$toadd = array_diff_assoc($newteam, $oldteam);
			$move = array_intersect($toadd, $toremove);						
			
			if ($toremove) {
				foreach ($toremove as $position=>$player) {
					$mysqli->query("DELETE FROM tee_players WHERE teamid=".$team->teamid." AND personid=".$player) or die($mysqli->error);
					if (!in_array($toremove, $move))
						sendRemoveReport($mysqli, $player, $team, $actor);
				}
			}
			
			if ($toadd) {
				foreach ($toadd as $position=>$player) {
					$result = $mysqli->query("INSERT INTO tee_players(teamid, personid, position) VALUES (".
						$team->teamid.','.($player).',"'. $position.'")') or die($mysqli->error);
					if (!in_array($toadd, $move))
						sendAddReport($mysqli, $player, $team, $actor);
				}
			}
						
			if ($toadd || $toremove)
				$mysqli->query("UPDATE tee_teams SET modified=NULL WHERE teamid=".$team->teamid) or die($mysqli->error);
				
			$result = $mysqli->query('SELECT modified FROM tee_teams WHERE teamid='.$team->teamid) or die($mysqli->error);
			$modified->{'team'.$team->event.'_modified'} = $result->fetch_object()->modified;
		}
		
		
		// $team has been handled, remove from additions
		unset($teamEvents[array_search($team->event, $teamEvents)]);		
	}
	
	// Remove any teams that do not have players in them.
	$mysqli->query("DELETE FROM tee_teams WHERE (SELECT COUNT(personid) FROM tee_players WHERE teamid = tee_teams.teamid)=0") or die($mysqli->error);
	
	// Add teams that didn't exists before
	foreach ($teamEvents as $code) {
		if (is_string($data->{'event'.$code}) && (!strcmp($data->{'event'.$code},"yes"))) {
			$players = array();
			
			// Only create a team if it has a valid name.
			if (strlen($data->{'team'.$code.'_name'}) < 4)
				continue;
			
			foreach ($data as $name => $value) {
				if (strpos($name, 'team'.$code)===false) {
					continue;
				}
				$position = substr($name, 8);
				if (in_array($position, $teamPositions)) {
					// Add person to the desired position
					if ($value) {
						$players[$position] = $value->value;
					}
				}
			}

			if (count($players) > 0) {
				// Some players were chosen.
				
				// The user has selected the team event and the corresponding team is not in the database		
				$result = $mysqli->query('INSERT INTO tee_teams(name, event) VALUES ("'.
					$mysqli->real_escape_string($data->{"team".$code."_name"}).'","'. $code . '")') or die($mysqli->error);
				$teamid = $mysqli->insert_id;
				
				$result = $mysqli->query('SELECT * FROM tee_teams WHERE teamid='.$teamid);
				$team = $result->fetch_object();
				$modified->{'team'.$code.'_modified'} = $team->modified;
				
				foreach ($players as $position => $id) {
					$mysqli->query("INSERT INTO tee_players(teamid, personid, position) VALUES (".
						$teamid .','. ($id) .',"'. $position.'")') or die($mysqli->error);
					// Report selections to all parties involved by email.					
					sendAddReport($mysqli, $id, $team, $actor);
				}
				$modified->{'team'.$team->event.'_id'} = $team->teamid;
			}
			
		}
	}
	$mysqli->query("UNLOCK TABLES");
//	$_SESSION["data"] = $INDATA["data"];	
	
	
	sendReport($INDATA, $person, $mysqli);	
	// Find updated date
	echo json_encode($modified);	
	break;
case "retract":
	$mysqli->query("DELETE FROM tee_events WHERE personid=".$person->personid);
	$mysqli->query("DELETE FROM tee_players WHERE personid=".$person->personid);
	$mysqli->query("DELETE FROM tee_variables WHERE personid=".$person->personid);
	$mysqli->query("DELETE FROM tee_hotel WHERE personid=".$person->personid);
	$mysqli->query("UPDATE tee_people SET status=\"Retracted\" WHERE personid=".$person->personid);
	
	// Remove any teams that do not have players in them.
	$mysqli->query("DELETE FROM tee_teams WHERE (SELECT COUNT(personid) FROM tee_players WHERE teamid = tee_teams.teamid)=0") or die($mysqli->error);	
	
	$result = $mysqli->query("SELECT status,modified FROM tee_people WHERE personid=".$person->personid) or die($mysqli->error);
	echo json_encode($result->fetch_object());	
	break;
case "cache":
	$_SESSION["data"] = $INDATA["data"];
	echo "true";
	
	break;
	
default: 
	echo "Illegal or no operation supplied. [".$op."]";
}	

/// CUSTOM FUNCTIONS

function expandEventName($code) {
	$event = new stdClass();
	if ($code[0] == 'A') {
	} elseif ($code[0] == 'B') {
		$event->category = "Interantional Frienship Games 2013";
		switch ($code) {
		case "B19": $event->event = "B19 Dantai hokei"; break;
		case "B20": $event->event = "B20 Dantai jissen, men"; break;
		case "B21": $event->event = "B21 Dantai jissen, women"; break;
		case "B22": $event->event = "B22 Tenkai"; break;		
		}	
	}
	return $event;
}


function sendAddReport($mysqli, $person, $team, $who) {

	if (!strpos($person->email, "@"))
		// Not an email address.
		return;

	$result = $mysqli->query("SELECT * FROM tee_people WHERE personid=\"".$person."\"") or die($mysqli->error);
	$person = $result->fetch_object();
	
	$event = expandEventName($team->event);
	
	$message = '
Dear '. $person->firstName . ' ' . $person->lastName .',

'.$who->firstName .' '. $who->lastName .' has added you to the '. $event->category . ' ' . $event->event .' team "' . $team->name .'".

Sincerely yours,
WTC2013 web team';
	$topic = 'Added to ' . $event->event . ' team ' .$team->name;
	
	$headers = array(
		'From: stl@taido.fi',
        'Content-type: text/plain; charset="UTF-8";',		
//		'Content-type: text/plain; charset=iso-8859-1',	
		'Reply-To: stl@taido.fi',
		'X-Mailer: PHP/' . phpversion());

	if (!mail($person->email, $topic, $message, implode("\r\n", $headers))) {
		$error = error_get_last();
		echo "Send unsuccessfull: " + $error["message"];
		exit;
	}
	
}

function sendRemoveReport($mysqli, $person, $team, $who) {	

	if (!strpos($person->email, "@"))
		// Not an email address.
		return;
	$result = $mysqli->query("SELECT * FROM tee_people WHERE personid=\"".$person."\"") or die($mysqli->error);
	$person = $result->fetch_object();	

	$event = expandEventName($team->event);
	
	$message = 
'Dear '. $person->firstName . ' ' . $person->lastName .',

'.$who->firstName .' '. $who->lastName .' has removed you from the '. $event->category . ' ' . $event->event .' team "' . $team->name .'".

Sincerely yours,
WTC2013 web team';
	$topic = 'Removed from ' . $event->event . ' team ' .$team->name;
	
	$headers = array(
		'From: stl@taido.fi',
        'Content-type: text/plain; charset="UTF-8";',		
//		'Content-type: text/plain; charset=iso-8859-1',	
		'Reply-To: stl@taido.fi',
		'X-Mailer: PHP/' . phpversion());

	if (!mail($person->email, $topic, $message, implode("\r\n", $headers))) {
		$error = error_get_last();
		echo "Send unsuccessfull: " + $error["message"];
		exit;
	}
}

function sendReport($INDATA, $person, $mysqli) {

	if (!strpos($person->email, "@"))
		// Not an email address.
		return;

	ob_start();
	include "report.php";
	$report = ob_get_contents();
	ob_end_clean();
	
	$message =
'Dear '. $person->firstName . ' ' . $person->lastName .',
	
Summary of your enrollment:

'.$report.'

Sincerely yours,
WTC2013 web team';

	$topic = "WTC2013 enrollment summary";
	$headers = array(
		'From: stl@taido.fi',
        'Content-type: text/plain; charset="UTF-8";',		
//		'Content-type: text/plain; charset=iso-8859-1',	
		'Reply-To: stl@taido.fi',
		'X-Mailer: PHP/' . phpversion());	
	if (!mail($person->email, $topic, $message, implode("\r\n", $headers))) {
		$error = error_get_last();
		echo "Send unsuccessfull: " + $error["message"];
		exit;
	}
}
?>