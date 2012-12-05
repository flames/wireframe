<?php /* Smarty version 2.6.26, created on 2012-09-29 17:00:02
         compiled from xtc5/index.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'xtc5/index.html', 1, false),array('modifier', 'date_format', 'xtc5/index.html', 46, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['language'])."/lang_".($this->_tpl_vars['language']).".conf",'section' => 'index'), $this);?>

<div id="wrap">
    <div id="header">
        <div id="logo"><img src="<?php echo $this->_tpl_vars['tpl_path']; ?>
img/spacer.gif" width="400" alt="<?php echo $this->_tpl_vars['store_name']; ?>
" /></div>
        <div id="search"><?php echo $this->_tpl_vars['box_SEARCH']; ?>
</div>
    </div>
    <div id="topmenuwrap">
        <ul id="topmenu">
            <li><a href="<?php echo $this->_tpl_vars['index']; ?>
"><?php echo $this->_config[0]['vars']['link_index']; ?>
</a></li>
            <li><a href="<?php echo $this->_tpl_vars['cart']; ?>
"><?php echo $this->_config[0]['vars']['link_cart']; ?>
</a></li>
            <?php if ($this->_tpl_vars['account']): ?>
            <li><a href="<?php echo $this->_tpl_vars['account']; ?>
"><?php echo $this->_config[0]['vars']['link_account']; ?>
</a></li>
            <?php endif; ?>
            <?php if ($_SESSION['customers_status']['customers_status_id'] == '1'): ?>             
            <li><a href="<?php echo xtc_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'); ?>"><?php echo $this->_config[0]['vars']['new_customer']; ?>
</a></li>             
            <?php endif; ?>
            <li><a href="<?php echo $this->_tpl_vars['checkout']; ?>
"><?php echo $this->_config[0]['vars']['link_checkout']; ?>
</a></li>
            <?php if ($_SESSION['customer_id']): ?>
            <li><a href="<?php echo $this->_tpl_vars['logoff']; ?>
"><?php echo $this->_config[0]['vars']['link_logoff']; ?>
</a></li>
            <?php else: ?>
            <li><a href="<?php echo $this->_tpl_vars['login']; ?>
"><?php echo $this->_config[0]['vars']['link_login']; ?>
</a></li>
            <?php endif; ?>
        </ul>
        <div id="languages"><?php echo $this->_tpl_vars['box_LANGUAGES']; ?>
</div>
    </div>
    <div id="breadcrumb"><?php echo $this->_tpl_vars['navtrail']; ?>
</div>
    <div id="contentwrap"> <?php if (! strstr ( $_SERVER['PHP_SELF'] , 'checkout' )): ?>
        <div id="leftcol"><?php echo $this->_tpl_vars['box_CATEGORIES']; ?>
<?php echo $this->_tpl_vars['box_ADD_QUICKIE']; ?>
<?php echo $this->_tpl_vars['box_CONTENT']; ?>
<?php echo $this->_tpl_vars['box_INFORMATION']; ?>
<?php echo $this->_tpl_vars['box_LAST_VIEWED']; ?>
<?php echo $this->_tpl_vars['box_REVIEWS']; ?>
<?php echo $this->_tpl_vars['box_SPECIALS']; ?>
<?php echo $this->_tpl_vars['box_WHATSNEW']; ?>
</div>
        <?php endif; ?>
        <div
		<?php if (! strstr ( $_SERVER['PHP_SELF'] , 'checkout' )): ?>
			id="content"
		<?php else: ?>
			id="contentfull"
		<?php endif; ?>
		 >
        <?php if (strstr ( $_SERVER['PHP_SELF'] , 'index' )): ?>
            <?php if ($_GET['cPath'] == null && $_GET['manufacturers_id'] == ''): ?>
                 <?php if ($this->_tpl_vars['BANNER']): ?><?php echo $this->_tpl_vars['BANNER']; ?>
<?php endif; ?>
           <?php endif; ?>
        <?php endif; ?>
        <?php echo $this->_tpl_vars['main_content']; ?>
</div>
    <?php if (! strstr ( $_SERVER['PHP_SELF'] , 'checkout' )): ?>
    <div id="rightcol"><?php echo $this->_tpl_vars['box_CART']; ?>
<?php echo $this->_tpl_vars['box_LOGIN']; ?>
<?php echo $this->_tpl_vars['box_ADMIN']; ?>
<?php echo $this->_tpl_vars['box_NEWSLETTER']; ?>
<?php echo $this->_tpl_vars['box_BESTSELLERS']; ?>
<?php echo $this->_tpl_vars['box_INFOBOX']; ?>
<?php echo $this->_tpl_vars['box_CURRENCIES']; ?>
<?php echo $this->_tpl_vars['box_MANUFACTURERS_INFO']; ?>
<?php echo $this->_tpl_vars['box_MANUFACTURERS']; ?>
</div>
    <?php endif; ?> </div>
<p class="footer"><?php echo @TITLE; ?>
 &copy; <?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 | Template &copy; 2009 by xtcModified eCommerce Shopsoftware</p>
</div>