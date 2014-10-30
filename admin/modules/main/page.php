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

	if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'forgot') {

		$fh = new formhelper('forgot-form', 'forgot_model');

		if ($fh->isPosted()) {
			if ($fh->isValid()) {
				$forgot = $c->doForgotPassword($fh->model->username);

				if (IsSuccess($forgot)) {
					if (count($forgot->msgs) > 0) {
						foreach (array_values($forgot->msgs) as $msg) {
							$infoboxes->throwSuccess('', $msg, '');
						}
					} else {
						$infoboxes->throwSuccess('', 'You should receive an email shortly.<br />Check your spam folder if you do not see it.', '');
					}
				} else {
					if (count($forgot->msgs) > 0) {
						foreach (array_values($forgot->msgs) as $msg) {
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

		$layoutFile = 'forgot';

	} else if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'reset') {

		if (!isset($_REQUEST['confirm'])) {
			header("Location: ./");
			exit;
		}

		$layoutFile = 'forgot_reset';

		$user = chameleon_user::getUserByConfirmCode($_REQUEST['confirm']);
		if ($user->userId > 0) {

			$fh = new formhelper('reset-form', 'reset_model');
			if ($fh->isPosted()) {
				if ($fh->isValid()) {
					$reset = $c->doPasswordReset($user, $fh->model->password);
					if (IsSuccess($reset)) {
						$infoboxes->throwSuccess('', 'Successfully reset the password, you can now <a href="./">Login</a>.', '');

						$layoutFile = 'login';
						$fh = new formhelper('login-form', 'login_model');
					} else {
						if (count($reset->msgs) > 0) {
							foreach (array_values($reset->msgs) as $msg) {
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
		} else {
			$infoboxes->throwError('', 'Unable to find the confirmation code.', '');
		}

		$c->setField('confirm', $_REQUEST['confirm']);
	} else {

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
	}

	$c->setLayoutModule('main')->setLayoutSkin('default')->setLayoutFile($layoutFile);
	$c->setField('PageTitle', 'Chameleon Administration');
	$c->render();

?>