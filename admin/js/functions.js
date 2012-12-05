function open_iframe(url,width,height){
                            if (width != 0 && height != 0){
                            Shadowbox.open({
                                content:    url,
                                player:     "iframe",
                                height:     height,
                                width:      width
                            });
                            }
                            else{
                            Shadowbox.open({
                                content:    url,
                                player:     "iframe"
                            });
                            }
        
}
function openShadowbox(content, player, title, width, height){
    Shadowbox.open({
        content:    content,
        player:     player,
        title:      title,
        height:     height,
        width:      width
    });
}

function wait(ms) {ms += new Date().getTime();while (new Date() < ms){}} 
function str_replace(search, replace, subject) {return subject.split(search).join(replace);}
function count (value) {return value.length;};


$.fn.focusNextInputField = function() {
    return this.each(function() {
        var fields = $(this).parents('form:eq(0),body').find('button,input,textarea,select');
        var index = fields.index( this );
        if ( index > -1 && ( index + 1 ) < fields.length ) {
            fields.eq( index + 1 ).focus();
        }
        return false;
    });
};

  $.fn.extend({
  insertAtCaret: function(myValue){
  var obj;
  if( typeof this[0].name !='undefined' ) obj = this[0];
  else obj = this;

  if ($.browser.msie) {
    obj.focus();
    sel = document.selection.createRange();
    sel.text = myValue;
    obj.focus();
    }
  else if ($.browser.mozilla || $.browser.webkit) {
    var startPos = obj.selectionStart;
    var endPos = obj.selectionEnd;
    var scrollTop = obj.scrollTop;
    obj.value = obj.value.substring(0, startPos)+myValue+obj.value.substring(endPos,obj.value.length);
    obj.focus();
    obj.selectionStart = startPos + myValue.length;
    obj.selectionEnd = startPos + myValue.length;
    obj.scrollTop = scrollTop;
  } else {
    obj.value += myValue;
    obj.focus();
   }
 }
})


function form_submit(id,submit_function){
$("#"+id+" input").keypress(function(event) {
  if ( event.which == 13 ) {
     if ($(this).hasClass('form_submit_next')){
            event.preventDefault();
            $(this).focusNextInputField();
     }
     else{
            if (submit_function){
                event.preventDefault();
                submit_function();
            }
            else{
                $("#"+id).submit();
            }
     }
   }
});
}

function check2array(name){
    var selectedItems = new Array();
    $("input[@name='"+name+"[]']:checked").each(function() {selectedItems.push($(this).val());});
 
    if (selectedItems .length == 0) return false;
    else return selectedItems;
}

function message_reload(message,type,time,url){
    var loader;
    if (url != ""){loader = "= "+"'"+url+"'";}
    else loader = ".reload()";
    Shadowbox.close();
    alert(message,type);
    setTimeout("location"+loader,time);
}

$(document).ready(function() {
    $("textarea").keypress(function(event) {
        if ( event.keyCode == 9 ) {
            event.preventDefault();
            $(this).insertAtCaret('    ');
        }
    });
});



