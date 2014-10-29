<?php

	$configTokens = array('_%ENABLE_DEV_MODE%_', '_%DATABASE_PREFIX%_', '_%DATABASE_HOST%_', '_%DATABASE_PORT%_', '_%DATABASE_USER%_', '_%DATABASE_PASS%_', '_%DATABASE_NAME%_', '_%COOKIE_DOMAIN%_', '_%SITE_PATH%_');
	$configReplaces = array();
	$configContents = <<< END_CONFIG_FILE
<?php

	define('CS_CACHE_EXPIRATION',				86400);
	define('CS_ENABLE_DEV_MODE',				_%ENABLE_DEV_MODE%_);
	define('CS_REMEMBER_COOKIE',				'cs_uath');
	define('CS_DATABASE_TABLE_PREFIX',			'_%DATABASE_PREFIX%_');
	define('CS_DATABASE_TYPE',				'mysqli');
	define('CS_DATABASE_HOST',				'_%DATABASE_HOST%_');
	define('CS_DATABASE_PORT',				'_%DATABASE_PORT%_');
	define('CS_DATABASE_USER',				'_%DATABASE_USER%_');
	define('CS_DATABASE_PASS',				'_%DATABASE_PASS%_');
	define('CS_DATABASE_NAME',				'_%DATABASE_NAME%_');
	define('CS_COOKIE_DOMAIN',				'_%COOKIE_DOMAIN%_');
	define('CS_SITE_PATH',					'_%SITE_PATH%_');

