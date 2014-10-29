<?php

	$c = chameleon::getInstance();
	$layoutFile = 'login';
	$infoboxes = new info_boxes(N2F_DEBUG_ERROR, true);
	$db = n2f_database::getInstance();
	$sess = n2f_session::getInstance();
	$authSess = n2f_session::getInstance(null, 'auth');

	if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == 'true') {
		$c->doLogout();

		header('Location: ./');
		exit;
	}

	$fh = new formhelper('login-form', 'login_model');

	if ($fh->isPosted()) {
		if ($fh->isValid()) {
			$login = $c->doLogin($fh->model->username, $fh->model->password, (($fh->model->remember == "true") ? true : false));

			if (IsSuccess($login)) {
				$sess->set('curr_user', $login->data);
				$sess->set('lasttime', time());

				if ($authSess->exists('redir')) {
					$redir = $authSess->get('redir');
					$authSess->delete('redir');
					$authSess->delete('message');

					header('Location: ' . $redir);
					exit;
				} else {
					header('Location: ./');
					exit;
				}
			} else {
				if (count($login->msgs) > 0) {
					foreach (array_values($login->msgs) as $msg) {
						$infoboxes->throwError('', $msg, '');
					}
				}
			}
		} else {
			$errors = $fh->getErrors();

			if (count($errors) > 0) {
				foreach (array_values($errors) as $msg) {
					$infoboxes->throwError('', $msg, '');
				}
			}
		}
	}

	if ($c->user->userId > 0) {
		if ($c->checkUserPerm('cs_users', CS_PERMS_ACCESS_ADMIN)) {
			$layoutFile = 'layout';
			$c->registerContent(CS_CONTENT_ADMIN_MAIN, getSimpleTpl('main', 'landing'));
		} else {
			header('Location: ../');
			exit;
		}
	} else {
		if ($authSess->exists('message')) {
			$infoboxes->throwWarning('', $authSess->get('message'), '');
		}
	}

	$c->setLayoutModule('main')->setLayoutSkin('default')->setLayoutFile($layoutFile);
	$c->setField('PageTitle', 'Chameleon Administration');
	$c->render();

?>