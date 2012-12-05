<?php
/* -----------------------------------------------------------------------------------------
   $Id: outputfilter.note.php 1554 2010-12-05 15:23:03Z web28 $

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 xt:Commerce (campaigns.php 1117 2005-07-25)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

# SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!!!!

function smarty_outputfilter_note($tpl_output, &$smarty) {

  $cop='<div class="copyright"><a href="http://www.xtc-modified.org" target="_blank">' . PROJECT_VERSION . '</a>' . '&nbsp;' . '&copy;' . date('Y') . '&nbsp;' . 'provides no warranty and is redistributable under the <a href="http://www.gnu.org/licenses/gpl.txt" rel="nofollow" target="_blank">GNU General Public License</a></div>';

  //BOC - web28 - making output W3C-Conform: replace ampersands, rest is covered by the modified shopstat_functions.php - preg_replace by cYbercOsmOnauT
  $tpl_output = preg_replace("/((?<!&))&(?!(&|amp;|#[0-9]+;|[a-z0-9]+;))/i", "&amp;", $tpl_output);
  //EOC - web28 - making output W3C-Conform: replace ampersands, rest is covered by the modified shopstat_functions.php - preg_replace by cYbercOsmOnauT

  return $tpl_output.$cop;
}

# SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!!!!
?>