?>
END_CONFIG_FILE;

	$infoboxes = new info_boxes(N2F_DEBUG_ERROR, true);
	$sess = n2f_session::getInstance(null, 'setup-sess');
	$tplFields = array();
	$tplFile = 'index';

	if (!isset($_REQUEST['page'])) {
		$_REQUEST['page'] = 'step1';
	}

	$form_model = new setup_form_model();
	$form_model->db_prefix = 'csf_';
	$form_model->site_cookie_domain = getSiteCookieDomain();
	$form_model->site_path = getSitePath();

	$form = new formhelper('step1', $form_model);

	if ($form->isPosted()) {
		if ($form->isValid()) {
			$tdb = n2f_database::setInstance(n2f_cls::getInstance(), 'mysqli', true, 'tdb', new n2f_cfg_db(array(
				'type' => 'mysqli',
				'host' => $form->model->db_host,
				'port' => $form->model->db_port,
				'name' => $form->model->db_name,
				'user' => $form->model->db_user,
				'pass' => $form->model->db_pass
			)));
			$tdb->open();

			if (!$tdb->isOpen()) {
				$infoboxes->throwError('', 'Failed to connect to database, please check database information and try again.', '');
			} else {
				$configReplaces[] = $form->model->enable_dev_mode;
				$configReplaces[] = $form->model->db_prefix;
				$configReplaces[] = $form->model->db_host;
				$configReplaces[] = $form->model->db_port;
				$configReplaces[] = $form->model->db_user;
				$configReplaces[] = $form->model->db_pass;
				$configReplaces[] = $form->model->db_name;
				$configReplaces[] = $form->model->site_cookie_domain;
				$configReplaces[] = $form->model->site_path;

				$configContents = str_replace($configTokens, $configReplaces, $configContents);
				$sess->set('admin_info', array($form->model->admin_username, $form->model->admin_password, $form->model->admin_email));

				if (!@file_put_contents(N2F_REL_PATH . "system/extensions/chameleon/config.ext.php", $configContents)) {
					$infoboxes->throwError('', "Failed to write configuration file.  Please check root directory ownership and try again.", '');
				} else {
					header('Location: ./?page=step2');
					exit;
				}
			}
		} else {
			$infoboxes->throwError('', 'There was a problem with your setup information.', '');

			$errs = $form->getErrors();

			if (count($errs) > 0) {
				foreach (array_values($errs) as $error) {
					$infoboxes->throwWarning('', $error, '');
				}
			}
		}
	} else {
		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Continue') {
			if ($_REQUEST['page'] == 'step2') {
				$admin_info = $sess->get('admin_info');
				$db = n2f_database::getInstance();

				$query = $db->storedQuery(CS_SQL_CREATE_PACKAGES_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Failed to create packages table.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				} else {
					$query = $db->storedQuery(CS_SQL_INSERT_PACKAGE, array('key' => 'cs_packages', 'modules' => serialize(array('admin/csPackages')), 'extensions' => '', 'files' => '', 'baseExt' => '', 'startMod' => '', 'errorMod' => '', 'name' => 'Chameleon Package Manager', 'author' => 'Chameleon Sites', 'description' => 'Core package to manage packages for site.', 'url' => 'http://chameleon-sites.com/', 'version' => '1.0', 'installed' => date('Y-m-d G:i:s'), 'upgradeFrom' => '0', 'active' => '1'))->execQuery();

					if ($query->isError()) {
						$infoboxes->throwError('', "Fatal Error: Could not install package manager.  Check configuration values.", '');

						if (CS_ENABLE_DEV_MODE) {
							$infoboxes->throwNotice('', $query->fetchError(), '');
						}
					}

					$query = $db->storedQuery(CS_SQL_INSERT_PACKAGE, array('key' => 'cs_skins', 'modules'	=> serialize(array('admin/csSkins')), 'extensions' => '', 'files' => '', 'baseExt' => '', 'startMod' => '', 'errorMod' => '', 'name' => 'Chameleon Skin Manager', 'author' => 'Chameleon Sites', 'description' => 'Core package to manage skins for site.', 'url' => 'http://chameleon-sites.com/', 'version' => '1.0', 'installed' => date('Y-m-d G:i:s'), 'upgradeFrom' => '0', 'active' => '1'))->execQuery();

					if ($query->isError()) {
						$infoboxes->throwError('', "Fatal Error: Could not install skin manager.  Check configuration values.", '');

						if (CS_ENABLE_DEV_MODE) {
							$infoboxes->throwNotice('', $query->fetchError(), '');
						}
					}

					$query = $db->storedQuery(CS_SQL_INSERT_PACKAGE, array('key' => 'cs_users', 'modules' => serialize(array('admin/csUsers')), 'extensions' => '', 'files' => '', 'baseExt' => '', 'startMod' => '', 'errorMod' => '', 'name' => 'Chameleon User Manager', 'author' => 'Chameleon Sites', 'description' => 'Core package to manage users for site.', 'url' => 'http://chameleon-sites.com/', 'version' => '1.0', 'installed' => date('Y-m-d G:i:s'), 'upgradeFrom' => '0', 'active' => '1'))->execQuery();

					if ($query->isError()) {
						$infoboxes->throwError('', "Fatal Error: Could not install user manager.  Check configuration values.", '');

						if (CS_ENABLE_DEV_MODE) {
							$infoboxes->throwNotice('', $query->fetchError(), '');
						}
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_SKINS_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', "Failed to create skins table.", '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				} else {
					$query = $db->storedQuery(CS_SQL_INSERT_SKIN, array('key' => 'cs_skins', 'skin' => 'default'))->execQuery();

					if ($query->isError()) {
						$infoboxes->throwError('', 'Could not add default skin to system.  Check configuration values.', '');

						if (CS_ENABLE_DEV_MODE) {
							$infoboxes->throwNotice('', $query->fetchError(), '');
						}
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_SETTINGS_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create settings table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_SETTINGS_LOG_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create settings log table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_USERS_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create users table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_USERS_LOG_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create users log table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_USERPERMS_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create user permissions table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_USERPERMS_LOG_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create user permissions log table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_PERMS_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create permissions table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_PERMS_LOG_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create permissions log table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$query = $db->storedQuery(CS_SQL_CREATE_USERSESS_TABLE)->execQuery();

				if ($query->isError()) {
					$infoboxes->throwError('', 'Could not create user sessions table.  Check configuration values.', '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $query->fetchError(), '');
					}
				}

				$pkg = $db->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => 'cs_users'))->execQuery();

				if ($pkg->isError() || $pkg->numRows() != 1) {
					$infoboxes->throwError('', "Packages table wasn't installed properly.  Check configuration values, installation is probably broken.", '');

					if (CS_ENABLE_DEV_MODE) {
						$infoboxes->throwNotice('', $pkg->fetchError(), '');
					}
				} else {
					$inspkg = $pkg->fetchResult(0, 'packageId');

					$perms = array(
						array('packageId' => $inspkg, 'key' => CS_PERMS_ACCESS_ADMIN, 'label' => 'Access Administration'),
						array('packageId' => $inspkg, 'key' => CS_PERMS_MANAGE_USERS, 'label' => 'Manage Users'),
						array('packageId' => $inspkg, 'key' => CS_PERMS_MANAGE_SKINS, 'label' => 'Manage Skins/Modules'),
						array('packageId' => $inspkg, 'key' => CS_PERMS_MANAGE_PACKAGES, 'label' => 'Manage Packages'),
						array('packageId' => $inspkg, 'key' => CS_PERMS_SUPER_ADMIN, 'label' => 'Super Administrator')
					);

					foreach (array_values($perms) as $perm) {
						$ins = $db->storedQuery(CS_SQL_INSERT_PERM, $perm)->execQuery();

						if ($ins->isError()) {
							$infoboxes->throwError('', "Fatal Error: Failed installing the '{$perm['key']}' permission, installation is broken.", '');

							if (CS_ENABLE_DEV_MODE) {
								$infoboxes->throwNotice('', $ins->fetchError(), '');
							}
						}
					}

					$usr = new chameleon_user();
					$usr->username = $admin_info[0];
					$usr->salt = $usr->generateSalt();
					$usr->saltExpire = date('Y-m-d G:i:s', (time() + 2592000));
					$usr->dateJoined = date('Y-m-d G:i:s');
					$usr->email = $admin_info[2];
					$usr->password = encStr($admin_info[1] . $usr->salt);
					$usr->status = 1;
					$ret = $usr->create();

					if (!$ret->isSuccess()) {
						$infoboxes->throwError('', 'Failed to create default administration user, installation is broken.', '');

						if (CS_ENABLE_DEV_MODE) {
							$infoboxes->throwNotice('', debugEcho($ret->msgs, true), '');
						}
					}

					$usr->addPerm('cs_users', CS_PERMS_SUPER_ADMIN);

					$c = chameleon::getInstance();

					$c->putSetting('cs_version', CS_VERSION);
					$c->putSetting('cs_installed', date('Y-m-d G:i:s'));
					$c->putSetting('cs_front_skin', 'default');
					$c->putSetting('cs_admin_skin', 'default');
					$c->putSetting('cs_front_defmod_start', 'main');
					$c->putSetting('cs_front_defmod_error', 'error');
					$c->putSetting('cs_admin_defmod_start', 'admin/main');
					$c->putSetting('cs_admin_defmod_error', 'admin/error');
					$c->putSetting('cs_admin_defmod_users', 'admin/csUsers');

					if (!$infoboxes->hasErrors()) {
						header('Location: ./?page=step3');
						exit;
					}
				}
			}

			if ($_REQUEST['page'] == 'step3') {
				header('Location: ../admin/');
				exit;
			}
		}
	}

	switch ($_REQUEST['page']) {
		case 'phpinfo':
			phpinfo();
			exit;
		case 'step2':
			$tplFile = 'install';
			break;
		case 'step3':
			$tplFile = 'finish';
			break;
		case 'step1':
		default:
			$tplFile = 'index';
			break;
	}

	$tpl = new n2f_template('dynamic');
	$tpl->setModule('main')->setFile($tplFile);
	$tpl->render()->display();

?>