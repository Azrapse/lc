(function($){
	$(function(){
		$("#flagTray").on("click", ".flag", function(event){
			var langid = $(this).attr("data-langid");
			var url = $("#flagTray").attr("data-url");
			$.post(url, {langid: langid}, function(response){            
				location.reload();
			});        
		});
	});
})(jQuery);