$(function() {
    if($( "#category-select" ).length > 0){
        var categories = $.parseJSON($("#category-select").attr('data-source'));

        $( "#category-select" ).autocomplete({
            source: categories,
            delay: 50
        });
    }
    $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });
});