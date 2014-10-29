<?php

	class package_search_model {
		public $keyword;


		public function validate_keyword() {
			$ret = new n2f_return();

			if (empty($this->keyword)) {
				$ret->isFail();
				$ret->addMsg('Invalid search string supplied.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function sanitize_keyword() {
			return(trim($this->keyword));
		}
	}

	class add_package_model {
		public $file = null;
		public $url = null;


		public function validate_file() {
			$ret = new n2f_return();

			if ($this->file === null) {
				$ret->isGood();
			} else {
				if ($this->file['error'] == UPLOAD_ERR_OK) {
					if (strtolower(substr($this->file['name'], -4)) != '.zip') {
						$ret->isFail();
						$ret->addMsg('Invalid file upload, only .zip files are allowed.');
					} else {
						$ret->isGood();
					}
				} else {
					$ret->isFail();

					switch ($this->file['error']) {
						case UPLOAD_ERR_INI_SIZE:
							$ret->addMsg('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
							break;
						case UPLOAD_ERR_FORM_SIZE:
							$ret->addMsg("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
							break;
						case UPLOAD_ERR_PARTIAL:
							$ret->addMsg('The uploaded file was only partially uploaded.');
							break;
						case UPLOAD_ERR_NO_FILE:
							$ret->isGood();
							$this->file = null;
							break;
						case UPLOAD_ERR_NO_TMP_DIR:
							$ret->addMsg('Missing a temporary folder.');
							break;
						case UPLOAD_ERR_CANT_WRITE:
							$ret->addMsg('Failed to write file to disk.');
							break;
						default:
							$ret->addMsg('There was an unrecognized error while uploading the file.');
							break;
					}
				}
			}

			return($ret);
		}

		public function validate_url() {
			$ret = new n2f_return();

			if ($this->url === null || empty($this->url)) {
				$ret->isGood();
			} else {
				if (strtolower(substr($this->url, -4)) != '.zip') {
					$ret->isFail();
					$ret->addMsg('Invalid file URL, only .zip files are allowed.');
				} else {
					$ret->isGood();
				}
			}

			return($ret);
		}
	}

	class list_package_model {
		public $changed;
		public $action;
	}

	function shortenAuthor($author) {
		return((strlen($author) > 11) ? substr($author, 0, 9) . '..' : $author);
	}

?>