///////////////////////////////////////////////////
/////IMPORTANT!! INCLUDES THE MAIN FUNCTIONS!!////
/////////////////////////////////////////////////
document.writeln("<script language='JavaScript' type='text/javascript' src='"+URL_ROOT+"admin/js/functions.js'></script>");
///////////////////////////////////////////////

window.setInterval("zeitanzeige()",1000);
 
       function zeitanzeige()
       {
        d = new Date ();
 
        h = (d.getHours () < 10 ? '0' + d.getHours () : d.getHours ());
        m = (d.getMinutes () < 10 ? '0' + d.getMinutes () : d.getMinutes ());
        s = (d.getSeconds () < 10 ? '0' + d.getSeconds () : d.getSeconds ());
 
        var wochentage = new Array ("Sonntag", "Montag", "Dienstag",
        "Mittwoch", "Donnerstag", "Freitag", "Samstag");
 
        var monate = new Array ("Januar", "Februar", "März", "April",
        "Mai", "Juni", "Juli", "August", "September",
        "Oktober", "November", "Dezember");
 
        document.getElementById("zeit").innerHTML = d.getDate () + '. '
        + monate[d.getMonth ()] + ' '
        + d.getFullYear () +
        ', '
        + h + ':' + m + ':' + s + '';
       }

function ajax_action(action,table,id,value){
    $.post(URL_ROOT + "admin/includes/ajax.php", { action: action, table : table, id : id, value : value},function(data) {
        //alert(data);
        location.reload();
     });
}

function delete_mass(table){
    to_del = check2array("marked");
    $.post(URL_ROOT + "admin/includes/ajax.php", { action: "delete_mass", table : table, to_del : to_del},function(data) {
        location.reload();
     });
}


$(document).ready(function(){
    $(".parent_nav").click(
        function (event) {
            event.preventDefault();
            $(this).parent().children("ul").toggle();
        }
    );
    $(".inactive").hover(
    function () {
        $(this).children().removeClass("icon-blue").addClass("icon-dark_blue");
    }, 
    function () {
        $(this).children().removeClass("icon-dark_blue").addClass("icon-blue");
    }
);

});
