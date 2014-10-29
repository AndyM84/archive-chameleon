<?php

	if (!isset($_REQUEST['page'])) {
		$_REQUEST['page'] = 'main';
	}

	$tplFile = '';

	switch ($_REQUEST['page']) {
		case 'examples':
			$tplFile = 'examples';
			break;
		case 'main':
		default:
			$tplFile = 'index';
			break;
	}

	$c = chameleon::getInstance();
	$c->setLayoutModule('main')->setLayoutFile('layout');
	$c->setField('PageTitle', 'Chameleon Framework - Home');
	$c->registerContent(CS_CONTENT_FRONT_MAIN, getSimpleTpl('main', $tplFile));
	$c->render();

?>