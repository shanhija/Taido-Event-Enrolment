$(function() {
	$("#submitLogin").button();

	var showFailure = function(emailAddr, reason) {
		$("#loginForm").hide();
		$("#resultTitle").html("Failed!");
		$("#resultDetails").html("Email could not be sent to " + emailAddr + " address. Please check address. The following error was reported: <br>" + reason);
		$("#result").show();			
	};
	
	$("#submitLogin").click(function(event) {
		event.preventDefault();
		var emailAddr = $("#email").val();
		$.ajax({
			url: "register.php",
 			data: {"email": emailAddr}
		})
		.done(function(data) {
			if (data != "true") {
				showFailure(emailAddr,data);
			} else {
				$("#loginForm").hide();
				$("#resultTitle").html("Email sent successfully.");
				$("#result").show();
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			switch(jqXHR.status) {
				case 404: 
					showFailure(emailAddr, "Registration script could not be found. <br>[error code: " + jqXHR.status + "]"); break;
				case 500:
					showFailure(emailAddr, "Internal server error."); break;
				default:
					showFailure(emailAddr, 	textStatus + "<br>[error code: " + jqXHR.status + "]");
			}
		})
		.always(function() {
		});
		return false;
	});
});