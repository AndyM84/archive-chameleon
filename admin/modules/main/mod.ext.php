<?php

	class login_model {
		public $username = 'username';
		public $password;
		public $remember = null;


		public function validate_username() {
			$ret = new n2f_return();

			if (empty($this->username) || $this->username == 'username') {
				$ret->isFail();
				$ret->addMsg('You must supply a valid username.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function sanitize_username() {
			return(trim($this->username));
		}

		public function validate_password() {
			$ret = new n2f_return();

			if (empty($this->password)) {
				$ret->isFail();
				$ret->addMsg('You must supply a valid password');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function sanitize_password() {
			return(trim($this->password));
		}

		public function sanitize_remember() {
			return(($this->remember === null) ? false : $this->remember);
		}
	}

	class forgot_model {
		public $username;

		public function sanitize_username() {
			return(trim($this->username));
		}

		public function validate_username() {
			$ret = new n2f_return();

			if (empty($this->username) || $this->username == 'username') {
				$ret->isFail();
				$ret->addMsg('You must supply a valid username.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}
	}

	class reset_model {
		public $password;
		public $confirm_pass;

		public function validate_password() {
			$ret = new n2f_return();

			if (empty($this->password)) {
				$ret->isFail();
				$ret->addMsg('You must supply a valid password');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function validate_confirm_pass() {
			$ret = new n2f_return();

			if ($this->password != $this->confirm_pass) {
				$ret->isFail();
				$ret->addMsg('The confirmation password does not match.');
			} else {
				$ret->isGood();
			}

			return($ret);
		}

		public function sanitize_password() {
			return(trim($this->password));
		}

		public function sanitize_confirm_pass() {
			return(trim($this->confirm_pass));
		}
	}

?>