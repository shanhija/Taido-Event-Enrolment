var loginID;
var contactEmail = "stl@taido.fi";

/** Widget for handling selection of persons */
function SelectPersonWidget(component, model) {	
	this.component = component;
	var widget = this;
	this.name = widget.component.attr("data-position");
	
	// Setup select person ui components
	widget.component.html("");
						
	// Add an autocomplete component
	var input = $(document.createElement("input"));
	input.attr("type","text");
	input.attr("name", widget.name);					
	widget.component.append(input);
	widget.input = input;
						
	// Add a div to show the selected person
	var nameDiv = $(document.createElement("div"));
	nameDiv.addClass("variableObserver");					
	widget.component.append(nameDiv);
	widget.nameDiv = nameDiv;
	
	// Add a div for remove person button
	var removeDiv = $(document.createElement("div"));
	removeDiv.addClass("removePersonButton");
	widget.component.append(removeDiv);
	removeDiv.button({icons: {primary: "ui-icon-cancel"}, text: false})
		.click(function() {	
			widget.component.removeClass("personSelected");
			model.setTeamPlayer(widget.name, null);
		});

	// Convert the added input to autocomplete field
	input.autocomplete({
		minLength: 3,
		source: [], 
		select: function(event, ui) {
			if (ui.item) {
				// A new person was selected
				model.setTeamPlayer(widget.name, Number(ui.item.personid));
				widget.nameDiv.html(ui.item.label);
				widget.component.addClass("personSelected");
				$(this).val("");
			} 
			return false;
		}
	});	
	
	this.clear = function() {
		widget.component.removeClass("personSelected");
		widget.nameDiv.html("");
		model.setTeamPlayer(widget.name, null);
	};
	
	this.hide = function() { 
		widget.component.parent().hide(); 
	};	
	this.show = function() { 
		widget.component.parent().show(); 
	};
	
	this.setData = function(data) {
		widget.input.autocomplete("option", {source: data});
	};
	
	return this;	
};


//TODO: These rules should be moved to a configuration which could be used for rendering input form and summary as well as rule enforcement in this script
var wtcEventRules = [
	// Event code, title, sex, max birth year, soft max birth year, min belt, soft min belt, all inclusive
	["A1", "Hokei, men", "male", 1997, 1997, -4, 1],
	["A2", "Hokei, women", "female", 1997, 1997, -4, 1],
	["A3", "Jissen, men", "male", 1997, 1995, -2, 1],
	["A4", "Jissen, women", "female", 1997, 1995, -2, 1],
	["A5", "Dantai hokei, men", "male", 1997, 1997, -4, 1],
	["A6", "Dantai hokei, women", "female", 1997, 1997, -4, 1],
	["A7", "Dantai jissen, men", "male", 1997, 1995, -2, 1],	
	["A8", "Dantai jissen, women", "female", 1997, 1995, -2, 1],	
	["A9", "Tenkai, mixed", "*", 1997, 1997, -4, 1],	
	["A10", "Tenkai, women", "female", 1997, 1997, -4, 1]		
	];	

