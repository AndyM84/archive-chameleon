<?php

	/**
	 * Class to handle the textarea for the formhelper.
	 *
	 */
	class fh_textarea extends formhelper_fieldbase {
		/**
		 * Initialize a new textarea handler
		 *
		 */
		public function __construct() {
			$this->tagName = 'textarea';
		}

		/**
		 * Method to fetch a rendered textarea for template display.
		 *
		 * @param string $keyName	String value to use as textarea name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered textarea.
		 */
		public function fetch($keyName, array $attributes) {
			$element = "<textarea name=\"{$keyName}\"";

			if (isset($attributes['rows'])) {
				$element .= " rows=\"{$attributes['rows']}\"";
			}

			if (isset($attributes['cols'])) {
				$element .= " cols=\"{$attributes['cols']}\"";
			}

			$element .= getInputAttributes($attributes) . ">";

			if (isset($attributes['model_value'])) {
				$element .= $attributes['model_value'];
			}

			$element .= '</textarea>';

			return($element);
		}
	}

?>