<?php

$r =  include_once (pathinfo(__FILE__,PATHINFO_DIRNAME).'/smarty/libs/Smarty.class.php');


class smartySetup extends Smarty {
	
	function __construct($testMode=TRUE) {
		//parent::__construct();
		$path = pathinfo(__FILE__,PATHINFO_DIRNAME);
		$this->Smarty();
		$this->template_dir = $path.'/smarty/templates/';
		$this->compile_dir = $path.'/smarty/templates_c/';
		$this->config_dir = $path.'/smarty/configs/';
		$this->cache_dir = $path.'/smarty/cache/';
		
     	$this->assign('app_name','Morgana');

     	
     	//if ($testMode) {
			$smarty->compile_check = true;
			$smarty->force_compile = true;
			$smarty->caching = false;
     	//} else {
     	//	$smarty->compile_check = false;
     	//	$smarty->force_compile = false;
     	//	$smarty->caching = false;
		//}
		
	}
}
?>