function GlobalModel() {
	// Needed to access "this" in subcontexts
	var mdl = this; 
	
	mdl.people = new Array();
	mdl.events = new Array();
	mdl.teams = new Array();
	mdl.currentTeam = new Object();
	mdl.selectWidgets = new Array();
	
	$(".selectPerson[data-position]").each(function() {
		mdl.selectWidgets.push(new SelectPersonWidget($(this), mdl));
	});
	
	mdl.personToContainer= function(container, person, addButtons) {
		container.empty();
	
		var createContentDiv = function(name, content) {
			var div = $(document.createElement('div'));
			div.addClass(name);
			div.html(content);
			return div;
		};
		
		// Name
		if (person.nationality == 'japanese')
			container.append(createContentDiv('name',person.lastNameGanji + " " + person.firstNameGanji + " (" + person.lastName + " " + person.firstName + ")"));
		else
			container.append(createContentDiv('name',person.lastName + " " +person.firstName));
			
		// Rank
		if (person.taidoRank != null) {	
			if (person.taidoRank > 0)
				container.append(createContentDiv('taidoRank', person.taidoRank + " dan"));
			else if (person.taidoRank < 0)		
				container.append(createContentDiv('taidoRank', (-person.taidoRank) + " kyu"));
			else
				container.append(createContentDiv('taidoRank', "N/A"));		
			
		} else
			container.append(createContentDiv('taidoRank', 'N/A'));
		
		// Birthyear
		if (person.birthYear != null) {
			container.append(createContentDiv('birthYear', person.birthYear));
		} else 
			container.append(createContentDiv('birthYear', "N/A"));
		
		
		// Email
		if (person.email != null) 
			container.append(createContentDiv('email', "<a href=\"mailto:"+person.email+"\">"+person.email+"</a>"));
		else
			container.append(createContentDiv('email', 'N/A'));

		if (addButtons) {
			
			var editButton = createContentDiv('editButton',"Edit");
			editButton.button().click(function() {
				model.editPerson(person);
			});
			container.append(editButton);

			var enrollButton = createContentDiv('enrollButton',"Enroll");
			enrollButton.button().click(function() {
				window.open("gate.php?loginid="+person.loginid+"&page=enroll&actor="+loginID);
				$("#dialog-enroll-ready").dialog({
					modal: true,
					minWidth: 400, 
					resizable: true,
					buttons: {	
						OK: function() {
							model.fetchUpdate(person.personid);
							$(this).dialog("close");
						}
					}
				});
			});
			container.append(enrollButton);
			
			var reportButton = createContentDiv('reportButton',"Report");
			reportButton.button().click(function() {
				window.open("gate.php?loginid="+person.loginid+"&page=showreport");
			});			
			container.append(reportButton);	
		}
	};

	
	mdl.updatePerson = function(person) {
		mdl.people[person.personid] = person;
		mdl.updateAddFields();		
		var container = $('#peopleTable').find('div[data-id="'+person.personid+'"]').first();
		if (container.length == 0) {
			var container = $(document.createElement('div'));
			container.addClass("person");
			container.attr("data-id", person.personid);
			$('#peopleTable').append(container);		
		}
		mdl.personToContainer(container, person, true);
	};
	
	mdl.addPerson = function(person) {
		var container = $(document.createElement('div'));
		container.addClass("person");
		container.attr("data-id", person.personid);
		$('#peopleTable').append(container);
		
		mdl.updatePerson(person);
	};	
	
	mdl.removePerson = function(personid) {
		mdl.people.splice(personid,1);
		mdl.updateAddFields();	
		var container = $('#peopleTable').find('div[data-id="'+personid+'"]').first();
		if (container.length > 0) {
			container.remove();
		}
	};
	
	mdl.fetchUpdate = function(personid) {
		$.ajax("gate.php", {
				dataType: "html",
				data: {
					loginid: loginID,
					operation: "fetchPerson",
					data: personid
				},
				type: 'POST',
		})
		.success(function(response) {			
			$("#submitStatus").append(response+"done");		
			try {
				var person = jQuery.parseJSON(response);
				// Update model
			} catch(exception) {
			    alert("Could not store person. Please contact " + contactEmail + " and supply the following message: exception=" + exception + " response=" + response);
			}
			mdl.updatePerson(person);
			
		})
		.fail(function(jqXHR, type, errorObj) {
			alert("Could not store person. Please contact " + contactEmail + " and supply the following message: type=" + type + ", error="+errorObj);			
		})
		.complete(function() {
		});	
	};
	
	
	mdl.editPerson = function(person) {
		var dialog = $("#dialog-edit-person");	
		var buttons = {
				"Store": function() {				
					// Collect person data
					dialog.find('input').each(function() {
						person[$(this).attr("name")] = $(this).val();
					});
					dialog.find('select').each(function() {		
						person[$(this).attr("name")] = $(this).val();
					});					
					$("#submitStatus").html("Storing person id="+person.personid);
					$.ajax("gate.php", {
							dataType: "html",
							data: {
								loginid: loginID,
								operation: "storePerson",
								data: JSON.stringify(person)
							},
							type: 'POST',
					})
					.success(function(response) {			
						$("#submitStatus").append(response+"done");		
						try {
							var person = jQuery.parseJSON(response);
							// Update model
						} catch(exception) {
							alert("Could not store person. Please contact " + contactEmail + " and supply the following message: exception=" + exception + " response=" + response);	
						}
						mdl.updatePerson(person);
						
					})
					.fail(function(jqXHR, type, errorObj) {
						alert("Could not store person. Please contact " + contactEmail + " and supply the following message: type=" + type + ", error="+errorObj);			
					})
					.complete(function() {
						dialog.dialog( "close" );			
					});
				},
				Cancel: function() {
					$( this ).dialog( "close" );			
				}
			};
		if (person.personid != -99) {
			buttons.Remove = function() {
				dialog.dialog( "close" );
				$("#dialog-confirm-remove-person").dialog({
					modal: true,
					minWidth: 400, 
					resizable: true,
					buttons: {
						"Confirm": function() {
							$.ajax("gate.php", {
									dataType: "html",
									data: {
										loginid: loginID,
										operation: "removePerson",
										data: ""+person.personid
									},
									type: 'POST',
							})
							.success(function(response) {			
								$("#submitStatus").append(response+"done");		
								if (response != "true") {
									alert("Could not remove person. Please contact " + contactEmail + " and supply the following message: response=" + response);	
								}
								mdl.removePerson(person.personid);						
							})
							.fail(function(jqXHR, type, errorObj) {
								alert("Could not store person. Please contact " + contactEmail + " and supply the following message: type=" + type + ", error="+errorObj);			
							})
							.complete(function() {
								$("#dialog-confirm-remove-person").dialog( "close" );			
							});
						}, 
						Cancel: function() {
							$(this).dialog("close");
						}						
					}
				});

			};		
		}
		dialog.find('input').each(function() {
			$(this).val(person[$(this).attr("name")]);
		});
		dialog.find('select').each(function() {		
			$(this).val(person[$(this).attr("name")]);
		});
				
		$("#dialog-edit-person").dialog({
			modal: true,
			minWidth: 400, 
			resizable: true,
			buttons: buttons
		});
	};
	
	mdl.updateEvent = function(container, person, event, code) {
		container.empty();
		container.addClass("person");
		mdl.events[code].push(person.personid);		
		event.append(container);
		mdl.personToContainer(container, person, false);	
		var letterDiv = $(document.createElement('div'));
		letterDiv.addClass("letterOfRecommendation");

		var rules = wtcEventRules[code.substring(1)-1];
		if ((rules[2] != '*' && rules[2] != person.sex) ||
			(rules[4] < person.birthYear) || 
			(rules[6] > person.taidoRank)) {
			// Letter of recommendation is needed
			letterDiv.html("LOR");
		} else {
			letterDiv.addClass("disabled");
		}
		container.append(letterDiv);
		
		var removeButton = $(document.createElement('div'));
		removeButton.addClass("removeButton");
		removeButton.html("remove");
		removeButton.button().click(function() {
			$.ajax("gate.php", {
					dataType: "html",
					data: {
						loginid: loginID,
						operation: "removeFromEvent",
						personid: person.personid,
						event: code
						
					},
					type: 'POST',
			})
			.success(function(response) {			
				$("#submitStatus").append(response+"done");		
				if (response != "true") {
					alert("Could not remove person from event. Please contact " + contactEmail + " and supply the following message: response=" + response);	
				}
				mdl.events[code].splice($.inArray(person.personid, mdl.events[code]),1);
				container.remove();
			})
			.fail(function(jqXHR, type, errorObj) {
				alert("Could not remove person from event. Please contact " + contactEmail + " and supply the following message: type=" + type + ", error="+errorObj);			
			})
			.complete(function() {
			});
		});
		container.append(removeButton);	
	};
	
	mdl.constructEventPeople = function(eventCode) {
		var data = new Object();
		data.leaders = new Array();
		data.competitors = new Array();
		var rules = wtcEventRules[eventCode.substring(1) - 1];
		for (var id in mdl.people) {
			var person = mdl.people[id];
			
			var item;
			if (person.nationality == "japanese") {
				item = {
					value: person.lastNameGanji +" " + person.firstNameGanji + " (" + person.lastName + " " + person.firstName + ")",
					personid: person.personid
				};
			} else {
				item = {
					value: person.lastName + " " + person.firstName,
					personid: person.personid
				};							
			}
			data.leaders.push(item);
			if (person.role == "wtc") {
				// Only wtc competitors are included
				if ((rules[2] == '*' || rules[2] == person.sex) &&
					(rules[3] >= person.birthYear) && 
					(rules[5] <= person.taidoRank)) {
										
					if ($.inArray(person.personid, mdl.events[eventCode]) > -1) {
						// Player is already added in the corresponding event
						continue;
					}
					data.competitors.push(item);
				}
			}
		}
		return data;
	};
	
	mdl.updateAddFields = function() {
	
		$('.addToEventInput').each(function() {
	
			// First construct the data
			var comp = $(this);
			var eventCode = comp.attr("data-code");
			var data = mdl.constructEventPeople(eventCode);
			
			var eventContainer = $('#event'+eventCode+"people");
			comp.autocomplete({
				minLength: 2,
				source: data.competitors,
				select: function(event, ui) {
					$(this).val("");
					if (($.inArray(ui.item.personid, mdl.events[eventCode]))<0) {
						// Update database
						$.ajax("gate.php", {
								dataType: "html",
								data: {
									loginid: loginID,
									operation: "addToEvent",
									personid: ui.item.personid,
									event: comp.attr("data-code")
									
								},
								type: 'POST',
						})
						.success(function(response) {			
							$("#submitStatus").append(response+"done");		
							if (response != "true") {
								alert("Could not add person to event. Please contact " + contactEmail + " and supply the following message: response=" + response);	
								return;
							}
							var container = $(document.createElement('div'));					
							mdl.updateEvent(container, mdl.people[ui.item.personid], eventContainer, comp.attr("data-code"));
						})
						.fail(function(jqXHR, type, errorObj) {
							alert("Could not add person to event. Please contact " + contactEmail + " and supply the following message: type=" + type + ", error="+errorObj);			
						})
						.complete(function() {
						});						
					} 
					return false;
				}
			});		
		});
	};
	
	mdl.addTeam = function(team) {
		mdl.teams[team.teamid] = team;
		var container = $("#event"+team.event+"teams");
		var teamComp = $(document.createElement('div'));
		teamComp.addClass("team");
		mdl.teams[team.teamid].component = teamComp;
		
		var titleDiv = $(document.createElement('div'));
		titleDiv.addClass("teamName");
		titleDiv.html(team.name);
		
		var removeButton = $(document.createElement('div'));
		removeButton.html("Remove");
		removeButton.addClass("removeButton");
		teamComp.append(removeButton);		
		removeButton.button().click(function() {
			$.ajax("gate.php", {
					dataType: "html",
					data: {
						loginid: loginID,
						operation: "removeTeam",
						data: team.teamid
					},
					type: 'POST',
			})
			.success(function(response) {			
				$("#submitStatus").append(response+"done");		
				if (response == "true") {
					for (var position in team.players) {
						var player = team.players[position];
						mdl.events[team.event].splice($.inArray(player, mdl.events[team.event]),1);							
					}
					mdl.teams[team.teamid].component.remove();
					delete mdl.teams[team.teamid];
				} else {
					alert("Could not remove team. Please contact " + contactEmail + " and supply the following message: response=" + response);
				}
				
				
			})
			.fail(function(jqXHR, type, errorObj) {
				alert("Could not remove team. Please contact " + contactEmail + " and supply the following message: type=" + type + ", error="+errorObj);			
			})
			.complete(function() {
			});			
		});
		
		teamComp.append(titleDiv);
		
		var names = {
			lead: "Leader", 
			p1: "Player 1",
			p2: "Player 2",
			p3: "Player 3",
			p4: "Player 4",
			p5: "Player 5",
			p6: "Player 6",
			r1: "Reserve 1",
			r2: "Reserve 2"};
			
		for (var position in team.players) {
			if (team.players[position] != null) {
				var person = mdl.people[team.players[position]];
				var personDiv = $(document.createElement('div'));
				personDiv.addClass("person");
				var positionDiv = $(document.createElement('div'));
				positionDiv.addClass("position");
				positionDiv.html(names[position]);
				mdl.personToContainer(personDiv, person, false);
				personDiv.prepend(positionDiv);
				
				var letterDiv = $(document.createElement('div'));
				letterDiv.addClass("letterOfRecommendation");

				var rules = wtcEventRules[team.event.substring(1)-1];
				if ((rules[2] != '*' && rules[2] != person.sex) ||
					(rules[4] < person.birthYear) || 
					(rules[6] > person.taidoRank)) {
					// Letter of recommendation is needed
					letterDiv.html("LOR");
				} else {
					letterDiv.addClass("disabled");
				}
				personDiv.append(letterDiv);
				
				teamComp.append(personDiv);
			}
		}
		container.append(teamComp);
	};
	
	mdl.setTeamPlayer = function(position, value) {
		mdl.currentTeam.players[position] = value;
	};
	
	mdl.openTeamAdd = function(eventCode) {
		
		var data = mdl.constructEventPeople(eventCode);
		
		// Clear team name
		$('#teamName').val("");
		$("#teamName_nameok").hide();		
		$("#teamName_loading").hide();		
		
		mdl.currentTeam = new Object();
		mdl.currentTeam.players = new Object();
		mdl.currentTeam.nameok = false;
		mdl.currentTeam.event = eventCode;
		
		var requiredPositions = ["lead", "p1", "p2", "p3", "p4", "p5", "p6"];
		if ($.inArray(eventCode, ["A5", "A6", "A9", "A10"]) > -1)
			// No leader for dantai hokei and tenkai
			requiredPositions.splice($.inArray("lead", requiredPositions),1);
			
		if ($.inArray(eventCode, ["A5", "A6", "A7", "A8"]) > -1)
			// No sixth player for dantai hokei and jissen
			requiredPositions.splice($.inArray("p6", requiredPositions),1);
		
		for(var ind in mdl.selectWidgets) {
			var widget = mdl.selectWidgets[ind];
			widget.clear();
			if ($.inArray(widget.name, requiredPositions) > -1 || widget.name[0] == 'r') 
				widget.show();
			else 
				widget.hide();
			if (widget.name == "lead")
				widget.setData(data.leaders);
			else
				widget.setData(data.competitors);			
		}
		
		
		var dialog = $("#dialog-add-team");	
		var buttons = {
				"Add": function() {				
					$("#submitStatus").html("Adding new team");
					
					if (!mdl.currentTeam.nameok) {
						alert("The team must have a valid name.");
						return;
					}
					
					// Check all fields
					var people = new Array();
					for (var ind in requiredPositions) {
						var personid = mdl.currentTeam.players[requiredPositions[ind]];
						if (personid == null) {
							alert("The team must be full before it can be added.");
							return;
						}
						if ($.inArray(personid, people) > -1) {
							alert("Cannot have the same person in several positions.");
							return;
						}
						people.push(personid);
					}
					
					$.ajax("gate.php", {
							dataType: "html",
							data: {
								loginid: loginID,
								operation: "addTeam",
								data: JSON.stringify(mdl.currentTeam)
							},
							type: 'POST',
					})
					.success(function(response) {			
						$("#submitStatus").append(response+"done");		
						try {
							var teamid = jQuery.parseJSON(response);
						} catch(exception) {
							alert("Could not store person. Please contact " + contactEmail + " and supply the following message: exception=" + exception + " response=" + response);
							return;
						}			
						
						// Update model
						mdl.currentTeam.teamid = teamid;
						
						for(var ind in mdl.currentTeam.players) {
							mdl.events[mdl.currentTeam.event].push(mdl.currentTeam.players[ind]);
						}
						mdl.addTeam(mdl.currentTeam);
						
						dialog.dialog( "close" );			
						
					})
					.fail(function(jqXHR, type, errorObj) {
						alert("Could not store person. Please contact " + contactEmail + " and supply the following message: type=" + type + ", error="+errorObj);			
					})
					.complete(function() {
					});
				},
				Cancel: function() {
					$( this ).dialog( "close" );			
				}
			};

		$("#dialog-add-team").dialog({
			modal: true,
			minWidth: 400, 
			resizable: true,
			buttons: buttons
		});		
	};
	
	mdl.useResponse = function(response) {
		var isJSON = false;
		try {
			// Test if the result is JSON
			var values = jQuery.parseJSON(response);
			
			isJSON = true;
		} catch (exception) {
			$('#submitStatus').append(exception);					
		}
		if (isJSON && typeof(values) == "object") {
			$('#submitStatus').append(" applying JSON data.");
			
			$('#peopleTable').html("");			
			var people = values["people"];
			for (var person in people) {
				mdl.addPerson(people[person]);
				mdl.people[people[person].personid] = people[person];
			}
			
			var events = values["personal"];
			mdl.events = new Object();

			$('.eventList').html("");
			for (var i = 0; i < wtcEventRules.length; ++i) {
				mdl.events[wtcEventRules[i][0]] = new Array();
			}
			for (var i = 0; i < events.length; ++i) {
				var event = $('#event'+events[i]["event"]+"people");
				if (event.length > 0) {
					var person = mdl.people[events[i]["person"]];
					var container = $(document.createElement('div'));					
					mdl.updateEvent(container, person, event, events[i]["event"]);
				}
				mdl.events[events[i]["event"]].push(events[i]["person"]);
			}
			mdl.updateAddFields();
			
			// Handle teams
			var teams = values["teams"];
			mdl.teams = new Object();
			for (var i = 0; i < teams.length; ++i) {
				mdl.addTeam(teams[i]);
			}
			
			$("#nationality").html(values["nationality"]);
			
			mdl.values = values;
			
			pages.openPage("peoplePage");
		}
	};
	
	// Store to server routines
	mdl.store = function(operation) {	
		$("#submitStatus").html(operation + "...");
		$.ajax("gate.php", {
				dataType: "html",
				data: {
					loginid: loginID,
					operation: operation,
					data: JSON.stringify(mdl.values)
				},
				type: 'POST',
		})
		.done(function(response) {
			if (operation == "submit") {
				location.href = "gate.php?loginid="+loginID+"&page=showreport";
			}
			mdl.useResponse(response);
			$("#submitStatus").html(response+"done");
		})
		.fail(function(jqXHR, type, errorObj) {
				$("#submitStatus").html(type + "<br />"+errorObj);
		});
		
		
	};
	
	mdl.retract = function() {
		$("#submitStatus").html("retracting...");				
		$.ajax("gate.php", {
				dataType: "html",
				data: {
					loginid: loginID,
					operation: "retract"
				},
				type: 'POST',
		})
		.done(function(response) {
			mdl.useResponse(response);
			$("#submitStatus").html(response+"done");
		})
		.fail(function(jqXHR, type, errorObj) {
				$("#submitStatus").html(type + "<br />"+errorObj);
		});		
	};
	
	mdl.fetch = function() {
		$("#submitStatus").html("fetching...");		
		$.ajax("gate.php", {
				dataType: "html",
				data: {
					loginid: loginID,
					operation: "fetchManage",
				},
				type: 'POST',
		})
		.done(function(response) {
			$("#submitStatus").html("Response: " + response);	
			mdl.useResponse(response);
//			pages.openPage("personalPage"); 
			$('#submitStatus').append(" Opening personal page.");						
		})
		.fail(function(jqXHR, type, errorObj) {
				$("#submitStatus").html(type + "<br />"+errorObj);
		});			
	};

};	

