<?php
function generateNewsletterByTemplate($txt)
{
    global $URL_ROOT,$LANG;
	
	$txtnew = '
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>FROWEIN 808 NEWSLETTER</title>
            </head>
	<body bgcolor="#ffffff" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
<table background="'.$URL_ROOT.'/admin/includes/templates/newsletter/back.gif" border="0" height="1144" width="596" align="center">
  <tr>
    <td valign="top"><div style="margin:36px 0px 0px 132px; padding:5px; width:412px;">
        <div id="logoHolder" style="height: 76px;"><img src="'.$URL_ROOT.'/admin/includes/templates/newsletter/logo.gif" width="334" height="49"></div>
        <div id="contentHolder" style="color:#000000; font-size:12px; font-family:Verdana,Arial;">
			'.$txt.'
		</div>
	 </td>
	</tr>
	<tr>
	  <td>
	    <div style="margin:0px 0px 0px 132px; padding:5px; width:412px;">
		<div id="unsubscribeHolder" style="color:#000000; font-size:8pt; font-family:Verdana,Arial; height:54px;margin-top:30px;">'.$LANG["newsletter"]["abort_text"].'</div>
		<div id="footerHolder" style="color:#000000; font-size:8pt; font-family:Verdana,Arial;">
		
			<img src="'.$URL_ROOT.'/admin/includes/templates/newsletter/logo_bottom.gif" width="84" height="100" style="float: left; margin-right: 10px;"> 
			
			<div style="">
			  <span style="color:#E60004;">FROWEIN GMBH &amp; CO. KG</span>
			  <p>D-72437 Albstadt, Postfach 201440<br>
		      D-72461 Albstadt, Am Reislebach 83<br>
		      Tel. +49 (74 32) 9 56-0 &middot; Fax +49 (74 32) 9 56-1 38<br>
		      <a href="http://www.frowein808.de" target="_blank">www.frowein808.de</a> &middot; eMail: <a href="mailto:info@frowein808.de">info@frowein808.de</a></p>
		      <p><br>
	            <a href="mailto:info@frowein808.de"></a>
	          </p>
			  			  <br>
				<div align="center">
<a href="http://www.facebook.com/frowein808" style="border:0px solid transparent"><img vspace="5" src="'.$URL_ROOT.'/admin/includes/templates/newsletter/facebook-logo.png" border="0" alt="" /></a> &nbsp;	
<a href="http://www.youtube.com/user/FroweinGmbH?feature=mhum" style="border:0px solid transparent"><img vspace="5" src="'.$URL_ROOT.'/admin/includes/templates/newsletter/youtube-logo.png" border="0"alt="" /></a> &nbsp;
<a href="https://www.xing.com/companies/froweingmbh%252526co.kg?trkid=us%3afcd048176169ca9ea415e079f0154758%3ad41d8cd98f00b204e9800998ecf8427e%3acompanies;trkoff=0" style="border:0px solid transparent"><img vspace="5" src="'.$URL_ROOT.'/admin/includes/templates/newsletter/xing-logo.png" border="0" alt="" /></a> &nbsp;			
<a href="http://twitter.com/frowein808" style="border:0px solid transparent"><img vspace="5" src="'.$URL_ROOT.'/admin/includes/templates/newsletter/twitter.png" border="0" alt="" /></a> 
				</div>
		  </div>
		  </div>
      </div></td>
  </tr>
  <tr><td height="60%">&nbsp;</td></tr>
</table>
</body>
</html>';

	return($txtnew);
}
?>
