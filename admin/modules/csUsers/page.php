<?php

	$c = chameleon::getInstance();
	$c->doAuth('cs_users', CS_PERMS_MANAGE_USERS, './', './', './?nmod=csUsers');

	$c->setLayoutModule('main')->setLayoutFile('layout');

	$db = n2f_database::getInstance();
	$sess = n2f_session::getInstance(null, 'user-mgr');
	$infoboxes = new info_boxes(N2F_DEBUG_ERROR, true);
	$tplFields = array();
	$tplFile = 'list';

	if (!isset($_REQUEST['page'])) {
		$_REQUEST['page'] = 'list';
	}

	$user_search = new user_search_model();
	$user_modify = new add_user_model();
	$user_lister = new list_user_model();

	if (isset($_REQUEST['clearsearch']) && $_REQUEST['clearsearch'] == 'true') {
		$sess->delete('search-keyword');
		$sess->delete('search-orderBy');
		$sess->delete('search-orderDir');

		header('Location: ./?nmod=csUsers');
		exit;
	}

	if ($sess->exists('search-keyword')) {
		$user_search->keyword = $sess->get('search-keyword');
	}

	$fhs = new formhelper('user-search', $user_search);
	$fhm = new formhelper('user-modify', $user_modify);
	$fhl = new formhelper('user-lister', $user_lister);

	if ($fhm->isPosted()) {
		if ($fhm->isValid()) {
			if ($_REQUEST['page'] == 'edit') {
				$usr = new chameleon_user(intval($fhm->model->userId));

				if ($usr->userId < 1) {
					$infoboxes->throwError('', 'Invalid user identifier supplied, please try again.', '');
				} else {
					$usr->username = $fhm->model->username;
					$usr->email = $fhm->model->email;
					$usr->dateJoined = $fhm->model->dateJoined . ' 00:00:01';
					$usr->status = intval($fhm->model->status);
					$ret = $usr->update(true);

					if (!IsSuccess($ret)) {
						$infoboxes->throwError('', 'Unable to update user information, please try again.', '');
						$infoboxes->throwNotice('', debugEcho($ret, true), '');
					} else {
						$infoboxes->throwSuccess('', 'User information was successfully updated.', '');

						$uform = $c->doUserForm($usr, $_REQUEST);

						if (is_array($uform) && count($uform) > 0) {
							foreach (array_values($uform) as $result) {
								if ($result['returned'] instanceof n2f_return && !$result['returned']->isSuccess() && $result['returned']->hasMsgs()) {
									foreach (array_values($result['returned']->msgs) as $msg) {
										$infoboxes->throwNotice('', $msg, '');
									}
								}
							}
						}
					}

					if (!empty($fhm->model->password)) {
						$usr->salt = $usr->generateSalt();
						$usr->saltExpire = date('Y-m-d G:i:s', (time() + 2592000));
						$usr->password = encStr($fhm->model->password . $usr->salt);
						$ret = $usr->update(true);

						if (!IsSuccess($ret)) {
							$infoboxes->throwError('', 'Failed to update user password, please try again.', '');
						} else {
							$infoboxes->throwSuccess('', 'User password was successfully updated.', '');
						}
					}

					$c->doUserFormLoad($usr);
				}
			} else {
				if (!isset($fhm->model->password) || empty($fhm->model->password)) {
					$infoboxes->throwError('', 'You must supply a valid password for the user.', '');
				} else {
					$usr = new chameleon_user();
					$usr->username = $fhm->model->username;
					$usr->salt = $usr->generateSalt();
					$usr->saltExpire = date('Y-m-d G:i:s', (time() + 2592000));
					$usr->dateJoined = $fhm->model->dateJoined . ' 00:00:01';
					$usr->email = $fhm->model->email;
					$usr->password = encStr($fhm->model->password . $usr->salt);
					$usr->status = ($usr->userId == 1) ? 1 : intval($fhm->model->status);
					$ret = $usr->create();

					if (!IsSuccess($ret)) {
						$infoboxes->throwError('', 'Failed to create user, please try again.', '');
					} else {
						$infoboxes->throwSuccess('', 'User was successfully created.', '');
						$user_modify->userId = 0;
						$user_modify->username = "";
						$user_modify->email = "";
						$user_modify->password = "";
						$user_modify->confirmPassword = "";
						$user_modify->dateJoined = "";
						$user_modify->status = 0;
						unset($_POST);

						$uform = $c->doUserForm($usr, $_REQUEST);

						if (is_array($uform) && count($uform) > 0) {
							foreach (array_values($uform) as $result) {
								if ($result['returned'] instanceof n2f_return && !$result['returned']->isSuccess() && $result['returned']->hasMsgs()) {
									foreach (array_values($result['returned']->msgs) as $msg) {
										$infoboxes->throwNotice('', $msg, '');
									}
								}
							}
						}
					}
				}
			}
		} else {
			$errs = $fhm->getErrors();

			if (count($errs) > 0) {
				foreach (array_values($errs) as $msg) {
					$infoboxes->throwError('', $msg, '');
				}
			} else {
				$infoboxes->throwError('', 'The form was invalid when posted, please try again.', '');
			}
		}
	} else {
		if ($_REQUEST['page'] == 'edit' && (!isset($_REQUEST['uid']) || empty($_REQUEST['uid']))) {
			$infoboxes->throwError('', 'Missing user identifier, please check link and try again.', '');
			$_REQUEST['page'] = 'list';
		} else if ($_REQUEST['page'] == 'edit') {
			$usr = new chameleon_user(intval($_REQUEST['uid']));

			if ($usr->userId < 1) {
				$infoboxes->throwError('', 'Invalid user identifier or user not found, please check link and try again.', '');
				$_REQUEST['page'] = 'list';
			} else {
				$user_modify->userId = $usr->userId;
				$user_modify->username = $usr->username;
				$user_modify->email = $usr->email;
				$user_modify->password = '';
				$user_modify->dateJoined = substr($usr->dateJoined, 0, 10);
				$user_modify->status = $usr->status;

				$fhm = new formhelper('user-modify', $user_modify);
				$c->doUserFormLoad($usr);
			}
		}
	}

	if ($fhl->isPosted()) {
		if ($fhl->isValid()) {
			if (is_array($fhl->model->changed) && count($fhl->model->changed) > 0) {
				if ($fhl->model->action == 'permissions') {
					$sess->set('perm-users', $fhl->model->changed);

					header('Location: ./?nmod=csUsers&page=perms');
					exit;
				}

				foreach (array_values($fhl->model->changed) as $uid) {
					$usr = new chameleon_user(intval($uid));

					if ($usr->userId < 1) {
						$infoboxes->throwWarning('', 'Invalid user identifier (' . $uid . ').', '');

						continue;
					}

					if ($usr->userId == 1 && $c->user->userId != 1) {
						$infoboxes->throwWarning('', 'The main administrator can not be modified by other accounts.', '');

						continue;
					}

					if ($usr->hasPerm('cs_users', CS_PERMS_SUPER_ADMIN) && !$c->user->hasPerm('cs_users', CS_PERMS_SUPER_ADMIN)) {
						$infoboxes->throwWarning('', 'Only super administrators are able to act on other super administrators.', '');

						continue;
					}

					if ($fhl->model->action == 'activate') {
						$usr->status = ($usr->status == 1) ? 0 : 1;
						$ret = $usr->update(true);

						if (!IsSuccess($ret)) {
							$infoboxes->throwError('', "Failed to change activation for '{$usr->username}'.", '');
						} else {
							$infoboxes->throwSuccess('', "Successfully changed activation for '{$usr->username}'.", '');
						}
					} else if ($fhl->model->action == 'delete') {
						if ($usr->userId == $c->user->userId) {
							$infoboxes->throwError('', 'You cannot delete your own account.', '');

							continue;
						}

						$ret = $usr->delete();

						if (!IsSuccess($ret)) {
							$infoboxes->throwError('', "Failed to delete '{$usr->username}'.", '');
						} else {
							$infoboxes->throwSuccess('', "Successfully deleted account for '{$usr->username}'.", '');
						}
					}
				}
			} else {
				$infoboxes->throwWarning('', 'You must select at least one user to modify.', '');
			}
		} else {
			$errs = $fhl->getErrors();

			if (count($errs) > 0) {
				foreach (array_values($errs) as $msg) {
					$infoboxes->throwError('', $msg, '');
				}
			} else {
				$infoboxes->throwError('', 'The form was invalid when posted, please try again.', '');
			}
		}
	}

	if ($fhs->isPosted()) {
		if ($fhs->isValid()) {
			$sess->set('search-keyword', $fhs->model->keyword);
		}
	}

	if ($_REQUEST['page'] == 'perms' && $sess->exists('perm-users')) {
		$users = $sess->get('perm-users');

		if (!is_array($users) || count($users) < 1) {
			$_REQUEST['page'] = 'list';
			$sess->delete('perm-users');
			$infoboxes->throwError('', 'Invalid user set supplied for permissions page.', '');
		} else {
			$perms = array();
			$pQuery = $db->storedQuery(CS_SQL_SELECT_ALL_PERMS_WITH_PKG)->execQuery();

			if (!$pQuery->isError() && $pQuery->numRows() > 0) {
				$perms = $pQuery->fetchRows();
			}

			if (count($perms) < 1) {
				$_REQUEST['page'] = 'list';
				$sess->delete('perm-users');
				$infoboxes->throwError('', 'No permissions returned by database, pretty serious shtuff.', '');
			} else {
				$permList = array();

				foreach (array_values($perms) as $perm) {
					if (!isset($permList[$perm['pkg_key']])) {
						$permList[$perm['pkg_key']] = array(
							'pkg_key' 	=> $perm['pkg_key'],
							'name'		=> $perm['name'],
							'perms'		=> array()
						);
					}

					$permList[$perm['pkg_key']]['perms'][$perm['key']] = array(
						'key'	=> $perm['key'],
						'label'	=> $perm['label'],
						'permId'	=> $perm['permId'],
						'checked'	=> 'false'
					);
				}

				if (isset($_POST['action']) && $_POST['action'] == 'Save') {
					foreach (array_values($users) as $uid) {
						$usr = new chameleon_user(intval($uid));

						if ($usr->userId < 1) {
							$infoboxes->throwError('', "Failed to update permissions for userId '{$uid}', invalid.", '');

							continue;
						}

						if ($usr->userId == 1) {
							$infoboxes->throwWarning('', 'Not able to set permissions for root user, skipped.', '');

							continue;
						}

						if ($usr->userId == $c->user->userId) {
							$infoboxes->throwWarning('', 'You cannot set your own permissions, skipped.', '');

							continue;
						}

						foreach (array_values($permList) as $pkg) {
							foreach (array_values($pkg['perms']) as $perm) {
								if (isset($_POST['perms'][$perm['permId']]) && $_POST['perms'][$perm['permId']] == '1') {
									$usr->addPerm($pkg['pkg_key'], $perm['key']);
								} else {
									$usr->delPerm($pkg['pkg_key'], $perm['key']);
								}
							}
						}

						$infoboxes->throwSuccess('', "Successfully updated permissions for '{$usr->username}'.", '');
					}
				}

				if (count($users) == 1) {
					foreach ($permList as $pkg_key => $pkg) {
						foreach ($pkg['perms'] as $key => $perm) {
							$permList[$pkg_key]['perms'][$key]['checked'] = (chameleon_user::userHasPerm($users[0], $pkg_key, $key, false)) ? 'true' : 'false';
						}
					}
				} else if (count($users) > 1 && isset($_POST['action']) && $_POST['action'] == 'Save') {
					foreach ($permList as $pkg_key => $pkg) {
						foreach ($pkg['perms'] as $key => $perm) {
							$permList[$pkg_key]['perms'][$key]['checked'] = (isset($_POST['perms'][$perm['permId']]) && $_POST['perms'][$perm['permId']] == '1') ? 'true' : 'false';
						}
					}
				}

				$tplFields['permList'] = $permList;
			}
		}
	} else {
		$sess->delete('perm-users');
	}

	switch ($_REQUEST['page']) {
		case 'add':
			$tplFile = 'form';
			$c->doUserFormLoad(new chameleon_user());

			break;
		case 'edit':
			$tplFile = 'form';

			break;
		case 'perms':
			$tplFile = 'perms';

			break;
		case 'list':
		default:
			if ($sess->exists('current_page')) {
				if (isset($_REQUEST['pagenum'])) {
					$sess->set('current_page', $_REQUEST['pagenum']);
				} else {
					$_REQUEST['pagenum'] = $sess->get('current_page');
				}
			} else {
				$sess->set('current_page', (isset($_REQUEST['pagenum'])) ? $_REQUEST['pagenum'] : 1);
			}

			$orderBy = ($sess->exists('search-orderBy')) ? $sess->get('search-orderBy') : 'userId';
			$orderDir = ($sess->exists('search-orderDir')) ? $sess->get('search-orderDir') : 'ASC';
			$search = ($sess->exists('search-keyword')) ? $sess->get('search-keyword') : null;
			$paginate = new n2f_paginate(((isset($_REQUEST['pagenum'])) ? intval($_REQUEST['pagenum']) : 1), chameleon_user::searchUserCount($search), 10);
			$tplFields['users'] = chameleon_user::searchUsers($search, $paginate->offset, $paginate->per_page, $orderBy, $orderDir);
			$tplFields['paginate'] = $paginate;

			if ($paginate->curr_page < $sess->get('current_page')) {
				$sess->set('current_page', $paginate->curr_page);
			}

			break;
	}

	$tpl = new n2f_template('dynamic');
	$tpl->setModule('csUsers')->setFile($tplFile);

	if (count($tplFields) > 0) {
		$tpl->setFields($tplFields);
	}

	$tpl->render();

	$c->setField('PageTitle', 'User Management');
	$c->registerContent(CS_CONTENT_ADMIN_MAIN, $tpl->fetch());

	$c->render();

?>