<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<script src="js/jquery-1.9.0.js"></script>
	<script src="js/jquery-ui-1.10.0.custom.min.js"></script>
	<link href="css/jquery-ui-1.10.1.custom.css" rel="stylesheet">
	<script src="js/tee_manager.js"></script>		
	<link href="css/tee.css" rel="stylesheet" type="text/css" />
	<link href="css/tee_manager.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="wrapper">
	<div class="logo"></div>
	
	<div class="navigation">
		<ul> 
			<li><a href="#" data-target="peoplePage">People</a></li>
			<li><a href="#" data-target="wtcIndividualPage">Individual events</a></li>
			<li><a href="#" data-target="wtcTeamPage">Team events</a></li>
			<li><a href="#" data-target="reportsPage">Reports</a></li>
		</ul>
		<div class="clearfloat"></div>
	</div>
	
<div class="pages">
	<div class="page" id="peoplePage">
		<h1>Every <span id="nationality"></span> enrollment</h1>

		<p>
			This page allows you to manage who has enrolled to the system from your country. The bottons after a name have the following effects:
			<ul>
				<li>Edit: Open an edit dialog to change the personal information of the selected person.</li>
				<li>Enroll: Open a new tab in the enrollment system as if the person had logged in to the personal enrollment system.</li>
				<li>Report: Open a new tab of containing a summary report of all the choices for a person. </li>
			</ul>
			Note that you may only manage the enrollments of your own nationality.
		</p>
		
		<div id="addPersonButton">Add person</div>
		<div id="peopleTable">
			<img src="images/ajax-loader.gif">
		</div>
	</div>	
	<div class="page" id="wtcIndividualPage">
		<h1>Individual WTC events</h1>
		<p>
			This page allows you to assign competitors to the WTC events. A person can enroll to an event only if they have been assigned the "WTC Competor" role and their taido rank and birth year meet the required criteria. In some cases, if a person is very young or has a low taido rank, a letter of recommendation (LOR) may be required. LOR should be sent by email to the Finnish Taido Association (<a href="mailto:stl@taifo.fi">stl@taido.fi</a>) preferably before the submission deadline ends. All applicants are documented and reviewed by both the Finnish Taido Association and the World Taido Federation.
		</p>
		<div id="events">
			<span class="eventTitle">A1 Hokei, men</span>
			<div class="addToEvent">
				Add a competitor
				<input class="addToEventInput" data-code="A1"></input>
			</div>
			<div class="clearfloat"></div>
			<div class="eventList" id="eventA1people"><img src="images/ajax-loader.gif"></div>
			
			<span class="eventTitle">A2 Hokei, women</span>
			<div class="addToEvent">
				Add a competitor 
				<input class="addToEventInput" data-code="A2"></input>
			</div>
			<div class="clearfloat"></div>
			<div class="eventList" id="eventA2people"><img src="images/ajax-loader.gif"></div>
			
			<span class="eventTitle">A3 Jissen, men</span>
			<div class="addToEvent">
				Add a competitor 
				<input class="addToEventInput" data-code="A3"></input>
			</div>
			<div class="clearfloat"></div>
			<div class="eventList" id="eventA3people"><img src="images/ajax-loader.gif"></div>
			<span class="eventTitle">A4 Jissen, women</span>
			<div class="addToEvent">
				Add a competitor 
				<input class="addToEventInput" data-code="A4"></input>
			</div>
			<div class="clearfloat"></div>
			<div class="eventList" id="eventA4people"><img src="images/ajax-loader.gif"></div>
			
		</div>
	</div>	
	<div class="page" id="wtcTeamPage">
		<h1>Team events</h1>
		<p>
			You can edit the people enrolled to the team events of WTC 2013. When a person is selected to a team, he or she are automatically enrolled to the event. Conversely, when he or she is removed from a team, his or her enrollment to the event will be automatically removed.			
		</p>
		<p>
			A person can be included in a team only if they have been assigned the "WTC Competor" role and their taido rank and birth year meet the required criteria. However, these requirements do not apply to leaders and hence you can add any enrolled person as a team leader. Also, in some cases, if a person is very young or has a low taido rank, a letter of recommendation (LOR) may be required. LOR should be sent by email to the Finnish Taido Association (<a href="mailto:stl@taifo.fi">stl@taido.fi</a>) preferably before the submission deadline ends. All applicants are documented and reviewed by both the Finnish Taido Association and the World Taido Federation.		
		</p>
		
		<span class="eventTitle">A5 Dantai hokei, men</span>
		<div class="addTeamButton" data-code="A5">Add team</div>
		<div class="clearfloat"></div>
		<div class="eventList" id="eventA5teams"><img src="images/ajax-loader.gif"></div>
		
		<span class="eventTitle">A6 Dantai hokei, women</span>
		<div class="addTeamButton" data-code="A6">Add team</div>
		<div class="clearfloat"></div>
		<div class="eventList" id="eventA6teams"><img src="images/ajax-loader.gif"></div>

		<span class="eventTitle">A7 Dantai jissen, men</span>
		<div class="addTeamButton" data-code="A7">Add team</div>
		<div class="clearfloat"></div>
		<div class="eventList" id="eventA7teams"><img src="images/ajax-loader.gif"></div>

		<span class="eventTitle">A8 Dantai jissen, women</span>
		<div class="addTeamButton" data-code="A8">Add team</div>
		<div class="clearfloat"></div>
		<div class="eventList" id="eventA8teams"><img src="images/ajax-loader.gif"></div>
		
		<span class="eventTitle">A9 Tenkai, mixed</span>
		<div class="addTeamButton" data-code="A9">Add team</div>
		<div class="clearfloat"></div>
		<div class="eventList" id="eventA9teams"><img src="images/ajax-loader.gif"></div>
		
		<span class="eventTitle">A10 Tenkai, women</span>
		<div class="addTeamButton" data-code="A10">Add team</div>
		<div class="clearfloat"></div>
		<div class="eventList" id="eventA10teams"><img src="images/ajax-loader.gif"></div>		
	</div>	
	<div class="page" id="reportsPage">
		<h1>Reports of all enrollments</h1>
		
		<p>You can produce various reports from the database to inspect what people have enrolled.
		</p>
		
		<div class="button report" id="peopleReport">Personal details</div>
		<div class="button report" id="wtcPeopleReport">People enrolled to each WTC event</div>		
		<div class="button report" id="ifgPeopleReport">People enrolled to each IFG event</div>
		<div class="button report" id="hotelReport">Hotel selections</div>
	<?php if ($person->manager > 1) { ?>
			<div class="button report" id="statsReport">Statistics</div>
			<div class="button report" id="volunteerReport">Volunteer</div>
	<?php } ?>
			<div class="button report" id="megaReport">All enrollment information</div>
				
		<p>This page is still under construction. New content will be added shortly.</p>		
	</div>	
	
	
	<div class="clearfloat"></div>	
