<?php

	/**
	 * Icons used from 'Knob Buttons Toolbar Icons' @ http://itweek.deviantart.com/art/Knob-Buttons-Toolbar-icons-73463960
	 * All code licensed under N2F.
	 *
	 */

	// Our new event
	define('N2F_EVT_SUCCESS_THROWN',	'N2F_EVT_SUCCESS_THROWN');

	/**
	 * Class for managing information boxes on a page.
	 *
	 */
	class info_boxes extends n2f_debug {
		/**
		 * Private collection of success messages.
		 *
		 * @var array
		 */
		private $successes;


		/**
		 * Initializes a new info_boxes object.
		 *
		 * @param integer $baseLevel	Base level of debug messages to 'throw'.
		 * @return info_boxes
		 */
		public function __construct($baseLevel = N2F_DEBUG_ERROR, $registerTag = false) {
			parent::__construct(new n2f_cfg_dbg(array('level' => $baseLevel, 'dump_debug' => false)));

			$this->successes = array();

			$this->addEvent(N2F_EVT_SUCCESS_THROWN);

			if ($registerTag) {
				n2f_template::setGlobalTag('cs:infoboxes', array($this, 'processTag'));
			}

			return($this);
		}

		/**
		 * Adds a new success message onto the stack.
		 *
		 * @param integer $sucno	Number of success message being added.
		 * @param string $sucstr	Actual success message being added.
		 * @param string $file	Origin filename of success message being added.
		 * @return info_boxes	info_boxes object for chaining.
		 */
		public function throwSuccess($sucno, $sucstr, $file) {
			$this->successes[] = array(
				'num'	=> $sucno,
				'str'	=> $sucstr,
				'file'	=> $file,
				'time'	=> time()
			);

			$this->hitEvent(N2F_EVT_SUCCESS_THROWN, array($sucno, $sucstr, $file));

			return($this);
		}

		/**
		 * Returns the stack of success messages.
		 *
		 * @return array	Array of success messages, empty if no success messages available.
		 */
		public function getSuccesses() {
			return($this->successes);
		}

		/**
		 * Checks if there are any errors in the stack.
		 *
		 * @return boolean	Boolean TRUE or FALSE depending on existance of error messages.
		 */
		public function hasErrors() {
			return((count($this->errors) > 0) ? true : false);
		}

		/**
		 * Checks if there are any warnings in the stack.
		 *
		 * @return boolean	Boolean TRUE or FALSE depending on existance of warning messages.
		 */
		public function hasWarnings() {
			return((count($this->warnings) > 0) ? true : false);
		}

		/**
		 * Checks if there are any notices in the stack.
		 *
		 * @return boolean	Boolean TRUE or FALSE depending on existance of notice messages.
		 */
		public function hasNotices() {
			return((count($this->notices) > 0) ? true : false);
		}

		/**
		 * Checks if there are any successes in the stack.
		 *
		 * @return boolean	Boolean TRUE or FALSE depending on existance of fail.
		 */
		public function hasSuccesses() {
			return((count($this->successes) > 0) ? true : false);
		}

		/**
		 * Displays the info boxes.
		 *
		 */
		public function display($return = false) {
			$tplFields = array();

			if ($this->hasErrors()) {
				if (count($this->errors) > 1) {
					$tplFields['errors'] = '<ul>';

					foreach (array_values($this->errors) as $error) {
						$tplFields['errors'] .= "<li>{$error['str']}</li>";
					}

					$tplFields['errors'] .= '</ul>';
				} else {
					$tplFields['errors'] = $this->errors[0]['str'];
				}
			}

			if ($this->hasWarnings()) {
				if (count($this->warnings) > 1) {
					$tplFields['warnings'] = '<ul>';

					foreach (array_values($this->warnings) as $warning) {
						$tplFields['warnings'] .= "<li>{$warning['str']}</li>";
					}

					$tplFields['warnings'] .= '</ul>';
				} else {
					$tplFields['warnings'] = $this->warnings[0]['str'];
				}
			}

			if ($this->hasNotices()) {
				if (count($this->notices) > 1) {
					$tplFields['notices'] = '<ul>';

					foreach (array_values($this->notices) as $notice) {
						$tplFields['notices'] .= "<li>{$notice['str']}</li>";
					}

					$tplFields['notices'] .= '</ul>';
				} else {
					$tplFields['notices'] = $this->notices[0]['str'];
				}
			}

			if ($this->hasSuccesses()) {
				if (count($this->successes) > 1) {
					$tplFields['successes'] = '<ul>';

					foreach (array_values($this->successes) as $success) {
						$tplFields['successes'] .= "<li>{$success['str']}</li>";
					}

					$tplFields['successes'] .= '</ul>';
				} else {
					$tplFields['successes'] = $this->successes[0]['str'];
				}
			}

			$tpl = new n2f_template('dynamic');
			$tpl->setModule('main')->setFile('infoboxes');

			if (count($tplFields) > 0) {
				$tpl->setFields($tplFields);
			}

			if (!$return) {
				$tpl->render()->display();

				return(null);
			}

			return($tpl->render()->fetch());
		}

		/**
		 * Method to aid with displaying via tags (made for Chameleon).  - Andy
		 *
		 */
		public function processTag(n2f_template $tpl, $currentContent, $originalContent) {
			return($this->display(true));
		}
	}

?>