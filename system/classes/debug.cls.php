<?php

	/***********************************************\
	 * N2F Yverdon v0                              *
	 * Copyright (c) 2009 Zibings Incorporated     *
	 *                                             *
	 * You should have received a copy of the      *
	 * Microsoft Reciprocal License along with     *
	 * this program.  If not, see:                 *
	 * <http://opensource.org/licenses/ms-rl.html> *
	\***********************************************/

	/*
	 * $Id: debug.cls.php 120 2010-11-06 03:45:28Z amale $
	 */

	/**
	 * Debug utility class for N2 Framework Yverdon.
	 *
	 */
	class n2f_debug extends n2f_events {
		/**
		 * Internal n2f_cfg_dbg configuration object.
		 *
		 * @var n2f_cfg_dbg
		 */
		protected $config;
		/**
		 * Internal list of errors produced by system.
		 *
		 * @var array
		 */
		protected $errors;
		/**
		 * Internal list of warnings produced by system.
		 *
		 * @var array
		 */
		protected $warnings;
		/**
		 * Internal list of notices produced by system.
		 *
		 * @var array
		 */
		protected $notices;

		/**
		 * Initializes a new n2f_debug object.
		 *
		 * @param mixed $cfg	Optional configuration values in either array or n2f_cfg_dbg format.
		 * @return n2f_debug	The new n2f_debug object.
		 */
		public function __construct($cfg = null) {
			parent::__construct();

			if ($cfg == null) {
				$cfg = new n2f_cfg_dbg();
				$cfg->dump_debug = false;
				$cfg->level = N2F_DEBUG_OFF;
			} else {
				if (is_array($cfg)) {
					$this->config = new n2f_cfg_dbg($cfg);
				} else if ($cfg instanceof n2f_cfg_dbg) {
					$this->config = $cfg;
				}
			}

			$this->errors = array();
			$this->warnings = array();
			$this->notices = array();

			$this->addEvent(N2F_EVT_ERROR_THROWN);
			$this->addEvent(N2F_EVT_WARNING_THROWN);
			$this->addEvent(N2F_EVT_NOTICE_THROWN);

			return($this);
		}

		/**
		 * Adds an error to the internal list.
		 *
		 * @param integer $errno		Error number being reported
		 * @param string $errstr		Error string being reported
		 * @param string $file		File where error was recorded from
		 * @return n2f_debug		The current n2f_debug object.
		 */
		public function throwError($errno, $errstr, $file) {
			$this->errors[] = array(
				'num'	=> $errno,
				'str'	=> $errstr,
				'file'	=> $file,
				'time'	=> time(),
			);

			$this->hitEvent(N2F_EVT_ERROR_THROWN, array($errno, $errstr, $file));

			return($this);
		}

		/**
		 * Adds a warning to the internal list.
		 *
		 * @param integer $warno		Warning number being reported
		 * @param string $warstr		Warning string being reported
		 * @param string $file		File where warning was recorded from
		 * @return n2f_debug		The current n2f_debug object.
		 */
		public function throwWarning($warno, $warstr, $file) {
			$this->warnings[] = array(
				'num'	=> $warno,
				'str'	=> $warstr,
				'file'	=> $file,
				'time'	=> time()
			);

			$this->hitEvent(N2F_EVT_WARNING_THROWN, array($warno, $warstr, $file));

			return($this);
		}

		/**
		 * Adds a notice to the internal list.
		 *
		 * @param integer $notno		Notice number being reported
		 * @param string $notstr		Notice string being reported
		 * @param string $file		File where notice was recorded from
		 * @return n2f_debug		The current n2f_debug object.
		 */
		public function throwNotice($notno, $notstr, $file) {
			$this->notices[] = array(
				'num'	=> $notno,
				'str'	=> $notstr,
				'file'	=> $file,
				'time'	=> time()
			);

			$this->hitEvent(N2F_EVT_NOTICE_THROWN, array($notno, $notstr, $file));

			return($this);
		}

		/**
		 * Returns the internal list of errors.
		 *
		 * @return array	Array of errors.
		 */
		public function getErrors() {
			return($this->errors);
		}

		/**
		 * Returns the internal list of warnings.
		 *
		 * @return array	Array of warnings.
		 */
		public function getWarnings() {
			return($this->warnings);
		}

		/**
		 * Returns the internal list of notices.
		 *
		 * @return array	Array of notices.
		 */
		public function getNotices() {
			return($this->notices);
		}

		/**
		 * Returns true or false depending on whether or not the provided debug level is currently toggled.
		 *
		 * @param integer $level		Level to compare against for toggle
		 * @return boolean			Boolean value based on curent debug level.
		 */
		public function showLevel($level) {
			if ($this->config->level >= $level) {
				return(true);
			}

			return(false);
		}
	}

?>