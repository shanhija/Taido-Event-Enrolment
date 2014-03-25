
$(function() {
	$("#proceedButton").button();

	$("#proceedButton").click(function(event) {
		event.preventDefault();
		
		var regex = new RegExp("[\\?&]loginid=([^&#]*)");
		var result = regex.exec(window.location.search);
		if (result == null) {
			alert("Could not find id in URL. Unable to proceed.");
		} else {
			location.href = "gate.php?loginid="+result[1]+"&page=enroll";
		}
		return false;
	});
});