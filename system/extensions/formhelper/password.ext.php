<?php

	/**
	 * Class to handle password for the formhelper.
	 *
	 */
	class fh_password extends formhelper_fieldbase {
		/**
		 * Initializes a new password handler.
		 *
		 */
		public function __construct() {
			$this->tagName = 'password';
		}

		/**
		 * Method to fetch a rendered password for template display.
		 *
		 * @param string $keyName	String value to use as password name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered password.
		 */
		public function fetch($keyName, array $attributes) {
			return("<input type=\"password\" name=\"{$keyName}\"" . getInputAttributes($attributes) . ' />');
		}
	}

?>