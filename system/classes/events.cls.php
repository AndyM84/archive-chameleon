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
	 * $Id: events.cls.php 161 2011-07-10 05:46:54Z amale@EPSILON $
	 */

	/**
	 * Core event class for N2 Framework Yverdon
	 *
	 */
	class n2f_events {
		/**
		 * Contains an array of events and hooked callbacks.
		 *
		 * @var array
		 */
		protected $events;

		/**
		 * Initializes the n2f_events object.
		 *
		 * @return n2f_events
		 */
		public function __construct() {
			$this->events = array();

			return($this);
		}

		/**
		 * Adds an event to the n2f_events object stack.
		 *
		 * @param string $name	Name of the event to make available
		 * @return n2f_events
		 */
		protected function addEvent($name, $sys_evt = false) {
			$this->events[$name] = array(
				'sys'	=> $sys_evt,
				'hooks'	=> array(),
				'count'	=> 0
			);

			return($this);
		}

		/**
		 * Causes an event in the n2f_events object stack to be 'hit' or 'bubbled'.
		 *
		 * @param string $name	Name of the event to hit/bubble
		 * @param array $args	Arguments to pass to event hooks
		 * @return array
		 */
		protected function hitEvent($name, array $args = null) {
			if (!isset($this->events[$name]) || $this->events[$name]['count'] < 1) {
				return(false);
			}

			if ($args === null) {
				$args = array();
			}

			$results = array();

			foreach (array_values($this->events[$name]['hooks']) as $callback) {
				if (is_callable($callback)) {
					if ($this->events[$name]['sys'] === true) {
						$results = call_user_func_array($callback, $args);
					} else {
						$results[] = array(
							'callback'	=> callback_toString($callback),
							'returned'	=> call_user_func_array($callback, $args)
						);
					}
				}
			}

			return($results);
		}

		/**
		 * Attaches a callback to an event in the n2f_events object stack.
		 *
		 * @param string $name		Name of the event to hook to
		 * @param callback $callback	Callback method/function to hook to event
		 * @return boolean
		 */
		public function hookEvent($name, $callback) {
			if (!isset($this->events[$name])) {
				return(false);
			}

			if ($this->events[$name]['sys'] === true) {
				$this->events[$name]['hooks'] = array($callback);
				$this->events[$name]['count'] = 1;
			} else {
				$this->events[$name]['hooks'][] = $callback;
				$this->events[$name]['count']++;
			}

			return(true);
		}
	}

?>