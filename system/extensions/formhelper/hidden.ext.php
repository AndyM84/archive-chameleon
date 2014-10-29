<?php

	/**
	 * Class to handle hidden inputs for the formhelper.
	 *
	 */
	class fh_hidden extends formhelper_fieldbase {
		/**
		 * Initializes a new hidden input handler.
		 *
		 */
		public function __construct() {
			$this->tagName = 'hidden';
		}

		/**
		 * Method to fetch a rendered hidden input for template display.
		 *
		 * @param string $keyName	String value to use as hidden input name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered hidden input.
		 */
		public function fetch($keyName, array $attributes) {
			$element = "<input type=\"hidden\" name=\"{$keyName}\"";

			if (isset($attributes['model_value'])) {
				$element .= ' value="' . str_replace(array('"', "'"), array('&#39;', '&#34;'), $attributes['model_value']) . '"';
			}

			$element .= getInputAttributes($attributes);
			$element .= ' />';

			return($element);
		}
	}

?>