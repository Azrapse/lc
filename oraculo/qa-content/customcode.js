$(function(){
	$(document).on("click", "#oraculoLogin, .loginLink", function(event){
		event.preventDefault();
		var loginFrame = $("<div id='loginFrameContainer'><iframe src='/users/embedded_login' id='loginFrame'/></div>");
		loginFrame.dialog({
			modal: true, 
			width: 450, 
			height: 250, 
			title: "Introduce tus credenciales de Legalecloud"
		});
		var iframe = $("#loginFrame");
		var externalLocation = location;
		iframe.on("load", function(){
			var confirmation = iframe.contents().find(".loggedInConfirmation");
			if(confirmation.length){
				externalLocation.reload();
			}
		});
		return false;
	});
});