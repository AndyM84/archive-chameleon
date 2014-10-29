<?php

	/**
	 * Class to handle text boxes for the formhelper.
	 *
	 */
	class fh_textbox extends formhelper_fieldbase {
		/**
		 * Initializes a new textbox handler.
		 *
		 */
		public function __construct() {
			$this->tagName = 'textbox';
		}

		/**
		 * Method to fetch a rendered textbox for template display.
		 *
		 * @param string $keyName	String value to use as textbox name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered textbox.
		 */
		public function fetch($keyName, array $attributes) {
			$element = "<input type=\"text\" name=\"{$keyName}\"";

			if (isset($attributes['model_value'])) {
				$element .= ' value="' . str_replace(array('"', "'"), array('&#34;', '&#39;'), $attributes['model_value']) . '"';
			}

			$element .= getInputAttributes($attributes);
			$element .= ' />';

			return($element);
		}
	}

?>