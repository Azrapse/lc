(function($){	
	function parseDate(dateStringInRange) {
		var isoExp = /^\s*(\d{4})-(\d\d)-(\d\d)(?:T|\s+)(\d\d):(\d\d):(\d\d).*Z?\s*$/,
			date = new Date(NaN), month,
			parts = isoExp.exec(dateStringInRange);
		if (parts) {
			month = +parts[2];
			date.setUTCFullYear(parts[1], month - 1, parts[3]);
			date.setUTCHours(parts[4]);
			date.setUTCMinutes(parts[5]);
			date.setUTCSeconds(parts[6]);
			if(month != date.getMonth() + 1) {
				date.setTime(NaN);
			}
		}
		return date;
	}
	if ( !Date.prototype.toISOString ) {     
		( function() {     
			function pad(number) {
				var r = String(number);
				if ( r.length === 1 ) {
					r = '0' + r;
				}
				return r;
			}  
			Date.prototype.toISOString = function() {
				return this.getUTCFullYear()
					+ '-' + pad( this.getUTCMonth() + 1 )
					+ '-' + pad( this.getUTCDate() )
					+ 'T' + pad( this.getUTCHours() )
					+ ':' + pad( this.getUTCMinutes() )
					+ ':' + pad( this.getUTCSeconds() )
					+ '.' + String( (this.getUTCMilliseconds()/1000).toFixed(3) ).slice( 2, 5 )
					+ 'Z';
			};   
		}() );
	}
	
	var mailRegex = new RegExp(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i);
	$(document).on("change", ".inlineEditor", function(){
		var input = $(this);
		var value = input.val();		
		var date = parseDate(value);
		var icon = $("#"+input.attr("data-iconId"));
		if(!value){
			date = null;
			icon.removeClass("okay error warning").addClass("warning");
			icon.attr("title", "Fecha vacía. No se notificará.");
		} else if(isNaN(date)){
			input.val("");
			date = null;
			icon.removeClass("okay error warning").addClass("error");
			icon.attr("title", "Fecha no válida. No se notificará.");
		} else {
			date = new Date(date).toISOString();
		}
		var url = input.attr("data-notifyUrl");
		$.post(url, {value: date}, function(response){			
			if(!date){
				return;
			}
			if(!mailRegex.match(response)){				
				icon.removeClass("okay error warning").addClass("warning");
				icon.attr("title", "Usted no ha proporcionado una dirección de correo válida.\nProporcione una dirección de correo en su perfil para que la notificación pueda ser enviada.\nDetalles del Error:"+response);				
			} else {
				icon.removeClass("okay error warning").addClass("okay");
				icon.attr("title", "Se notificará a la dirección de correo "+response);
			}			
		});
	});
})(jQuery);