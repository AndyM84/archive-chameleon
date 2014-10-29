<?php

	/**
	 * Class to handle file inputs for the formhelper.
	 *
	 */
	class fh_file extends formhelper_fieldbase {
		/**
		 * Initializes a new file handler.
		 *
		 */
		public function __construct() {
			$this->tagName = 'file';
		}

		/**
		 * Method to fetch a rendered file input for template display.
		 *
		 * @param string $keyName	String value to use as file input name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered file input.
		 */
		public function fetch($keyName, array $attributes) {
			$element = "<input type=\"file\" name=\"{$keyName}\"";

			if (isset($attributes['model_value'])) {
				$element .= ' value="' . str_replace(array('"', "'"), array('&#34;', '&#39;'), $attributes['model_value']) . '"';
			}

			$element .= getInputAttributes($attributes);
			$element .= ' />';

			return($element);
		}
	}

?>