<?php
/**
 * Compact PHP code.
 *
 * Strip comments, combine entire library into one file.
 */
function minify_html1($d) {
    $d = str_replace(array(chr(9), chr(10), chr(11), chr(13)), ' ', $d);
    //$d = preg_replace('`<\!\-\-.*\-\->`U', ' ', $d);
    $d = preg_replace('/[ ]+/', ' ', $d);
    $d = str_replace('> <', '><', $d);
    return $d;
}

$source = $_GET["source"];
$target = $_GET["target"];
print "Compacting $source into $target.\n";
 
include $source;
  
$files = get_included_files();
 
$out = fopen($target, 'w');
fwrite($out, '<?php' . PHP_EOL);
foreach ($files as $f) {
  if ($f !== __FILE__) {
    $contents = file_get_contents($f);
    foreach (token_get_all($contents) as $token) {
      if (is_string($token)) {
        fwrite($out, $token);
      }
      else {
        switch ($token[0]) {
          case T_REQUIRE:
          case T_REQUIRE_ONCE:
          case T_INCLUDE_ONCE:
          // We leave T_INCLUDE since it is rarely used to include
          // libraries and often used to include HTML/template files.
          case T_COMMENT:
          case T_DOC_COMMENT:
          case T_OPEN_TAG:
          case T_CLOSE_TAG:
            break;
          case T_WHITESPACE:
            fwrite($out, ' ');
            break;
          case T_CONSTANT_ENCAPSED_STRING:
          	fwrite($out, minify_html1($token[1]));
          	break;
          default:
            fwrite($out, $token[1]);
        }
 
      }
    }
  }
}
fwrite($out, PHP_EOL.'?>');
fclose($out);
?>