<?php

	/**
	 * Class to handle check boxes for the formhelper.
	 *
	 */
	class fh_checkbox extends formhelper_fieldbase {
		/**
		 * Initializes a new checkbox handler.
		 *
		 */
		public function __construct() {
			$this->tagName = 'checkbox';
		}

		/**
		 * Method to fetch a rendered the checkbox for template display.
		 *
		 * @param string $keyName	String value to use as checkbox name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered checkbox.
		 */
		public function fetch($keyName, array $attributes) {
			if (!isset($attributes['value'])) {
				return('');
			}

			$element = "<input type=\"checkbox\" name=\"{$keyName}\" value=\"{$attributes['value']}\"";

			if (isset($attributes['model_value'])) {
				if (substr($attributes['value'], 0, 3) == '<%$' && substr($attributes['value'], -2) == '%>') {
					$varname = substr($attributes['value'], 3, -2);

					$element .= '<% if (isset($_REQUEST[\'' . $keyName . '\']) && $_REQUEST[\'' . $keyName . '\'] == $' . $varname . '): %> checked="checked"<% endif; %>';
				} else {
					if (is_array($attributes['model_value']) && count($attributes['model_value']) > 0) {
						foreach (array_values($attributes['model_value']) as $val) {
							if ($val == $attributes['value']) {
								$element .= ' checked="checked"';

								break;
							}
						}
					} else if ($attributes['model_value'] == $attributes['value']) {
						$element .= ' checked="checked"';
					}
				}
			}

			$element .= getInputAttributes($attributes) . ' />';

			return($element);
		}
	}

?>