function Pages() {
	pgs = this;
	this.all = new Array();
	this.names = new Object();
	this.current = 0;
	
	/* Populate page information */
	$('.navigation a').each(function() {
		var comp = $(this);
		var page = {
			target: comp.attr("data-target"),
			enabled: true,
			menuItem: comp.parent()};
		pgs.all.push(page);
		var index = pgs.all.length - 1;
		// For easy access
		pgs.names[page.target] = index;
		
		comp.click(function(event) {
			event.preventDefault();		
			return (page.enabled) && pgs.openPage(index);
		});
	});
	
	this.openPage  = function(index) {

		if (typeof index == "string") {
			index = pgs.names[index];
		}
		
		if (!pgs.all[index].enabled) {
			// Don't open disabled pages
			return false;
		}
		
		var current = pgs.all[pgs.current];
		
		$('#' + current.target).hide();
		current.menuItem.removeClass('opened');	
			
		pgs.current = index;	
		current = pgs.all[index];
		$('#' + current.target).show();
		pgs.all[index].menuItem.addClass('opened');
		
		
		if (index == pgs.all.length-1) {
			// Hide the next button when we are at the last page.
			$("#nextButton").hide();
		} else {
			$("#nextButton").show();
		}
		if (index == 0) {
			// Hide the prev button when we are at the last page.
			$("#prevButton").hide();
		} else {
			$("#prevButton").show();
		}
				
		return true;
	};
	
	this.setEnabled = function(index, enable) {
		if (typeof index == "string") {
			index = pgs.names[index];
		}
		var page = pgs.all[index];
		if (enable) {
			page.enabled = true;
			page.menuItem.removeClass('disabled');
			
		} else {
			if (index == pgs.current) {
				alert("Maximum badness: current page is being disabled.");
				return;
			}
			page.enabled = false;
			page.menuItem.addClass('disabled');
		}		
	}
	
	this.nextPage = function() {
		var index = pgs.current+1;
		while (index < pgs.all.length && !pgs.all[index].enabled) ++index;
		if (index == pgs.all.length) {
			alert("Maximum bad. Über trouble. And no marmelade. index=\\infty");
			return;
		}
		pgs.openPage(index);	
	};
	
	this.prevPage = function() {
		var index = pgs.current-1;
		while (index >= 0 && !pgs.all[index].enabled) --index;
		if (index == -1) {
			alert("Maximum bad. Über trouble. And no marmelade. index==-1");
			return;
		}
		pgs.openPage(index);
	};
};

