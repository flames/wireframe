$(document).ready(function(){
        $(".top_point").hover(
                function () {
                        $(this).children("ul").show();
                }, 
                function () {
                        $(this).children("ul").hide();
                }
        );
        $.gsuggest({'url': URL_ROOT + 'includes/suggest.php'});
});