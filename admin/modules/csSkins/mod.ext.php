<?php

	class module_model {
		public $startmod;
		public $errormod;

		public function validate_startmod() {
			$ret = new n2f_return();

			if (empty($this->startmod) || $this->startmod === null) {
				$ret->isFail();
				$ret->addMsg("Invalid value supplied for start module.");
			} else {
				if ($this->startmod == 'main') {
					$ret->isGood();
				} else {
					$ret->isFail();
					$pkgs = array();
					$pQuery = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_ACTIVE_PACKAGES)->execQuery();

					if (!$pQuery->isError() && $pQuery->numRows() > 0) {
						$pkgs = $pQuery->fetchRows();
					}

					if (count($pkgs) > 0) {
						foreach (array_values($pkgs) as $package) {
							if ($package['startMod'] == $this->startmod) {
								$ret->isGood();;
							}
						}
					}

					if (!IsSuccess($ret)) {
						$ret->addMsg("Start module supplied is not associated with a valid package.");
					}
				}
			}

			return($ret);
		}

		public function sanitize_startmod() {
			return(trim($this->startmod));
		}

		public function validate_errormod() {
			$ret = new n2f_return();

			if (empty($this->errormod) || $this->errormod === null) {
				$ret->isFail();
				$ret->addMsg("Invalid value supplied for error module.");
			} else {
				if ($this->errormod == 'error') {
					$ret->isGood();
				} else {
					$ret->isFail();
					$pkgs = array();
					$pQuery = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_ACTIVE_PACKAGES)->execQuery();

					if (!$pQuery->isError() && $pQuery->numRows() > 0) {
						$pkgs = $pQuery->fetchRows();
					}

					if (count($pkgs) > 0) {
						foreach (array_values($pkgs) as $package) {
							if ($package['errorMod'] == $this->errormod) {
								$ret->isGood();
							}
						}
					}

					if (!IsSuccess($ret)) {
						$ret->addMsg("Error module supplied is not associated with a valid package.");
					}
				}
			}

			return($ret);
		}

		public function sanitize_errormod() {
			return(trim($this->errormod));
		}

		public static function getStartMods() {
			$pkgs = array();
			$pQuery = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_ACTIVE_PACKAGES)->execQuery();
			$ret = array('main' => 'Default');

			if (!$pQuery->isError() && $pQuery->numRows() > 0) {
				$pkgs = $pQuery->fetchRows();
			}

			if (count($pkgs) > 0) {
				foreach (array_values($pkgs) as $package) {
					if ($package['startMod'] != '') {
						$ret[$package['startMod']] = $package['name'];
					}
				}
			}

			return($ret);
		}

		public static function getErrorMods() {
			$pkgs = array();
			$pQuery = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_ACTIVE_PACKAGES)->execQuery();
			$ret = array('error' => 'Default');

			if (!$pQuery->isError() && $pQuery->numRows() > 0) {
				$pkgs = $pQuery->fetchRows();
			}

			if (count($pkgs) > 0) {
				foreach (array_values($pkgs) as $package) {
					if ($package['errorMod'] != '') {
						$ret[$package['errorMod']] = $package['name'];
					}
				}
			}

			return($ret);
		}
	}

	class list_skin_model {
		public $active;

		public function validate_active() {
			$ret = new n2f_return();

			if (empty($this->active) || intval($this->active) < 1) {
				$ret->isFail();
				$ret->addMsg("Invalid skin identifier provided.");
			} else {
				$query = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_SKIN, array('skinId' => intval($this->active)))->execQuery();

				if ($query->isError()) {
					$ret->isFail();
					$ret->addMsg("Failed to get skin data.");

					if (CS_ENABLE_DEV_MODE) {
						$ret->addMsg($query->fetchError());
					}
				} else if ($query->numRows() < 1) {
					$ret->isFail();
					$ret->addMsg("Skin data not found in database.");
				} else {
					$skin = $query->fetchRow();

					$query = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $skin['key']))->execQuery();

					if ($query->isError()) {
						$ret->isFail();
						$ret->addMsg("Failed to get skin package data.");

						if (CS_ENABLE_DEV_MODE) {
							$ret->addMsg($query->fetchError());
						}
					} else if ($query->numRows() < 1) {
						$ret->isFail();
						$ret->addMsg("Package data not found in database.");
					} else {
						$pkg = $query->fetchRow();

						if ($pkg['active'] == 1) {
							$ret->isGood();
						} else {
							$ret->isFail();
							$ret->addMsg("Package is not currently active and therefore can not be used as the active template.");
						}
					}
				}
			}

			return($ret);
		}
	}

	function shortenAuthor($author) {
		return((strlen($author) > 11) ? substr($author, 0, 9) . '..' : $author);
	}

?>