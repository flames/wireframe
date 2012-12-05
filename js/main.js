///////////////////////////////////////////////////
/////IMPORTANT!! INCLUDES THE MAIN FUNCTIONS!!////
/////////////////////////////////////////////////
document.writeln("<script language='JavaScript' type='text/javascript' src='"+URL_ROOT+"admin/js/functions.js'></script>");
///////////////////////////////////////////////
$.fn.serializeObject = function()
{
   var o = {};
   var a = this.serializeArray();
   $.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   return o;
};

function logout(){
  $.post(URL_ROOT+"includes/ajax.php", {action:"logout"},function(data) {
    window.location.reload();
  });
}



$(document).ready(function(){
        $(".dropdown-toggle").hover(
                function () {
                        var parent = $(this).parent();
                        parent.children(".dropdown-menu").fadeIn("slow");
                        parent.hover(
                                function () {}, 
                                function () {
                                        $(this).children(".dropdown-menu").fadeOut("slow");
                                }
                        );
                }
        );
        $(".thumb").hover(
                function () {
                        var image = $(this).attr("href");
                        $('.main_pic').attr("src",image);
                }
        );
});