</div> <!-- pages -->

	<div class="clearfloat"></div>
	<div id="controls">
		<div class="button" id="nextButton">Next</div>		
		
		<div class="button" id="prevButton">Previous</div>
		<div class="clearfloat"></div>		
	</div>

	<div id="footer">
		<div id="footerLeft">
			Help? <a href="mailto:stl@taido.fi">stl@taido.fi</a>
		</div>
		<div id="footerRight">
			<a href="http://www.motify.fi" target="_blank"><img src="images/logo-small.png"></a>			
		</div>
	</div>

</div> <!-- wrapper -->
<!-- dialogs -->
<div id="dialog-confirm-submit" title="Really submit?" class="dialog">
	<p>When you submit, it will be considered a signed confirmation of your enrollment. If you have IFG dantai or tenkai events selected, other people will be able to add you to their teams.</p>
	
	<p>
	You may update your submission or retract it at any time before the submission deadline 15.4.2013.
	</p>
</div>

<div id="dialog-confirm-retract" title="Really retract?" class="dialog">
	<p>Do you really want to retract your submission? You will be removed from any IFG dantai or tenkai teams you might be selected in.</p>
	
	<p>
		You may resubmit at any time before the submission deadline 15.4.2013.
	</p>
</div>

<div id="dialog-edit-person" title="Edit person" class="dialog">
	<form id="editPersonForm">
		<label for="firstName"><span class="title">First name</span>
			<input type="text" name="firstName">
		</label>
		<label for="lastName"><span class="title">Last name</span>
			<input type="text" name="lastName">
		</label>
		<label for="firstNameGanji"><span class="title">First name in Ganji</span>
			<input type="text" name="firstNameGanji">
		</label>
		
		<label for="lastNameGanji"><span class="title">Last name in Ganji</span>
			<input type="text" name="lastNameGanji">
		</label>

		<label for="email"><span class="title">Email address</span>
			<input type="text" name="email">
		</label>

		<label for="birthDay"><span class="title">Birthday</span>
			<input type="text" name="birthDay" class="birthDay">
			<input type="text" name="birthMonth" class="birthDay">
			<input type="text" name="birthYear" class="birthDay">
		</label>		
		
		<label for="sex"><span class="title">Sex</span>		
			<select name="sex" id="sex">
				<option value="male" selected="selected">male</option>
				<option value="female">female</option>
			</select>		
		</label>
		
		<label for="taidoRank"><span class="title">Taido rank</span>
			<select name="taidoRank" id="taidoRank">
				<option value="8">8 dan</option>
				<option value="7">7 dan</option>
				<option value="6">6 dan</option>
				<option value="5">5 dan</option>
				<option value="4">4 dan</option>
				<option value="3">3 dan</option>
				<option value="2">2 dan</option>
				<option value="1">1 dan</option>
				<option value="-1">1 kyu</option>
				<option value="-2">2 kyu</option>
				<option value="-3">3 kyu</option>
				<option value="-4">4 kyu</option>
				<option value="-5">5 kyu</option>
				<option value="-6">6 kyu</option>
				<option value="-7" selected="selected">7 kyu</option>
				<option value="none">don't practice</option>						
			</select>			
		</label>
		
		<label for="role"><span class="title">Role</span>
			<select name="role">
				<option value="">None</option>
				<option value="wtc">WTC Competitor</option>
				<option value="staff">Staff</option>
			</select>
		</label>		
	</form>
	<p>
	Note! When the personal information is changed, the enrollment to events are updated accordingly without additional notification. For example, if sex is changed from male to female, all enrollments to male events will be removed automatically, including corresponding teams.
	</p>
