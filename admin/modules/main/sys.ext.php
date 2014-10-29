<?php

	// Add stylesheet files
	scrylemgr::addCssFile('reset', 'reset.css');
	scrylemgr::addCssFile('style', 'style.css');
	scrylemgr::addCssFile('style-fixed', 'style_fixed.css');

	// Add javascript files
	scrylemgr::addJsFile('jquery-ui-selectmenu', 'jquery.ui.selectmenu.js');
	scrylemgr::addJsFile('jquery-flot', 'jquery.flot.min.js');
	scrylemgr::addJsFile('jquery-tinymce', 'tiny_mce/jquery.tinymce.js');
	scrylemgr::addJsFile('smooth', 'smooth.js');
	scrylemgr::addJsFile('smooth-menu', 'smooth.menu.js');
	scrylemgr::addJsFile('smooth-table', 'smooth.table.js');
	scrylemgr::addJsFile('smooth-form', 'smooth.form.js');
	scrylemgr::addJsFile('smooth-dialog', 'smooth.dialog.js');
	scrylemgr::addJsFile('smooth-autocomplete', 'smooth.autocomplete.js');

	if (isset($_COOKIE['admincolor']) && $_COOKIE['admincolor'] != "") {
		n2f_template::setGlobalField('admincolor', $_COOKIE['admincolor']);
	} else {
		n2f_template::setGlobalField('admincolor', 'blue');
	}

?>