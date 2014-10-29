<?php

	$default_fields = array(
		'textbox',
		'dropdown',
		'password',
		'hidden',
		'textarea',
		'checkbox',
		'radio',
		'file'
	);

	foreach (array_values($default_fields) as $ftype) {
		n2f_cls::getInstance()->loadExtension("formhelper/{$ftype}");
	}

	n2f_cls::getInstance()->registerExtension('formhelper', 'FormHelper', '0.1', 'Andrew Male & Chris Butcher', 'http://n2fyformhelper.codeplex.com/');

	/**
	 * The main FormHelper class.
	 *
	 */
	class formhelper {
		/**
		 * The processed model (properties only).
		 *
		 * @var stdClass
		 */
		public $model;
		private $name;
		private $_model;
		private $types = array();
		private $errors = array();
		private $modelMembers = array();


		/**
		 * Initializes a new formhelper object.
		 *
		 * @param string $name	String value for associating form with tags.
		 * @param mixed $model	String or object value of model for association.
		 */
		public function __construct($name, $model) {
			$this->name = $name;
			$this->model = new stdClass();
			n2f_template::setGlobalBlock('fh:form', array($this, 'processFormTag'));

			if (is_string($model)) {
				$this->_model = new $model;
			} else if (is_object($model)) {
				$this->_model = $model;
			}

			$this->modelMembers = get_object_vars($this->_model);

			if (count($this->modelMembers) > 0) {
				foreach (array_keys($this->modelMembers) as $key) {
					$this->model->{$key} = $this->_model->{$key};
				}
			}

			$declaredClasses = get_declared_classes();

			foreach (array_values($declaredClasses) as $class) {
				if (is_subclass_of($class, 'formhelper_fieldbase')) {
					$tmp = new $class();
					$tag = $tmp->getTagName();
					$this->types[$tag] = $tmp;
				}
			}
		}

		/**
		 * Method to process the begin and end form tags in templates.  Internal, do not use.
		 *
		 * @return string	String value of replaced form tag.
		 */
		public function processFormTag() {
			$args = $this->getTplArgs(func_get_args());
			$attribs = n2f_template::getBlockAttributes('fh:form', $args['current']);

			if (isset($attribs['name']) && $attribs['name'] == $this->name) {
				$ret = "<form name=\"{$this->name}\"";
				$ret .= " action=\"" . ((isset($attribs['action'])) ? $attribs['action'] : $_SERVER['PHP_SELF']) . "\"";
				$ret .= " method=\"" . ((isset($attribs['method'])) ? $attribs['method'] : 'POST') . "\"";

				if (isset($attribs['enctype'])) {
					$ret .= " enctype=\"{$attribs['enctype']}\"";
				}

				if (isset($attribs['accept'])) {
					$ret .= " accept=\"{$attribs['accept']}\"";
				}

				if (isset($attribs['onsubmit'])) {
					$ret .= " onsubmit=\"{$attribs['onsubmit']}\"";
				}

				if (isset($attribs['onreset'])) {
					$ret .= " onreset=\"{$attribs['onreset']}\"";
				}

				if (isset($attribs['onclick'])) {
					$ret .= " onclick=\"{$attribs['onclick']}\"";
				}

				if (isset($attribs['ondblclick'])) {
					$ret .= " ondblclick=\"{$attribs['ondblclick']}\"";
				}

				if (isset($attribs['onmousedown'])) {
					$ret .= " onmousedown=\"{$attribs['onmousedown']}\"";
				}

				if (isset($attribs['onmouseup'])) {
					$ret .= " onmouseup=\"{$attribs['onmouseup']}\"";
				}

				if (isset($attribs['onmouseover'])) {
					$ret .= " onmouseover=\"{$attribs['onmouseover']}\"";
				}

				if (isset($attribs['onmousemove'])) {
					$ret .= " onmousemove=\"{$attribs['onmousemove']}\"";
				}

				if (isset($attribs['onmouseout'])) {
					$ret .= " onmouseout=\"{$attribs['onmouseout']}\"";
				}

				if (isset($attribs['onkeypress'])) {
					$ret .= " onkeypress=\"{$attribs['onkeypress']}\"";
				}

				if (isset($attribs['onkeydown'])) {
					$ret .= " onkeydown=\"{$attribs['onkeydown']}\"";
				}

				if (isset($attribs['onkeyup'])) {
					$ret .= " onkeyup=\"{$attribs['onkeyup']}\"";
				}

				if (isset($attribs['accept-charset'])) {
					$ret .= " accept-charset=\"{$attribs['accept-charset']}\"";
				}

				if (isset($attribs['class'])) {
					$ret .= " class=\"{$attribs['class']}\"";
				}

				if (isset($attribs['style'])) {
					$ret .= " style=\"{$attribs['style']}\"";
				}

				if (isset($attribs['id'])) {
					$ret .= " id=\"{$attribs['id']}\"";
				}

				if (isset($attribs['lang'])) {
					$ret .= " lang=\"{$attribs['lang']}\"";
				}

				if (isset($attribs['title'])) {
					$ret .= " title=\"{$attribs['title']}\"";
				}

				if (isset($attribs['target'])) {
					$ret .= " target=\"{$attribs['target']}\"";
				}

				$ret .= '><input type="hidden" name="' . $this->name . '_posted" value="true" />';

				$attribString = '';

				foreach ($attribs as $key => $val) {
					$attribString .= " {$key}=\"{$val}\"";
				}

				$formOpen = substr($args['current'], 0, strlen("<fh:form{$attribString}>"));
				$args['current'] = str_replace(array($formOpen, '</fh:form>'), array($ret, '</form>'), $args['current']);

				if (count($this->types) > 0) {
					foreach ($this->types as $tag => $hlpr) {
						$tagName = "fh:{$tag}";
						$returned = n2f_template::getInnerTag($tagName, $args['current']);

						while (count($returned) == 2) {
							if (isset($returned['attributes']['for'])) {
								$model_key = str_replace('[]', '', $returned['attributes']['for']);

								if (isset($this->_model->{$model_key})) {
									$returned['attributes']['model_value'] = $this->_model->{$model_key};
								}

								$rendered = $hlpr->fetch($returned['attributes']['for'], $returned['attributes']);

								$args['current'] = str_replace($returned['matched'], $rendered, $args['current']);
								$returned = n2f_template::getInnerTag($tagName, $args['current']);
							}
						}
					}
				}
			}

			return($args['current']);
		}

		/**
		 * Method to process field tags within a form in templates.  Internal, do not use.
		 *
		 * @return string	String value of replaced form field.
		 */
		public function processFieldTag() {
			$args = $this->getTplArgs(func_get_args());

			if (count($this->types) > 0) {
				foreach ($this->types as $tag => $fld) {
					if (stristr($args['original'], "fh:{$tag}") !== false) {
						$attribs = n2f_template::getTagAttributes("fh:{$tag}", $args['original']);

						if (!isset($attribs['for'])) {
							break;
						}

						$model_key = str_replace('[]', '', $attribs['for']);

						if (isset($this->_model->{$model_key})) {
							$attribs['model_value'] = $this->_model->{$model_key};
						}

						return($fld->fetch($attribs['for'], $attribs));
					}
				}
			}

			return($args['current']);
		}

		/**
		 * Method to clear values in both the internal and public models.
		 *
		 */
		public function clear() {
			if (count($this->modelMembers) > 0) {
				foreach (array_keys($this->modelMembers) as $key) {
					$this->_model->{$key} = null;
					$this->model->{$key} = null;
				}
			}

			return;
		}

		/**
		 * Method to determine if the form has been posted.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isPosted() {
			if (!isset($_REQUEST[$this->name . '_posted']) || $_REQUEST[$this->name . '_posted'] != 'true') {
				return(false);
			}

			return(true);
		}

		/**
		 * Method to determine if the form was valid when posted.
		 *
		 * @return boolean	Boolean TRUE or FALSE.
		 */
		public function isValid() {
			if (count($this->modelMembers) > 0) {
				foreach (array_keys($this->modelMembers) as $key) {
					if (isset($_REQUEST[$key])) {
						$this->_model->{$key} = $_REQUEST[$key];
					} else if (isset($_FILES[$key])) {
						$this->_model->{$key} = $_FILES[$key];
					} else {
						$this->_model->{$key} = null;
					}
				}
			}

			$modelMethods = get_class_methods($this->_model);
			$valid = true;

			if (count($modelMethods) > 0) {
				foreach (array_values($modelMethods) as $method) {
					if (strtolower(substr($method, 0, 9)) === 'validate_') {
						$result = @call_user_func(array($this->_model, $method));

						if ($result instanceof n2f_return && !IsSuccess($result)) {
							if ($result->hasMsgs()) {
								foreach (array_values($result->msgs) as $msg) {
									$this->errors[] = $msg;
								}
							}

							$valid = false;
						} else if ($result === false) {
							$valid = false;
						}
					}
				}
			}

			if (!$valid) {
				return(false);
			}

			if (count($this->modelMembers) > 0) {
				foreach (array_keys($this->modelMembers) as $key) {
					$this->model->{$key} = (is_callable(array($this->_model, "sanitize_{$key}"))) ? call_user_func(array($this->_model, "sanitize_{$key}")) : $this->_model->{$key};
				}
			}

			return(true);
		}

		/**
		 * Method to retrieve the list of error messages from form processing.  (Only appear if a validation routine returned a n2f_return object with errors)
		 *
		 * @return array	Array of error messages.
		 */
		public function getErrors() {
			return($this->errors);
		}

		private function getTplArgs(array $argList) {
			return(array(
				'tpl'		=> $argList[0],
				'current'		=> $argList[1],
				'original'	=> $argList[2]
			));
		}
	}

	/**
	 * Base class for formhelper fields.
	 *
	 */
	abstract class formhelper_fieldbase {
		/**
		 * Protected member which holds the tag name to match in templates.
		 *
		 * @var string
		 */
		protected $tagName;


		/**
		 * Public method to retrieve the rendered
		 *
		 * @param string $keyName	String value to use as the name of the field when rendered.
		 * @param array $attributes	Array of attributes supplied by field tag.
		 * @return string			String value representing fully-rendered field.
		 */
		abstract public function fetch($keyName, array $attributes);


		/**
		 * Public method to retrieve the tag name to match in templates (fh:<tagName>).
		 *
		 * @return string
		 */
		public function getTagName() { return($this->tagName); }
	}

	/**
	 * Function to parse and prepare a string for INPUT element attributes.
	 *
	 * @param array $attribs	Array of attributes to parse and process.
	 * @return string		Prepared string of attributes for HTML.
	 */
	function getInputAttributes(array $attribs) {
		if (count($attribs) < 1) {
			return('');
		}

		$ret = '';

		foreach ($attribs as $key => $val) {
			switch ($key) {
				case 'checked':
					$ret .= " checked";
					break;
				case 'disabled':
					$ret .= " disabled";
					break;
				case 'readonly':
					$ret .= " readonly";
					break;
				case 'size':
					$ret .= " size=\"{$val}\"";
					break;
				case 'maxlength':
					$ret .= " maxlength=\"{$val}\"";
					break;
				case 'src':
					$ret .= " src=\"{$val}\"";
					break;
				case 'alt':
					$ret .= " alt=\"{$val}\"";
					break;
				case 'usemap':
					$ret .= " usemap=\"{$val}\"";
					break;
				case 'ismap':
					$ret .= " ismap=\"{$val}\"";
					break;
				case 'tabindex':
					$ret .= " tabindex=\"{$val}\"";
					break;
				case 'accesskey':
					$ret .= " accesskey=\"{$val}\"";
					break;
				case 'onfocus':
					$ret .= " onfocus=\"{$val}\"";
					break;
				case 'onblur':
					$ret .= " onblur=\"{$val}\"";
					break;
				case 'onselect':
					$ret .= " onselect=\"{$val}\"";
					break;
				case 'onchange':
					$ret .= " onchange=\"{$val}\"";
					break;
				case 'onclick':
					$ret .= " onclick=\"{$val}\"";
					break;
				case 'ondblclick':
					$ret .= " ondblclick=\"{$val}\"";
					break;
				case 'onmousedown':
					$ret .= " onmousedown=\"{$val}\"";
					break;
				case 'onmouseup':
					$ret .= " onmouseup=\"{$val}\"";
					break;
				case 'onmouseover':
					$ret .= " onmouseover=\"{$val}\"";
					break;
				case 'onmousemove':
					$ret .= " onmousemove=\"{$val}\"";
					break;
				case 'onmouseout':
					$ret .= " onmouseout=\"{$val}\"";
					break;
				case 'onkeypress':
					$ret .= " onkeypress=\"{$val}\"";
					break;
				case 'onkeydown':
					$ret .= " onkeydown=\"{$val}\"";
					break;
				case 'onkeyup':
					$ret .= " onkeyup=\"{$val}\"";
					break;
				case 'accept':
					$ret .= " accept=\"{$val}\"";
					break;
				case 'class':
					$ret .= " class=\"{$val}\"";
					break;
				case 'style':
					$ret .= " style=\"{$val}\"";
					break;
				case 'id':
					$ret .= " id=\"{$val}\"";
					break;
				case 'title':
					$ret .= " title=\"{$val}\"";
					break;
				case 'lang':
					$ret .= " lang=\"{$val}\"";
					break;
				case 'alt':
					$ret .= " alt=\"{$val}\"";
					break;
				case 'align':
					$ret .= " align=\"{$val}\"";
					break;
			}
		}

		return($ret);
	}

?>