var model, pages;		
	
$(function() {

	// Find out the login id
	var regex = new RegExp("[\\?&]loginid=([^&#]*)");
	var result = regex.exec(window.location.search);
	if (result == null) {
		alert("Could not find id in URL. Cannot proceed.");
		return;
	} else {
		loginID = result[1];
	}
	
	/* Content verifiers */
	model = new GlobalModel();
	pages = new Pages();
				
	// Control buttons
	$("#nextButton").button().click(function() {pages.nextPage();});
	$("#prevButton").button().click(function() {pages.prevPage();});

	$("#submitButton").button().click(function(event) {
		$("#dialog-confirm-submit").dialog({
			modal: true,
			resizable: false,
			buttons: {
				"Submit": function() {
					model.store("submit");
					$( this ).dialog( "close" );				
				},
				Cancel: function() {
					$( this ).dialog( "close" );			
				}
			}
		});	
	});
	
	$("#addPersonButton").button().click(function() {
		var person = new Object();
		person.personid = -99;
		model.editPerson(person);
	});

	$(".addTeamButton").button().click(function() {
		model.openTeamAdd($(this).attr("data-code"));
	});
	
	$("#peopleReport").button().click(function() {
		window.open("gate.php?loginid="+loginID+"&page=managereport&report=people");
	});
	$("#wtcPeopleReport").button().click(function() {
		window.open("gate.php?loginid="+loginID+"&page=managereport&report=wtcPeople");
	});
	$("#ifgPeopleReport").button().click(function() {
		window.open("gate.php?loginid="+loginID+"&page=managereport&report=ifgPeople");
	});
	$("#hotelReport").button().click(function() {
		window.open("gate.php?loginid="+loginID+"&page=managereport&report=hotel");
	});
	$("#statsReport").button().click(function() {
		window.open("gate.php?loginid="+loginID+"&page=managereport&report=statistics");
	});	
	$("#volunteerReport").button().click(function() {
		window.open("gate.php?loginid="+loginID+"&page=managereport&report=volunteer");
	});
	$("#megaReport").button().click(function() {
		window.open("gate.php?loginid="+loginID+"&page=managereport&report=all");
	});

//	$("#saveButton").button().click(function(event) {model.store("cache");});
	
	
	// Add warnings about empty team members	
	/*
	model.addEffect(["teamB19_p1", "teamB19_p2", "teamB19_p3", "teamB19_p4", "teamB19_p5", "eventB19"], function() {		
		// SelectPersonWidget stores an object as value when a person is selected, otherwise it is null
		if (model.getValue("eventB19") == "yes") {
			var nr = 0;
			var teamNames = ["teamB19_p1", "teamB19_p2", "teamB19_p3", "teamB19_p4", "teamB19_p5"];			
			$.each(teamNames, function() {
				if (model.getValue(this)) 
					++nr;
			});
			if (nr < teamNames.length)
				$("#eventB19playerMissing").removeClass("disabled");
			else 
				$("#eventB19playerMissing").addClass("disabled");
		} else {
			$("#eventB19playerMissing").addClass("disabled");
		}
	});
	*/

	// Teams must have names
	$('#teamName').change(function() {
		model.currentTeam.nameok = false;
		var value = $(this).val();
		$("#teamName_nameok").hide();
		if (value.length < 4) {
			// A non-empty name must be of valid length
			alert("Team name must be at least 4 characters long");
			return;
		}
			
		$("#teamName_loading").css("display","inline-block");
		var matches;
		var url = 'gate.php?loginid=' + loginID + '&operation=teamname';
		url += '&event=' + model.currentTeam.event + '&name=' + escape(value);
		
		$.ajax({
				url:   url,
				success: function(response) {
						matches = parseInt(response);
					},
				error: function(jqXHR, type, errorObj) {
					$("#submitStatus").html(type + "<br />"+errorObj);
					},	
				async: false
			});       
		$("#teamName_loading").hide();
		if (matches == undefined) {
			alert("Could not connect to server. Try again later.");
		} else if (matches == 0) {
			model.currentTeam.nameok = true;		
			model.currentTeam.name = value;
			$("#teamName_nameok").css("display","inline-block");			
		} else {
			alert("The team name or similar has already been taken for the event. Please select another one.");
		}		
	});
	
/*	model.addVariable("teamB19_id"); model.addVariable("teamB20_id"); 
	model.addVariable("teamB21_id"); model.addVariable("teamB22_id");
	
	model.addRule("teamB19_name", function(value) { return teamNameTest(value, "B19");});
	model.addRule("teamB20_name", function(value) { return teamNameTest(value, "B20");});
	model.addRule("teamB21_name", function(value) { return teamNameTest(value, "B21");});
	model.addRule("teamB22_name", function(value) { return teamNameTest(value, "B22");});
	*/
	
	model.fetch();	
	
	
	// Cache data every 5 minutes
/*	setInterval(function() {
		model.store("cache");
	}, 60*1000);*/
	
});