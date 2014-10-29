<?php

	/**
	 * Class to handle drop-downs for the formhelper.
	 *
	 */
	class fh_dropdown extends formhelper_fieldbase {
		/**
		 * Initializes a new dropdown handler.
		 *
		 */
		public function __construct() {
			$this->tagName = 'dropdown';
		}

		/**
		 * Method to fetch a rendered dropdown for template display.
		 *
		 * @param string $keyName	String value to use as dropdown name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered dropdown.
		 */
		public function fetch($keyName, array $attributes) {
			if (!isset($attributes['data'])) {
				return('');
			}

			if (strpos($attributes['data'], '::') !== false) {
				$attributes['data'] = explode('::', $attributes['data']);
			}

			if (!is_callable($attributes['data'])) {
				return('');
			}

			$element = "<select name=\"{$keyName}\"" . getInputAttributes($attributes) . '>';

			$options = call_user_func($attributes['data']);

			if (count($options) > 0) {
				foreach ($options as $value => $text) {
					if (is_array($text) && count($text) > 0) {
						$element .= "<optgroup label=\"{$value}\">";

						foreach ($text as $val => $txt) {
							$element .= "<option value=\"{$val}\"";

							if (isset($attributes['model_value']) && $attributes['model_value'] == $val) {
								$element .= ' selected="selected"';
							}

							$element .= ">{$txt}</option>";
						}

						$element .= "</optgroup>";

						continue;
					}

					$element .= "<option value=\"{$value}\"";

					if (isset($attributes['model_value']) && $attributes['model_value'] == $value) {
						$element .= ' selected="selected"';
					}

					$element .= ">{$text}</option>";
				}
			}

			$element .= '</select>';

			return($element);
		}
	}

?>