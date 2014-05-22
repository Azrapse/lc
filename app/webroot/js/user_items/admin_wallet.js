(function($){
    $(function(){
        $("#itemModifyLink").on("click", function(event){
            event.preventDefault();
            var itemId = $(this).attr("data-itemId");
            var typeId = $("#type").val();
            var amount = $("#amount").val();
            var usable = $("#usable").val();
            var url = $(this).attr("href");
            //alert("itemID:"+itemId+" typeId:"+typeId+" amount:"+amount+" usable:"+usable);
            $.post(url, {itemId: itemId, typeId: typeId, amount: amount, usable: usable})
                .success(function(){
                    alert("Changes saved!");
                })
                .fail(function(){
                    alert("Error saving changes!");
                })
            return false;
        })
    });
})(jQuery);