</div>

<div id="dialog-confirm-remove-person" title="Really remove person?" class="dialog">
	<p>Do you really want to remove this person? He or she will be completely removed from the database, which includes any teams.
	</p>
</div>

<div id="dialog-enroll-ready" title="Editing enrollment" class="dialog">
	<p>A new tab has been opened, where you can edit the enrollment information for the selected person. Close this dialog box to refresh the contents of this page.
	</p>
</div>

<div id="dialog-add-team" title="Add a new team" class="dialog">
	<label for="teamName"><span class="title">Team name</span>
		<input type="text" name="teamName" id="teamName">
		<div id="teamName_loading" class="loading"><img src="images/ajax-loader.gif"></div>
		<div id="teamName_nameok" class="nameok"><img src="images/tick.png"></div>
	</label>	
	
	<label for="lead"><span class="title">Leader</span>
		<div class="selectPerson" data-position="lead"></div>
	</label>
	
	<label for="p1"><span class="title">Player 1</span>
		<div class="selectPerson" data-position="p1"></div>
	</label>

	<label for="p2"><span class="title">Player 2</span>
		<div class="selectPerson" data-position="p2"></div>
	</label>

	<label for="p3"><span class="title">Player 3</span>
		<div class="selectPerson" data-position="p3"></div>
	</label>

	<label for="p4"><span class="title">Player 4</span>
		<div class="selectPerson" data-position="p4"></div>
	</label>

	<label for="p5"><span class="title">Player 5</span>
		<div class="selectPerson" data-position="p5"></div>
	</label>

	<label for="p6"><span class="title">Player 6</span>
		<div class="selectPerson" data-position="p6"></div>
	</label>
	
	<label for="r1"><span class="title">Reserve 1</span>
		<div class="selectPerson" data-position="r1"></div>
	</label>
	
	<label for="r2"><span class="title">Reserve 2</span>
		<div class="selectPerson" data-position="r2"></div>
	</label>
</div>

<div id="submitStatus"></div>			
</body>
</html>