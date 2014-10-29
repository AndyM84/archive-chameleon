<?php

	/**
	 * Class to handle radio button groups for the formhelper.
	 *
	 */
	class fh_radio extends formhelper_fieldbase {
		/**
		 * Initializes a new radio group handler.
		 *
		 */
		public function __construct() {
			$this->tagName = 'radio';
		}

		/**
		 * Method to fetch a rendered the radio group for template display.
		 *
		 * @param string $keyName	String value to use as radio group name.
		 * @param array $attributes	Array of attributes provided by template.
		 * @return string			String value of rendered radio group.
		 */
		public function fetch($keyName, array $attributes) {
			if (!isset($attributes['data']) && !isset($attributes['value']) && !isset($attributes['text'])) {
				return('');
			}

			$groups = array();

			if (isset($attributes['data'])) {
				if (strpos($attributes['data'], '::') !== false) {
					$attributes['data'] = explode('::', $attributes['data']);
				}

				if (!is_callable($attributes['data'])) {
					return('');
				}

				$groups = call_user_func($attributes['data']);
			} else {
				$groups = array($attributes['value'] => $attributes['text']);
			}

			$element = '';

			if (count($groups) > 0) {
				foreach ($groups as $value => $text) {
					$element .= "<input type=\"radio\" name=\"{$keyName}\" value=\"{$value}\"";

					if (isset($attributes['model_value'])) {
						if ($attributes['model_value'] == $value) {
							$element .= ' checked="checked"';
						} else if (substr($attributes['value'], 0, 3) == '<%$' && substr($value, -2) == '%>') {
							$varname = substr($attributes['value'], 3, -2);
							$element .= '<% if (isset($_REQUEST[\'' . $keyName . '\']) && $_REQUEST[\'' . $keyName . '\'] == $' . $varname . '): %> checked="checked"<% endif; %>';
						}
					}

					$element .= " /> {$text}";

					if (count($groups) > 1) {
						$element .= "<br />";
					}
				}
			}

			return($element);
		}
	}

?>