(function($){
    $(function(){
        // At certain interval, call the processing routine that deals with emails.
        var mail_process_request_url = $(document.body).attr("data-mail-process-url");
        var interval = 30000;
        function mail_process_request(){
            $.post(mail_process_request_url, {})
                .success(function(response){
                    console && console.log("Mail Process Response:" + response.toString());
                })
                .error(function(e){
                    console && console.log("Mail Process Response: ERROR "+e);
                });
        }
        setInterval(mail_process_request, interval);


        // At certain interval, check for imported actions
        var imported_actions_url = $(document.body).attr("data-imported-actions-url");
        var importedActionsInbox = $("#importedActionsInbox");
        var user_id = $(document.body).attr("data-user-id");
        var url = imported_actions_url+"/"+user_id+".json";
        function getNewImportedActions(){
            $.get(url, {})
                .success(function(response){
                   // Response is usually an array of imported actions
                    var count = response && response.length;

                    if(!count){
                        importedActionsInbox.find(".contents").empty();
                        importedActionsInbox.hide();
                    }
                    else{
                        $.get(imported_actions_url,{})
                            .success(function(response){
                                importedActionsInbox.find(".contents").html(response);
                                importedActionsInbox.show();
                            });
                    }
                })
                .error(function(e){
                    console && console.log("Mail Process Response: ERROR "+e);
                });
        }
        setInterval(getNewImportedActions, interval);

        // Start by getting them on load.
        getNewImportedActions();

        // Collapse the ImportedActions inbox if clicking on its title
        $("#importedActionsInbox").on("click", "h1", function(e){
            $("#importedActionsInbox .contents").toggle('fast');
        });
        // Delete the ImportedAction entry if clicking on its 'Viewed' icon
        $("#importedActionsInbox").on("click", ".deleteImportedActionLink", function(e){
            // Do not follow the link
            e.preventDefault();
            var url = $(this).attr("href");
            var li = $(this).parents("li").first();
            var ul = $(this).parents("ul").first();
            $.post(url, {})
                .success(function(response){
                    // Check if the answer was positive. Then delete de entry.
                    if(response === true){
                        li.fadeOut(function(){
                            li.remove();
                            // If there are no more entries left, hide the inbox
                            if(ul.find("li").length < 1){
                                importedActionsInbox.fadeOut();
                            }
                        });

                    } else {
                        console.log("Couldn't delete ImportedAction: "+url);
                    }
                });
        });
    });
})(jQuery);
