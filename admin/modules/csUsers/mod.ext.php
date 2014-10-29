<?php

	class user_search_model {
		public $keyword;


		public function validate_keyword() {
			$ret = new n2f_return();

			if (empty($this->keyword)) {
				$ret->isFail();
				$ret->addMsg('Invalid search string supplied');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function sanitize_keyword() {
			return(trim($this->keyword));
		}
	}

	class add_user_model {
		public $userId;
		public $username;
		public $email;
		public $password;
		public $confirmPassword;
		public $dateJoined;
		public $status;


		public function validate_username() {
			$ret = new n2f_return();

			if (!isset($this->username) || empty($this->username)) {
				$ret->isFail();
				$ret->addMsg('Invalid username supplied.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function validate_email() {
			$ret = new n2f_return();

			if (!validEmail($this->email)) {
				$ret->isFail();
				$ret->addMsg('Invalid email address supplied.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function validate_password() {
			$ret = new n2f_return();

			if (!isset($this->password) || empty($this->password)) {
				if (!isset($this->confirmPassword) || empty($this->confirmPassword)) {
					$ret->isGood();
				} else {
					$ret->isFail();
					$ret->addMsg('Invalid password supplied.');
				}
			} else if ($this->password !== $this->confirmPassword) {
				$ret->isFail();
				$ret->addMsg('Both passwords must match.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function validate_dateJoined() {
			$ret = new n2f_return();

			$parts = explode('-', $this->dateJoined);

			if (count($parts) != 3) {
				$ret->isFail();
				$ret->addMsg('Invalid date format supplied.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function validate_status() {
			$ret = new n2f_return();

			if ($this->status != '1' && $this->status != '0') {
				$ret->isFail();
				$ret->addMsg('Invalid status supplied.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function sanitize_userId() {
			return(intval($this->userId));
		}

		public function sanitize_username() {
			return(trim($this->username));
		}

		public function sanitize_email() {
			return(trim($this->email));
		}

		public function sanitize_password() {
			return(trim($this->password));
		}

		public function sanitize_dateJoined() {
			return(trim($this->dateJoined));
		}

		public function sanitize_status() {
			return(intval($this->status));
		}

		public static function getStatuses() {
			return(array(
				'0'	=> 'Inactive',
				'1'	=> 'Active'
			));
		}
	}

	class list_user_model {
		public $changed;
		public $action;
	}

?>