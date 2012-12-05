<?php
function getTemplatePath()
{	global $GLOBALSERVNAME,$GLOBALMODDIR;
	$t=$GLOBALSERVNAME."/".$GLOBALMODDIR."/admin/templates/newsletter/";
	return($t);
}

function generateNewsletterByTemplate($txt)
{
    global $LANGUAGETEXT;

/*$txtnew='
<body bgcolor="#ffffff" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" style="color:#CB0001; font-size:8pt; font-family:Verdana,Arial">
<img src="'.getTemplatePath().'hdr.jpg" border="0" alt=""><table border="0" cellspacing="0" cellpadding="0" width="596" height="826" background="'.getTemplatePath().'back.jpg">
<tr><td valign="top">
        <div style="margin:0px; padding:0px; position:absolute; left:0px; top:136px;">
            <div style="margin-left:119px; top:136px; width:423px; padding:12px 12px 12px 25px; color:#000000; font-size:12px; font-style:Arial,Verdana">
                '.$txt.'
            </div>
            <div style="color:#CB0001; font-size:8pt; font-family:Verdana,Arial font-weight:bold; left:0px; width:596px;">
                <img src="'.getTemplatePath().'line.gif" alt="" border="0" style="margin-bottom:10px;"><br>
                <div style="margin-left:30px;">
                FROWEIN GMBH &amp; CO.KG, Am Reislebach 83, D-72461 Albstadt<br>
                Telefon: +49 7432/956-0, Telefax: +49 7432/956-138 <a href="mailto:info@frowein808.de">info@frowein808.de</a><br>
                <br>
                '.$LANGUAGETEXT[216].'
            </div>
        </div>
    </td>
</tr>
</table>
	';*/
	
	$txtnew = '
	<body bgcolor="#ffffff" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
<table background="'.getTemplatePath().'back.gif" border="0" height="1144" width="596">
  <tr>
    <td valign="top"><div style="margin:36px 0px 0px 132px; padding:5px; width:412px;">
        <div id="logoHolder" style="height: 76px;"><img src="'.getTemplatePath().'logo.gif" width="334" height="49"></div>
        <div id="contentHolder" style="color:#000000; font-size:12px; font-family:Verdana,Arial;">
			'.$txt.'
		</div>
		<div id="unsubscribeHolder" style="color:#000000; font-size:8pt; font-family:Verdana,Arial; height:54px;margin-top:30px;">'.$LANGUAGETEXT[216].'</div>
		<div id="footerHolder" style="color:#000000; font-size:8pt; font-family:Verdana,Arial;">
			<img src="'.getTemplatePath().'logo_bottom.gif" width="84" height="100" style="float: left; margin-right: 10px;">
			<div style="">
			  <span style="color:#E60004;">FROWEIN GMBH &amp; CO. KG</span>
			  <p>D-72437 Albstadt, Postfach 201440<br>
		      D-72461 Albstadt, Am Reislebach 83<br>
		      Tel. +49 (74 32) 9 56-0 &middot; Fax +49 (74 32) 9 56-1 38<br>
		      <a href="http://www.frowein808.de" target="_blank">www.frowein808.de</a> &middot; eMail: <a href="mailto:info@frowein808.de">info@frowein808.de</a></p>
		      <p><br>
	            <a href="mailto:info@frowein808.de"></a>
	          </p>
				
		  </div>
		  </div>
      </div></td>
  </tr>
</table>';

	return($txtnew);
}

?>

