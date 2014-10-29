<?php

	class setup_form_model {
		public $admin_username;
		public $admin_password;
		public $admin_password2;
		public $admin_email;
		public $db_host;
		public $db_port = 3306;
		public $db_name;
		public $db_prefix;
		public $db_user;
		public $db_pass;
		public $site_cookie_domain;
		public $site_path;
		public $enable_dev_mode;


		public function validate_admin_username() {
			$ret = new n2f_return();
			$ret->isGood();

			if (empty($this->admin_username) || strlen($this->admin_username) < 4) {
				$ret->isFail();
				$ret->addMsg("Invalid admin username, must be at least 6 character long.");
			}

			return($ret);
		}

		public function validate_admin_password() {
			$ret = new n2f_return();
			$ret->isGood();

			if (empty($this->admin_password) || empty($this->admin_password2) || $this->admin_password !== $this->admin_password2) {
				$ret->isFail();
				$ret->addMsg("Invalid admin password, must enter the same password twice.");
			}

			if ($ret->isSuccess() && strlen($this->admin_password) < 6) {
				$ret->isFail();
				$ret->addMsg("Invalid admin password, must be at least 6 characters long.");
			}

			return($ret);
		}

		public function validate_admin_email() {
			$ret = new n2f_return();
			$ret->isGood();

			$eml = new sanit_emailaddress($this->admin_email);

			if (!$eml->isValid()) {
				$ret->isFail();
				$ret->addMsg("Invalid email address provided.");
			}

			return($ret);
		}

		public function validate_db_host() {
			$ret = new n2f_return();
			$ret->isGood();

			if (empty($this->db_host)) {
				$ret->isFail();
				$ret->addMsg("Invalid database host, you must enter a hostname/IP for the database connection.");
			}

			return($ret);
		}

		public function validate_db_name() {
			$ret = new n2f_return();
			$ret->isGood();

			if (empty($this->db_name)) {
				$ret->isFail();
				$ret->addMsg("Invalid database name, you must enter the database name.");
			}

			return($ret);
		}



		public function sanitize_admin_username() {
			return(trim($this->admin_username));
		}

		public function sanitize_admin_password() {
			return(trim($this->admin_password));
		}

		public function sanitize_admin_email() {
			$eml = new sanit_emailaddress($this->admin_email);

			return($eml->getSanitized());
		}

		public function sanitize_db_host() {
			return(trim($this->db_host));
		}

		public function sanitize_db_port() {
			return((!empty($this->db_port)) ? intval(trim($this->db_port)) : 3306);
		}

		public function sanitize_db_name() {
			return(trim($this->db_name));
		}

		public function sanitize_db_prefix() {
			return((!empty($this->db_prefix)) ? trim($this->db_prefix) : '');
		}

		public function sanitize_site_cookie_domain() {
			return((!empty($this->site_cookie_domain)) ? trim($this->site_cookie_domain) : getSiteCookieDomain());
		}

		public function sanitize_site_path() {
			return((!empty($this->site_path)) ? trim($this->site_path) : getSitePath());
		}

		public function sanitize_enable_dev_mode() {
			return(($this->enable_dev_mode == 'true') ? 'true' : 'false');
		}
	}

	function getSiteCookieDomain() {
		$ret = '';

		// Break domain into parts
		$dparts = explode('.', $_SERVER['SERVER_NAME']);

		// If it's a <=2 part domain, just prepend the .
		if (count($dparts) < 3) {
			$ret = ".{$_SERVER['SERVER_NAME']}";
		} else { // otherwise, put it back together the way we need
			$ret = '.';

			// Start at 1 so we include all 'sub domains' from the current level
			for ($i = 1; $i < count($dparts); $i++) {
				if ($i > 1) {
					$ret .= '.';
				}

				$ret .= $dparts[$i];
			}
		}

		return($ret);
	}

	function getSitePath() {
		$ret = '';

		// Protocol
		$ret .= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
		// Domain
		$ret .= $_SERVER['SERVER_NAME'];
		// Port (if necessary)
		$ret .= ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) ? ":{$_SERVER['SERVER_PORT']}" : '';

		// Path
		$path = str_replace('setup/', '', $_SERVER['REQUEST_URI']);
		$ret .= (stripos($path, '?') !== false) ? substr($path, 0, stripos($path, '?')) : $path;

		return($ret);
	}

?>