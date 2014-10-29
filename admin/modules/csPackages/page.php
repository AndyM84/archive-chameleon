<?php

	$c = chameleon::getInstance();
	$c->doAuth('cs_users', CS_PERMS_MANAGE_PACKAGES, './', './', './?nmod=csPackages');

	$c->setLayoutModule('main')->setLayoutFile('layout');

	$db = n2f_database::getInstance();
	$sess = n2f_session::getInstance(null, 'package-mgr');
	$infoboxes = new info_boxes(N2F_DEBUG_ERROR, true);
	$tplFields = array();

	recoverInfoboxesFromSession($sess, $infoboxes);

	$package_search = new package_search_model();
	$package_upload = new add_package_model();
	$package_lister = new list_package_model();

	if (isset($_REQUEST['clearsearch']) && $_REQUEST['clearsearch'] == 'true') {
		$sess->delete('search-keyword');

		header('Location: ./?nmod=csPackages');
		exit;
	}

	if ($sess->exists('search-keyword')) {
		$package_search->keyword = $sess->get('search-keyword');
	}

	$fhs = new formhelper('package-search', $package_search);
	$fhu = new formhelper('package-upload', $package_upload);
	$fhl = new formhelper('package-lister', $package_lister);

	$doReload = false;

	if ($fhu->isPosted()) {
		$doReload = true;

		if ($fhu->isValid()) {
			if ($fhu->model->file === null && ($fhu->model->url === null || empty($fhu->model->url))) {
				$infoboxes->throwError('', 'You must upload a file or specify a file location URL.', '');
			} else {
				if ($fhu->model->file !== null) {
					if (!is_dir('resources/upload_tmp')) {
						mkdir('resources/upload_tmp');
					}

					$tmpname = "resources/upload_tmp/{$fhu->model->file['name']}";

					if (@move_uploaded_file($fhu->model->file['tmp_name'], $tmpname)) {
						$install = $c->installPackage($tmpname, false);

						if (IsSuccess($install)) {
							$infoboxes->throwSuccess('', 'Successfully installed the ' . $install->data['name'] . ' package.', '');
						} else {
							$infoboxes->throwError('', 'Failed to install the package, more detail may be offered below.', '');

							if ($install->hasMsgs()) {
								foreach (array_values($install->msgs) as $msg) {
									$infoboxes->throwError('', '- ' . $msg, '');
								}
							}
						}

						unlink($tmpname);
					} else {
						$infoboxes->throwError('', 'Failed to move uploaded file to temp directory.', '');
					}
				} else if ($fhu->model->url !== null && !empty($fhu->model->url)) {
					$contents = @file_get_contents($fhu->model->url);

					if ($contents !== false) {
						if (!is_dir('resources/upload_tmp')) {
							mkdir('resources/upload_tmp', 0777);
						}

						$pathinfo = pathinfo($fhu->model->url);
						$tmpname = "resources/upload_tmp/{$pathinfo['filename']}";

						if (@file_put_contents($tmpname, $contents)) {
							$install = $c->installPackage($tmpname, false);

							if (IsSuccess($install)) {
								$infoboxes->throwSuccess('', 'Successfully installed the ' . $install->data['name'] . ' package.', '');
							} else {
								$infoboxes->throwError('', 'Failed to install the package, more detail may be offered below.', '');

								if ($install->hasMsgs()) {
									foreach (array_values($install->msgs) as $msg) {
										$infoboxes->throwError('', '- ' . $msg, '');
									}
								}
							}

							unlink($tmpname);
						} else {
							$infoboxes->throwError('', 'Failed to move downloaded file to temp directory.', '');
						}
					} else {
						$infoboxes->throwError('', 'Failed to download file from URL, please check URL and try again.', '');
					}
				} else {
					$infoboxes->throwWarning('', 'Buffer overflow error, this may be bad...', '');
				}
			}
		} else {
			$infoboxes->throwError('', 'Failed to upload file, or something along those lines.', '');
			$errors = $fhu->getErrors();

			if (count($errors) > 0) {
				foreach (array_values($errors) as $error) {
					$infoboxes->throwError('', $error, '');
				}
			}
		}
	}

	if ($fhl->isPosted() && $fhl->isValid()) {
		$doReload = true;

		if (is_array($fhl->model->changed) && count($fhl->model->changed) > 0) {
			foreach (array_values($fhl->model->changed) as $changed) {
				$res = $db->storedQuery(CS_SQL_SELECT_PACKAGE_BY_ID, array('packageId' => $changed));
				$res->execQuery();

				if (!$res->isError() && $res->numRows() > 0) {
					$package = $res->fetchRow();

					if ($package['key'] == 'cs_packages' || $package['key'] == 'cs_skins' || $package['key'] == 'cs_users') {
						continue;
					}

					if ($fhl->model->action == 'delete') {
						if ($package['baseExt'] != '' && $package['active'] == 1) {
							$recvr = "{$package['key']}_receiver";
							n2f_cls::getInstance()->loadExtension($package['baseExt']);

							if (class_exists($recvr) && is_subclass_of($recvr, 'chameleon_receiver')) {
								$cls = new $recvr();
								$ret = $cls->deactivate($c);

								if ($ret !== null && $ret instanceof n2f_return && !$ret->isSuccess()) {
									if ($ret->hasMsgs()) {
										foreach (array_values($ret->msgs) as $msg) {
											$infoboxes->throwError('', $msg, '');
										}
									} else {
										$infoboxes->throwError('', "There was an error while deleting the package.  Some cleanup may have failed.", '');
									}
								}
							}
						}

						if (IsSuccess($c->removePackage($package['key']))) {
							$db->storedQuery(CS_SQL_DELETE_ALL_PERMS_BY_PKGID, array('packageId' => $package['packageId']))->execQuery();
							$infoboxes->throwSuccess('', "Successfully deleted the '{$package['name']}' package.", '');
						} else {
							$infoboxes->throwError('', "Couldn't delete the '{$package['name']}' package.", '');
						}
					}

					if ($fhl->model->action == 'activate') {
						$activate = ($package['active'] == 1) ? false : true;
						$recvr = "{$package['key']}_receiver";

						if ($c->togglePackageActivation($package['key'], $activate)) {
							if ($activate) {
								if ($package['baseExt'] != '') {
									n2f_cls::getInstance()->loadExtension($package['baseExt']);

									if (class_exists($recvr) && is_subclass_of($recvr, 'chameleon_receiver')) {
										$cls = new $recvr();
										$ret = null;

										if (version_compare($package['version'], $package['upgradeFrom'], '<')) {
											$ret = $cls->upgrade($c, $package['upgradeFrom']);

											$db->storedQuery(CS_SQL_UPDATE_PACKAGE, array('upgradeFrom' => '0', 'packageId' => $package['packageId']))->execQuery();
										} else {
											$ret = $cls->activate($c);
										}

										if ($ret !== null && $ret instanceof n2f_return) {
											if (!IsSuccess($ret)) {
												$c->togglePackageActivation($package['key'], false);

												if ($ret->hasMsgs()) {
													foreach (array_values($ret->msgs) as $msg) {
														$infoboxes->throwError('', $msg, '');
													}
												} else {
													$infoboxes->throwError('', "There was an error while activating the package.  Package activation was cancelled.", '');
												}
											}
										}
									}
								}

								if (!$infoboxes->hasErrors()) {
									$infoboxes->throwSuccess('', "Successfully activated the '{$package['name']}' package.", '');
								}
							} else {
								if ($package['baseExt'] != '') {
									n2f_cls::getInstance()->loadExtension($package['baseExt']);

									if (class_exists($recvr) && is_subclass_of($recvr, 'chameleon_receiver')) {
										$cls = new $recvr();
										$ret = $cls->deactivate($c);

										if ($ret !== null && $ret instanceof n2f_return && !$ret->isSuccess()) {
											if (!IsSuccess($ret)) {
												if ($ret->hasMsgs()) {
													foreach (array_values($ret->msgs) as $msg) {
														$infoboxes->throwError('', $msg, '');
													}
												} else {
													$infoboxes->throwError('', "There was an error while deactivating the package.  Some cleanup may have failed.", '');
												}
											}
										}
									}
								}

								if (!$infoboxes->hasErrors()) {
									$infoboxes->throwSuccess('', "Successfully deactivated the '{$package['name']}' package.", '');
								}
							}
						} else {
							$infoboxes->throwError('', "Failed to toggle activation of the '{$package['name']}' package.", '');
						}
					}
				}
			}
		}
	}

	if ($fhs->isPosted()) {
		if ($fhs->isValid()) {
			$sess->set('search-keyword', $fhs->model->keyword);
		}
	}

	if ($doReload) {
		storeInfoboxesInSession($sess, $infoboxes);

		header('Location: ./?nmod=csPackages');
		exit;
	}

	if ($sess->exists('current_page')) {
		if (isset($_REQUEST['pagenum'])) {
			$sess->set('current_page', $_REQUEST['pagenum']);
		} else {
			$_REQUEST['pagenum'] = $sess->get('current_page');
		}
	} else {
		$sess->set('current_page', (isset($_REQUEST['pagenum'])) ? $_REQUEST['pagenum'] : 1);
	}

	$orderBy = 'packageId';
	$orderDir = 'ASC';
	$search = ($sess->exists('search-keyword')) ? $sess->get('search-keyword') : null;
	$paginate = new n2f_paginate(((isset($_REQUEST['pagenum'])) ? intval($_REQUEST['pagenum']) : 1), $c->selectPackageCount($search), 10);
	$tplFields['packages'] = $c->selectPackages($orderBy, $orderDir, $search, $paginate->offset, $paginate->per_page);
	$tplFields['paginate'] = $paginate;

	if ($paginate->curr_page < $sess->get('current_page')) {
		$sess->set('current_page', $paginate->per_page);
	}

	$tpl = new n2f_template('dynamic');
	$tpl->setModule('csPackages')->setFile('index');

	if (count($tplFields) > 0) {
		$tpl->setFields($tplFields);
	}

	$tpl->render();

	$c->setField('PageTitle', 'Package Management');
	$c->registerContent(CS_CONTENT_ADMIN_MAIN, $tpl->fetch(), 0);

	$c->render();

?>