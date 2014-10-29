<?php

	$c = chameleon::getInstance();
	$c->doAuth('cs_users', CS_PERMS_MANAGE_SKINS, './', './', './?nmod=csSkins');

	$c->setLayoutModule('main')->setLayoutFile('layout');

	$db = n2f_database::getInstance();
	$infoboxes = new info_boxes(N2F_DEBUG_ERROR, true);
	$tplFields = array('packages' => array());

	$skin_lister = new list_skin_model();
	$module_selector = new module_model();

	$module_selector->startmod = $c->getSetting('cs_front_defmod_start');
	$module_selector->errormod = $c->getSetting('cs_front_defmod_error');

	$query = $db->storedQuery(CS_SQL_SELECT_SKIN_BY_SKIN, array('skin' => $c->getSetting('cs_front_skin')))->execQuery();

	if (!$query->isError() && $query->numRows() > 0) {
		$skin_lister->active = $query->fetchResult(0, 'skinId');
	}

	$fh = new formhelper('skin-lister', $skin_lister);
	$fhm = new formhelper('module-selector', $module_selector);

	if ($fh->isPosted()) {
		if ($fh->isValid()) {
			$query = $db->storedQuery(CS_SQL_SELECT_SKIN, array('skinId' => $fh->model->active))->execQuery();
			$c->putSetting('cs_front_skin', $query->fetchResult(0, 'skin'));
			$infoboxes->throwSuccess('', 'Active skin successfully change.', '');
		} else {
			if (count($fh->getErrors()) > 0) {
				foreach (array_values($fh->getErrors()) as $msg) {
					$infoboxes->throwError('', $msg, '');
				}
			}
		}
	} else {
		$_POST['active'] = $skin_lister->active;
	}

	if ($fhm->isPosted()) {
		if ($fhm->isValid()) {
			$c->putSettings(array(
				'cs_front_defmod_start' => $fhm->model->startmod,
				'cs_front_defmod_error' => $fhm->model->errormod
			));
			$infoboxes->throwSuccess('', "Successfully changed default modules for front-end.", '');
		} else {
			$infoboxes->throwError('', "There was an error changing the default modules, please try again.", '');
			$infoboxes->throwNotice('', debugEcho($fhm->getErrors(), true), '');
		}
	}

	$query = $db->storedQuery(CS_SQL_SELECT_ALL_SKINS)->execQuery();

	if (!$query->isError() && $query->numRows() > 0) {
		$skins = $query->fetchRows();

		foreach (array_values($skins) as $skin) {
			$pQuery = $db->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $skin['key']))->execQuery();

			if (!$pQuery->isError() && $pQuery->numRows() > 0) {
				$pkg = $pQuery->fetchRow();

				if ($pkg['active'] == 1) {
					if ($pkg['packageId'] == 2) {
						$pkg['name'] = 'Default Skin';
					}

					$pkg['skinId'] = $skin['skinId'];
					$pkg['skin'] = $skin['skin'];
					$tplFields['packages'][] = $pkg;
				}
			}
		}
	}

	$tpl = new n2f_template('dynamic');
	$tpl->setModule('csSkins')->setFile('index');

	if (count($tplFields) > 0) {
		$tpl->setFields($tplFields);
	}

	$tpl->render();

	$c->setField('PageTitle', 'Skin Management');
	$c->registerContent(CS_CONTENT_ADMIN_MAIN, $tpl->fetch());
	$c->render();

?>