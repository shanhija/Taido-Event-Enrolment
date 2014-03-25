<?php

// $INDATA, $person and $mysqli are made available by the calling script
	
if (array_key_exists("actor", $INDATA)) {
	// Someone is acting as the person in personid
	$actor = $INDATA["actor"];
} else {
	// The actor is the person in personid
	$actor = $person->personid;
}


/** PHPExcel */
include 'Classes/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'Classes/PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
// echo date('H:i:s') . " Create new PHPExcel object\n";
$xls = new PHPExcel();

// Set properties
// echo date('H:i:s') . " Set properties\n";
$xls->getProperties()->setCreator("Maarten Balliauw");
$xls->getProperties()->setLastModifiedBy("Maarten Balliauw");
$xls->getProperties()->setTitle("Office 2007 XLSX Test Document");
$xls->getProperties()->setSubject("Office 2007 XLSX Test Document");
$xls->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");

$reportName = $INDATA['report'];

switch ($INDATA['report']) {

case "people":

	$query = "SELECT personid, firstName, lastName, nationality, sex, birthDay, birthMonth, birthYear, taidoRank, email, package, role FROM wtc_people WHERE status='Submitted'";
	if ($person->manager == 1) {
		$query.= " AND nationality='".$person->nationality."'";
		$reportName .= "_".$person->nationality;
	} elseif ($person->manager > 1) {
	} else {
		echo "You do not have the right to access this page.";
		exit();
	}
	$query.= " ORDER BY wtc_people.lastName";
	
	$result = $mysqli->query($query) or die($mysqli->error);

	$xls->setActiveSheetIndex(0);
	$sheet = $xls->getActiveSheet();
	$rowid = 1;
	while ($row = $result->fetch_assoc()) {
		if ($rowid==1) {
			$cols = count($row) + 1;
			// Add row names			
			$keys = array_keys($row);
			$keys[] = "dojo";
			$sheet->fromArray($keys, null, "A1");
			$rowid = 2;
		}
		
		$row["taidoRank"] = rankNumToStr($row["taidoRank"]);

		
		$dojoResult = $mysqli->query("SELECT value FROM wtc_variables WHERE personid=".$row["personid"]." AND variable='dojo'") or die($mysqli->error);
		
		if ($dojoRow = $dojoResult->fetch_assoc()) {
			$row[] = $dojoRow["value"];
		}
		
		$sheet->fromArray($row, null, "A" . $rowid);
		
		++$rowid;
	}

	$sheet->setTitle('People');
	$sheet->setAutoFilterByColumnAndRow(0,1,$cols-1,1);

break;

case "wtcPeople":

	$firstSheet = true;
	
	if ($person->manager == 1) {
		$reportName .= "_".$person->nationality;			
	} elseif ($person->manager > 1) {
	} else {
		echo "You do not have the right to access this page.";
		exit();
	}
	

	for($event = 1; $event <= 10; ++$event) {
	
		$query = 'SELECT 
			wtc_people.personid AS personid, 
			wtc_people.firstName AS firstName, 
			wtc_people.lastName AS lastName, 
			wtc_people.nationality AS nationality, 
			wtc_people.birthYear AS birthYear, 
			wtc_people.taidoRank AS taidoRank 
			FROM wtc_people
			JOIN wtc_events ON wtc_people.personid=wtc_events.personid
			WHERE wtc_events.event="A'.$event.'"';
			
		if ($person->manager == 1) {
			$query.= " AND wtc_people.nationality='".$person->nationality."'";			
		}
		$query.= " ORDER BY wtc_people.lastName";
		$result = $mysqli->query($query) or die($mysqli->error);

		if ($firstSheet) {
			$xls->setActiveSheetIndex(0);
			$sheet = $xls->getActiveSheet();
			$firstSheet = false;
		} else {
			$sheet = $xls->createSheet();			
		}
		
		$rowid = 1;
		while ($row = $result->fetch_assoc()) {
			if ($rowid==1) {
				$cols = count($row);
				// Add row names							
				$sheet->fromArray(array_keys($row), null, "A1");
				$rowid = 2;
			}
			
			$row["taidoRank"] = rankNumToStr($row["taidoRank"]);
			
			$sheet->fromArray($row, null, "A" . $rowid);
			
			++$rowid;
		}

		$sheet->setTitle("A".$event);
		$sheet->setAutoFilterByColumnAndRow(0,1,$cols-1,1);
	}
	break;
case "ifgPeople":
	$firstSheet = true;
	
	if ($person->manager == 1) {
		$reportName .= "_".$person->nationality;			
	} elseif ($person->manager > 1) {
	} else {
		echo "You do not have the right to access this page.";
		exit();
	}
	

	for($event = 1; $event <= 23; ++$event) {
	
		$query = 'SELECT 
			wtc_people.personid AS personid, 
			wtc_people.firstName AS firstName, 
			wtc_people.lastName AS lastName, 
			wtc_people.nationality AS nationality, 
			wtc_people.birthYear AS birthYear, 
			wtc_people.taidoRank AS taidoRank 
			FROM wtc_people
			JOIN wtc_events ON wtc_people.personid=wtc_events.personid
			WHERE wtc_events.event="B'.$event.'"';
			
		if ($person->manager == 1) {
			$query.= " AND wtc_people.nationality='".$person->nationality."'";			
		}
		$query.= " ORDER BY wtc_people.lastName";
		$result = $mysqli->query($query) or die($mysqli->error);

		if ($firstSheet) {
			$xls->setActiveSheetIndex(0);
			$sheet = $xls->getActiveSheet();
			$firstSheet = false;
		} else {
			$sheet = $xls->createSheet();			
		}
		
		$rowid = 1;
		while ($row = $result->fetch_assoc()) {
			if ($rowid==1) {
				$cols = count($row);
				// Add row names							
				$sheet->fromArray(array_keys($row), null, "A1");
				$rowid = 2;
			}
			
			$row["taidoRank"] = rankNumToStr($row["taidoRank"]);
			
			$sheet->fromArray($row, null, "A" . $rowid);
			
			++$rowid;
		}

		$sheet->setTitle("B".$event);
		$sheet->setAutoFilterByColumnAndRow(0,1,$cols-1,1);
	}
	break;
	
case "hotel":
	$firstSheet = true;
	
	if ($person->manager == 1) {
		$reportName .= "_".$person->nationality;			
		
	} elseif ($person->manager > 1) {
	} else {
		echo "You do not have the right to access this page.";
		exit();
	}
	
	$query = '
		SELECT 
		wtc_hotel.personid AS personid,
		wtc_people.firstName AS firstName,
		wtc_people.lastName as lastName,
		wtc_people.nationality AS nationality,
		wtc_hotel.nights AS nights,
		wtc_hotel.type AS type,
		wtc_hotel.accompany AS accompany,
		wtc_hotel.address AS address,
		wtc_hotel.passportNumber as passportNumber,
		wtc_hotel.additional AS additional
		FROM wtc_hotel 
		JOIN wtc_people ON wtc_people.personid=wtc_hotel.personid';

		
	if ($person->manager == 1) {
		$query.= " WHERE wtc_people.nationality='".$person->nationality."'";			
	}
	$query.= " ORDER BY wtc_people.lastName";
	$result = $mysqli->query($query) or die($mysqli->error);

	$xls->setActiveSheetIndex(0);
	$sheet = $xls->getActiveSheet();
	
	$rowid = 1;
	while ($row = $result->fetch_assoc()) {
		if ($rowid==1) {
			$keys = array_keys($row);
			unset($keys[array_search("nights",$keys)]);
			// Add row names							
			$days = array("Tue 30.7.", "Wed 31.7.", "Thu 1.8.", "Fri 2.8.", "Sat 3.8.", "Sun 4.8.");
			$keys = array_merge($keys, $days);
			
			$sheet->fromArray($keys, null, "A1");
			$rowid = 2;
		}
		$nights = $row["nights"];
		
		// Modify contents
		unset($row["nights"]);		
		
		$sheet->fromArray(array_merge($row, str_split($nights,1)), null, "A" . $rowid);
		
		++$rowid;
	}

	$sheet->setTitle("Hotel");
	$sheet->setAutoFilterByColumnAndRow(0,1,count($keys)-1,1);

	break;	
	
case "volunteer":
	$firstSheet = true;
	
	if (!($person->manager > 1)) {
		echo "You do not have the right to access this page.";
		exit();
	}

	// Fetch volunteer names
	$query = 'SELECT
		DISTINCT(variable) AS variable
		FROM wtc_variables
		WHERE variable LIKE "volunteer%"';
	$result = $mysqli->query($query) or die($mysqli->error);
	$keys = array("personid" => 0, "lastName" => 1, "firstName" => 2, "email" => 3, "nationality" => 4, "package" => 5, "ifgEvents" => 6);
	while ($row = $result->fetch_object()) {
		$keys[substr($row->variable,9)] = count($keys);
	}
	
	$query = '
		SELECT 
		wtc_people.personid AS personid, 
		wtc_people.lastName AS lastName, 
		wtc_people.firstName AS firstName, 
		wtc_people.email AS email,
		wtc_people.nationality AS nationality, 		
		wtc_people.package AS package,
		COUNT(wtc_events.event) AS ifgEvents
		FROM wtc_people 
		LEFT JOIN wtc_events ON wtc_people.personid=wtc_events.personid 
		WHERE wtc_people.role <> "wtc" OR wtc_people.role IS NULL
		AND wtc_people.status="Submitted"
		GROUP BY wtc_people.personid
		ORDER BY wtc_people.lastName';
		
	$result = $mysqli->query($query) or die($mysqli->error);

	$xls->setActiveSheetIndex(0);
	$sheet = $xls->getActiveSheet();
	$sheet->fromArray(array_keys($keys), null, "A1");
	
	$rowid = 2;
	while ($row = $result->fetch_assoc()) {
		
		$sheet->fromArray($row, null, "A" . $rowid);
		$query = "SELECT variable FROM wtc_variables WHERE personid=".$row["personid"]." AND variable LIKE 'volunteer%'";
		$volresult = $mysqli->query($query) or die($mysqli->error);
		while ($vol = $volresult->fetch_assoc()) {
			$sheet->setCellValueByColumnAndRow($keys[substr($vol["variable"],9)], $rowid, 1);
		}
		++$rowid;
	}

	$sheet->setTitle("Volunteers");
	$sheet->setAutoFilterByColumnAndRow(0,1,count($keys)-1,1);

	break;		
	
case "statistics":
	$firstSheet = true;
	
	if (!($person->manager > 1)) {
		echo "You do not have the right to access this page.";
		exit();
	}
	
	$query = 'SELECT 
		nationality, 
		count(personid) AS count
		FROM wtc_people 
		WHERE status="Submitted" 
		GROUP BY nationality
		ORDER BY nationality';
	$result = $mysqli->query($query) or die($mysqli->error);

	$xls->setActiveSheetIndex(0);
	$sheet = $xls->getActiveSheet();
	
	$sheet->fromArray(array("Nationality", "Count"), null, "A1");
	$rowid = 2;
		
	$total = 0;
	while ($row = $result->fetch_assoc()) {
		
		$sheet->fromArray(array_merge($row), null, "A" . $rowid);
		
		++$rowid;
		$total += $row["count"];
	}
	$sheet->fromArray(array("Total", $total), null, "A". $rowid);	
	$rowid += 2;
	
	$sheet->fromArray(array("Package", "Count"), null, "A".$rowid); ++$rowid;
	$query = 'SELECT
		package,
		count(personid) AS count
		FROM wtc_people
		WHERE status="Submitted"
		GROUP BY package
		ORDER BY package';
	$result = $mysqli->query($query) or die($mysqli->error);
	while ($row = $result->fetch_assoc()) {
		
		if (!$row["package"])
			$row["package"] = "None";
			
		$sheet->fromArray(array_merge($row), null, "A" . $rowid);
		
		++$rowid;
	}
	++$rowid;

	$sheet->fromArray(array("Competitions", "Count"), null, "A".$rowid); ++$rowid;
	$query = 'SELECT
		event,
		count(personid) AS count
		FROM wtc_events
		GROUP BY event
		ORDER BY event';
	$result = $mysqli->query($query) or die($mysqli->error);
	while ($row = $result->fetch_assoc()) {				
		$sheet->fromArray(array_merge($row), null, "A" . $rowid);		
		++$rowid;
	}
	++$rowid;	

	// Hotel room type
	$sheet->fromArray(array("Hotel room type", "Number of people", "Total nights"), null, "A".$rowid); ++$rowid;
	$query = 'SELECT
		type,
		count(personid) AS count
		FROM wtc_hotel
		GROUP BY type
		ORDER BY type';
	$result = $mysqli->query($query) or die($mysqli->error);
	while ($row = $result->fetch_assoc()) {				
		$nightsResult = $mysqli->query('SELECT nights FROM wtc_hotel WHERE type="'.$row["type"].'"') or die($mysqli->error);
		$total = 0;		
		while ($nights = $nightsResult->fetch_assoc()) {
			$total += substr_count($nights["nights"], "1");
		}
		$row["days"] = $total;		
		$sheet->fromArray(array_merge($row), null, "A" . $rowid);
		++$rowid;
	}
	++$rowid;	
		
	// Optionals
	$sheet->fromArray(array("Miscelleneous", "Count"), null, "A".$rowid); ++$rowid;
	$query = 'SELECT
		variable, 
		COUNT(personid) AS count
		FROM wtc_variables
		WHERE value="yes"
		GROUP BY variable';
	$result = $mysqli->query($query) or die($mysqli->error);
	while ($row = $result->fetch_assoc()) {				
		$sheet->fromArray(array_merge($row), null, "A" . $rowid);
		++$rowid;
	}
	++$rowid;	

	$sheet->setTitle("Statistics");

	break;	
	
case "all":
	$firstSheet = true;
	
	if (!($person->manager > 0)) {
		echo "You do not have the right to access this page.";
		exit();
	}
		
	$eventCodes = array();
	for ($i=1; $i <= 10; ++$i) $eventCodes["A".$i] = "A".$i;
	for ($i=1; $i <= 23; ++$i) $eventCodes["B".$i] = "B".$i;	
		
		
	$categories = array(
		"personid" => "Personal details",
		"package" => "Package",
		"A1" => "WTC events",
		"B1" => "IFG events",
		"type" => "Hotel",
		"ifgJudge" => "Judge",
		"volunteerIT038" => "Volunteer",
		"optionalBanquette" => "Optionals");
		
	$fields = array_merge(
		// Personal information
		array(
		"personid" => "Person ID", 
		"lastName" => "Last name",
		"firstName" => "First name",
		"taidoRank" => "Taido rank",
		"sex" => "Sex", 
		"birthDay" => "Birth day",
		"birthMonth" => "Birth month",
		"birthYear" => "Birth year",
		"nationality" => "Nationality",
		"email" => "Email",
		"lastNameGanji" => "Last name in Ganji",
		"firstNameGanji" => "First name in Ganji",
		"role" => "Role",
		"dojo" => "Dojo",
		"modified" => "Modified",
		
		// Package information
		"package" => "Package", 
		"tshirt" => "T-shirt",
		"diet" => "Diet"),
		
		// WTC and IFG events
		$eventCodes,
		
		// Hotel
		array(
		"type" => "Room type",
		"night1" => "Tue 30.7.",
		"night2" => "Wed 31.7.", 
		"night3" => "Thu 1.8.", 
		"night4" => "Fri 2.8.",
		"night5" => "Sat 3.8.",
		"night6" => "Sun 4.8.",
		"accompany" => "Room mate",
		"address" => "Address",
		"passportNumber" => "Passport number",
		"additional" => "Additional information",
		"separateBeds" => "Separate beds",	
		
		// Judge
		"ifgJudge"=>"IFG Judge",
		"wtcJudge"=>"WTC Judge",
		"judgeNationalSeminars" => "National seminars",
		"judgeInternationalSeminars" => "International seminars",
		"judgeNationalCount" => "Nationals judged",
		"judgeIFGCount" => "IFGs judged",
		"judgeECCount" => "ECs judged",
		"judgeWCCount" => "WCs judged",		
		"willComplete4dan" => "4 dan shinsa",
		
		// Volunteer
		"volunteerIT038" => "IT 3.8.",
		"volunteerKiosk018" => "Kiosk 1.8.",		
		"volunteerKiosk028" => "Kiosk 2.8.",
		"volunteerKiosk038" => "Kiosk 3.8.",
		"volunteerSecurity028" => "Security 2.8.",		
		"volunteerSecurity038" => "Security 3.8.",	
		"volunteerTatami307" => "Tatami 30.7.",
		"volunteerTatami028" => "Tatami 2.8.",
		"volunteerTatami048" => "Tatami 4.8.",

		// Optionals
		"optionalBanquette" => "Banquette (opt)",
		"optionalWTCticket" => "WTC ticket (opt)",
		"ultimateSauna" => "Ultimate sauna",
		"hikingTour" => "Nuuksio hiking",
		"helsinkiTourWednesday" => "Helsinki tour Wed.",
		"helsinkiTourThursday" => "Helsinki tour Thu.",
		"tallinTour" => "Tallin tour",
		"porvooTour" => "Porvoo tour")
		);

	$emptyRow = $fields;
	foreach ($emptyRow as $key=>$value) {
		$emptyRow[$key] = "";
	}
	
	$query = "SELECT * FROM wtc_people WHERE status='Submitted'";
	if (($person->manager == 1)) {
		$query .= "AND nationality='".$person->nationality."'";
	}
	$query .= " ORDER BY lastName";
	
	$peopleResult = $mysqli->query($query) or die($mysqli->error);
	
	$xls->setActiveSheetIndex(0);
	$sheet = $xls->getActiveSheet();	
	
	$catFields = $emptyRow;
	foreach ($categories as $key=>$value) {
		$catFields[$key] = $value;
	}
	$sheet->fromArray(array_values($catFields), null, "A1");		
	$sheet->fromArray(array_values($fields), null, "A2");	
	$rowid = 3;
	while ($personRow = $peopleResult->fetch_assoc()) {

		$row = $emptyRow;
		
		foreach ($personRow as $key=>$value) {
			if (array_key_exists($key, $row)) {
				$row[$key] = $value;
			}
		}
		// Fix rank
		$row["taidoRank"] = rankNumToStr($row["taidoRank"]);

		// Events
		$eventResult = $mysqli->query("SELECT event FROM wtc_events WHERE personid=".$row["personid"]) or die($mysqli->error);
		while ($eventRow = $eventResult->fetch_assoc()) {
			$posResult = $mysqli->query("SELECT position FROM wtc_players JOIN wtc_teams ON wtc_teams.teamid=wtc_players.teamid WHERE event='".$eventRow["event"]."' AND personid=".$row["personid"]) or die($mysqli->error);
			$value = "yes";
			if ($posRow = $posResult->fetch_assoc()) {
				$value = $posRow["position"];
				switch($value[0]) {
				case 'p': $value = str_replace("p","Player ", $value); break;
				case 'r': $value = str_replace("r","Reserve ", $value); break;
				case 'l': $value = "Leader"; break;
				}
				
				
			}
			
			$row[$eventRow["event"]] = $value;
		}
		
		// Hotel
		$hotelResult = $mysqli->query("SELECT * FROM wtc_hotel WHERE personid=".$row["personid"]) or die($mysqli->error);
		if ($hotelRow = $hotelResult->fetch_assoc()) {
			$nights = $hotelRow["nights"];
			for ($i = 0; $i < strlen($nights); ++$i) {
				if ($nights[$i] == '1')
					$row["night".($i+1)] = "yes";
			}
			foreach($hotelRow as $key => $value) {
				if (array_key_exists($key, $row)) {
					$row[$key] = $value;
				}
			}
		}
		
		// Variables
		$varResult = $mysqli->query("SELECT variable,value FROM wtc_variables WHERE personid=".$row["personid"]) or die($mysqli->error);
		while ($varRow = $varResult->fetch_assoc()) {
			if (array_key_exists($varRow["variable"], $row)) {
				$row[$varRow["variable"]] = $varRow["value"];
			}
		}
		
		$sheet->fromArray(array_values($row), null, "A" . $rowid);		
		
			
		++$rowid;
	}

	$sheet->setTitle("People");
	$sheet->setAutoFilterByColumnAndRow(0,2,count($fields)-1,2);

	$sheet = $xls->createSheet();
	$query = "SELECT * FROM wtc_teams";
	if ($person->manager==1) {
		$query = "
			SELECT *				
			FROM wtc_teams 
			WHERE teamid IN 
				(SELECT DISTINCT(teamid) 
				 FROM wtc_players 
				 JOIN wtc_people ON wtc_players.personid=wtc_people.personid 
				 WHERE wtc_people.nationality='".$person->nationality."')";
	}
	$teamResult = $mysqli->query($query) or die($mysqli->error);
	$teamFields = array(
		"teamid" => "Team ID",
		"name" => "Name",
		"event" => "Event",
		"nationality" => "Nationality",
		"modified" => "Modified",
		"lead" => "Leader",
		"lead_id" => "Leader ID",
		"p1" => "Player 1",
		"p1_id" => "Player 1 ID",
		"p2" => "Player 2",
		"p2_id" => "Player 2 ID",
		"p3" => "Player 3",
		"p3_id" => "Player 3 ID",
		"p4" => "Player 4",
		"p4_id" => "Player 4 ID",
		"p5" => "Player 5",
		"p5_id" => "Player 5 ID",
		"p6" => "Player 6",
		"p6_id" => "Player 6 ID",
		"r1" => "Reserve 1",
		"r1_id" => "Reserve 1 ID",		
		"r2" => "Reserve 2",
		"r2_id" => "Reserve 2 ID");
		
	$emptyRow = $teamFields;
	foreach ($emptyRow as $key=>$value) {
		$emptyRow[$key] = "";
	}
	
	$sheet->fromArray(array_values($teamFields), null, "A1");	
	$rowid = 2;
	while ($teamRow = $teamResult->fetch_assoc()) {
		$row = $emptyRow;
		
		foreach ($teamRow as $key=>$value) {
			if (array_key_exists($key, $row)) {
				$row[$key] = $value;
			}
		}		
		
		$playerResult = $mysqli->query("
			SELECT 
				wtc_people.personid,
				position,
				CONCAT(lastName, ' ', firstName) AS name
			FROM wtc_players
			JOIN wtc_people ON wtc_players.personid=wtc_people.personid
			WHERE wtc_players.teamid=".$teamRow["teamid"]) or die($mysqli->error);
		while ($playerRow = $playerResult->fetch_assoc()) {
			if (array_key_exists($playerRow["position"], $row)) {
				// Only include appropriate positions
				$row[$playerRow["position"]] = $playerRow["name"];
				$row[$playerRow["position"]."_id"] = $playerRow["personid"];
			}			
		}
		
		$sheet->fromArray(array_values($row), null, "A" . $rowid);		
		
			
		++$rowid;
	
	}
	
	$sheet->setTitle("Teams");
	$sheet->setAutoFilterByColumnAndRow(0,1,count($teamFields)-1,1);	

	break;		
default: 
	echo "Could not identify report type.";
	exit();
}

// Save Excel 2007 file
// echo date('H:i:s') . " Write to Excel2007 format\n";
$xlsWriter = new PHPExcel_Writer_Excel2007($xls);
$reportFile = "reports/".$reportName.".xlsx";
$xlsWriter->save($reportFile);

// Echo done
// echo date('H:i:s') . " Done writing file.\r\n";
	
	
function rankNumToStr($rank) {
	if ($rank < 0) {
		return (-$rank). " kyu";
	} elseif ($rank > 0) {
		return ($rank). " dan";
	} else {
		return "don't practice";
	}
}
?>
<html>
<script>
//	setTimeout(function() {
		window.location.href= "<? echo $reportFile;?>";
//	}, 0);
</script>
<body>
</body>
</html>
