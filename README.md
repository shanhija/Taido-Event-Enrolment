Taido-Event-Enrolment
=====================

A web enrolment system made specifically for Taido events. 

Requirements
------------

- MySQL, see database.php and 
- PHP with MySQL driver
- PHPExcel, https://phpexcel.codeplex.com/


Enrolment procedure
--------------------

1. User goes to the front page and registers to the system. An email address is required.
	Operational files: index.html, tee_register.js
2. The registration system sends a login email to the given email address. The login email contains a link to the system, which includes the login id, a salted and hashed version of the email.
	Operational files: register.php
3. The user clicks on the link and it takes the user to the welcome page.
	Operational files: database.php, gate.php, welcome.php
4. The user proceeds to enrolment and enrolment page is shown
	Operational files: database.php, gate.php, enroll.php
5. The user makes choices, browses friends etc. on the page
	Operational files: database.php, gate.php, interface.php
6. The user reviews his/her choices.
7. The user submits his/her enrolment.
	Operational files: database.php, gate.php, interface.php, showreport.php

	
General description of the files
--------------------------------

database.php
	Opens a MySQL connection to the server.
	
enroll.php
	Renders the enrolment page, much of which is hidden at any one time. Is tightly coupled with js/tee.js
	
gate.php
	Checks the user's credentials from the database and directs to requested pages, or to interface.php if an operation is requested.

ifsteamsmail.php
	Email sending page to suggest participants to check the IFG team selections due to a problem in the enrolment system early on.
	
index.html
	First page open to everyone.
	
interface.php
	Actual enrolment logic is in this file. Retrieves requested data, updates databases, and sends email about made updates.

manage.php	
	Manager interface to the system. Shows all the information about a single country, or all countries if the user is a general manager. Allows to change the data through interface.php.
	
managereport.php
	Creates various reports about the people enrolled. The reports are restricted to the country the manager is able to see.
	
register.php
	Registers a new email address, or updates a previous one, with a new login id and sends the login information via email. 
	
report.php
	Constructs a report of a single enrolment. Is used and by interface.php when sending emails and indirectly by showreport.php.
	
showreport.php
	A wrapper page to contain the summary report of an enrolment.
	
welcome.php	
	The welcome page where the register email's link takes to.
	
createtables.sql
	MySQL dump style commands to create necessary tables.
	
js/tee.js
	JavaScript logic that contains all the dynamic behaviour of the enrolment page. Is constructed using Model - View - Controller design pattern. Talks to the server via AJAX-calls, to view data, check for data collisions and send submissions. Contains an extensive rule system to check the integrity of the enrolment. Hence, essentially all rules specific to WTC are written there.
	
js/tee_manager.js
	JavaScript logic to handle manager interface. Similar construction as with tee.js

js/tee_register.js
	Registration page JavaScript logic.
	
js/tee_welcome.js
	The welcome page JavaScript logic.
	
	