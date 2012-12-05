<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title><?php echo $page_title; ?></title>
        <?php foreach ($css_files as $css){?>
        <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo $css; ?>">  
        <?php } ?>
        <script type="text/javascript">var URL_ROOT = '<?php echo $URL_ROOT; ?>'; </script>
        <?php foreach ($js_files as $js){?>
        <script type="text/javascript" src="<?php echo $js; ?>"></script> 
        <?php } ?>
        <script type="text/javascript"> var URL_ROOT = '<?php echo  $URL_ROOT;?>';</script>
    </head>
    <body>

        