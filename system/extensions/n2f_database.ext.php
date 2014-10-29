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
	 * $Id: database.cls.php 92 2010-03-13 13:59:10Z amale $
	 */

	global $_n2f_db_extensions, $cfg;

	// Database configuration
	$cfg['db']['type']			= '';					# Database extension type to use (ex: mysqli, pgsql, etc)
	$cfg['db']['host']			= '';					# Hostname of database server
	$cfg['db']['name']			= '';					# Name of database to select
	$cfg['db']['user']			= '';					# Username to use when authenticating
	$cfg['db']['pass']			= '';					# Password to use when authenticating
	$cfg['db']['exts']			= array('mysqli');			# Available database extensions

	define('N2F_DATABASE_DIR', N2F_REL_PATH . 'system/extensions/n2f_database/');

	require(N2F_DATABASE_DIR . 'config.inc.php');

	// Check if we need to include our template object
	if (is_array($cfg['db']['exts']) && count($cfg['db']['exts']) > 0) {
		foreach (array_values($cfg['db']['exts']) as $ext) {
			n2f_cls::getInstance()->loadExtension("n2f_database/{$ext}");
		}
	}

	n2f_cls::getInstance()->hookEvent(N2F_EVT_MODULES_LOADED, '_initDatabase');
	n2f_cls::getInstance()->hookEvent(N2F_EVT_DESTRUCT, '_closeDatabase');

	/**
	 * System method for initializing the database system.
	 *
	 * @return null
	 */
	function _initDatabase() {
		global $cfg;

		if (is_array($cfg['db']['exts']) && count($cfg['db']['exts']) > 0 && $cfg['db']['type'] != '') {
			global $db;

			$db = n2f_database::setInstance(n2f_cls::getInstance(), $cfg['db']['type']);
			$db->open();
		}

		return(null);
	}

	function _closeDatabase() {
		$db = n2f_database::getInstance();

		if ($db instanceof n2f_database && $db->isOpen()) {
			$db->close();
		}
	}

	/**
	 * Core database class for N2 Framework Yverdon.
	 *
	 */
	class n2f_database extends n2f_events {
		/**
		 * Connection resource for this n2f_database object.
		 *
		 * @var resource
		 */
		public $conn;
		/**
		 * Number of queries performed by this n2f_database object.
		 *
		 * @var integer
		 */
		public $queries;
		/**
		 * The extension currently in use by this n2f_database object.
		 *
		 * @var string
		 */
		public $extension;
		/**
		 * The current configuration to use when opening the connection.
		 *
		 * @var n2f_cfg_db
		 */
		protected $_config;
		/**
		 * Internal container for object data.
		 *
		 * @var array
		 */
		protected $_internalData;
		/**
		 * Protected static property to hold the stack of stored queries.
		 *
		 * @var array
		 */
		protected static $_storedQueries = array();
		/**
		 * Protected static property to hold current singleton global.
		 *
		 * @var array
		 */
		protected static $_instance = array();
		/**
		 * List of callbacks used by the core system for engine calls.
		 *
		 * @var array
		 */
		private static $callbacks = array(
				N2F_DBEVT_OPEN_CONNECTION,
				N2F_DBEVT_CLOSE_CONNECTION,
				N2F_DBEVT_CHECK_CONNECTION,
				N2F_DBEVT_ADD_PARAMETER,
				N2F_DBEVT_EXECUTE_QUERY,
				N2F_DBEVT_GET_ROW,
				N2F_DBEVT_GET_ROWS,
				N2F_DBEVT_GET_LAST_INC,
				N2F_DBEVT_GET_NUMROWS,
				N2F_DBEVT_GET_RESULT
		);


		/**
		 * Method to get a current n2f_database instance.
		 *
		 * @param string $key	String value of key name for n2f_database global.
		 * @return n2f_database	The requested n2f_database global, 'null' if non-existant.
		 */
		public static function &getInstance($key = null) {
			if ($key !== null && !isset(n2f_database::$_instance[$key])) {
				n2f_database::$_instance[$key] = new n2f_database(n2f_cls::getInstance(), '');
			} else if ($key === null || empty($key)) {
				$key = 'default';

				if (!isset(n2f_database::$_instance['default'])) {
					n2f_database::$_instance['default'] = new n2f_database(n2f_cls::getInstance(), '');
				}
			}

			$instance = n2f_database::$_instance[$key];

			return($instance);
		}

		/**
		 * Method to set a current n2f_database instance.
		 *
		 * @param n2f_cls $n2f	An n2f_cls object with configuration values.
		 * @param string $ext	String value for the extension type.
		 * @param boolean $new	Boolean value determining if this is a new global.
		 * @param string $key	String value for a new global.
		 * @return n2f_database	The new n2f_database global.
		 */
		public static function &setInstance(n2f_cls &$n2f, $ext, $new = false, $key = null, n2f_cfg_db $cfg = null) {
			if ($new === true && $key !== null && !empty($key)) {
				n2f_database::$_instance[$key] = new n2f_database($n2f, $ext, $cfg);
				$instance = n2f_database::$_instance[$key];
			} else {
				n2f_database::$_instance['default'] = new n2f_database($n2f, $ext, $cfg);
				$instance = n2f_database::$_instance['default'];
			}

			return($instance);
		}

		/**
		 * Static function for adding database extensions to the available list.
		 *
		 * @param string $name		Name of extension to add
		 * @param array $callbacks	Array of callbacks for extension
		 * @return null
		 */
		public static function addExtension($name, array $callbacks) {
			if (empty($name)) {
				return(null);
			}

			if (count($callbacks) != count(self::$callbacks)) {
				return(null);
			}

			foreach (array_values(self::$callbacks) as $callbk) {
				if (!isset($callbacks[$callbk])) {
					return(null);
				}
			}

			global $_n2f_db_extensions;

			$_n2f_db_extensions[$name] = array(
				N2F_DBEVT_OPEN_CONNECTION		=> $callbacks[N2F_DBEVT_OPEN_CONNECTION],
				N2F_DBEVT_CLOSE_CONNECTION		=> $callbacks[N2F_DBEVT_CLOSE_CONNECTION],
				N2F_DBEVT_CHECK_CONNECTION		=> $callbacks[N2F_DBEVT_CHECK_CONNECTION],
				N2F_DBEVT_ADD_PARAMETER			=> $callbacks[N2F_DBEVT_ADD_PARAMETER],
				N2F_DBEVT_EXECUTE_QUERY			=> $callbacks[N2F_DBEVT_EXECUTE_QUERY],
				N2F_DBEVT_GET_ROW				=> $callbacks[N2F_DBEVT_GET_ROW],
				N2F_DBEVT_GET_ROWS				=> $callbacks[N2F_DBEVT_GET_ROWS],
				N2F_DBEVT_GET_LAST_INC			=> $callbacks[N2F_DBEVT_GET_LAST_INC],
				N2F_DBEVT_GET_NUMROWS			=> $callbacks[N2F_DBEVT_GET_NUMROWS],
				N2F_DBEVT_GET_RESULT			=> $callbacks[N2F_DBEVT_GET_RESULT]
			);

			return(null);
		}

		/**
		 * Static function for storing a query in the stack.
		 *
		 * @param string $key		String value of key for recalling the query.
		 * @param string $engine		String value of the database engine this query targets.
		 * @param string $sql		String value of the query.
		 * @param array $paramTypes	Optional array of parameter values to parameterize the query.
		 * @param mixed $options		Optional mixed value of query options.
		 * @return boolean			Boolean TRUE or FALSE based on storage success.
		 */
		public static function storeQuery($key, $engine, $sql, array $paramTypes = null, $options = null) {
			$ret = true;

			if (!isset(n2f_database::$_storedQueries[$engine])) {
				n2f_database::$_storedQueries[$engine] = array($key => array($sql, $paramTypes, $options));
			} else if (isset(n2f_database::$_storedQueries[$engine][$key])) {
				$ret = false;
			} else {
				n2f_database::$_storedQueries[$engine][$key] = array($sql, $paramTypes, $options);
			}

			return($ret);
		}


		/**
		 * Initializes a new n2f_database object.
		 *
		 * @param n2f_cls $n2f
		 * @param string $ext
		 * @return n2f_database
		 */
		public function __construct(n2f_cls &$n2f, $ext, n2f_cfg_db $cfg = null) {
			global $_n2f_db_extensions;
			parent::__construct();

			$this->conn = null;
			$this->queries = 0;
			$this->_config = $cfg;
			$this->_internalData = array();

			$this->addEvent(N2F_DBEVT_CONNECTION_OPENED);
			$this->addEvent(N2F_DBEVT_CONNECTION_CLOSED);
			$this->addEvent(N2F_DBEVT_QUERY_CREATED);
			$this->addEvent(N2F_DBEVT_OPEN_CONNECTION, true);
			$this->addEvent(N2F_DBEVT_CLOSE_CONNECTION, true);
			$this->addEvent(N2F_DBEVT_CHECK_CONNECTION, true);

			if (!isset($_n2f_db_extensions[$ext])) {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(N2F_ERROR_DB_EXTENSION_NOT_LOADED, S('N2F_ERROR_DB_EXTENSION_NOT_LOADED', array($ext)), 'n2f_database.ext.php');
				}

				$this->extension = null;
			} else {
				$this->hookEvent(N2F_DBEVT_OPEN_CONNECTION, $_n2f_db_extensions[$ext][N2F_DBEVT_OPEN_CONNECTION]);
				$this->hookEvent(N2F_DBEVT_CLOSE_CONNECTION, $_n2f_db_extensions[$ext][N2F_DBEVT_CLOSE_CONNECTION]);
				$this->hookEvent(N2F_DBEVT_CHECK_CONNECTION, $_n2f_db_extensions[$ext][N2F_DBEVT_CHECK_CONNECTION]);
				$this->extension = $ext;
			}

			return($this);
		}

		/**
		 * Mechanism for storing data in the n2f_database object's internal data container.
		 *
		 * @param mixed $key
		 * @param mixed $data
		 * @return n2f_database
		 */
		public function addData($key, $data) {
			$this->_internalData[$key] = $data;

			return($this);
		}

		/**
		 * Retrieves data from the n2f_database object's internal data container.
		 *
		 * @param mixed $key
		 * @return mixed
		 */
		public function getData($key) {
			if (isset($this->_internalData[$key])) {
				return($this->_internalData[$key]);
			}

			return(null);
		}

		/**
		 * Opens the connection for this n2f_database object.
		 *
		 * @param array $args
		 * @return n2f_database
		 */
		public function open() {
			$n2f = n2f_cls::getInstance();

			if ($this->_config === null) {
				$this->_config = new n2f_cfg_db($GLOBALS['cfg']['db']);
			}

			$this->hitEvent(N2F_DBEVT_OPEN_CONNECTION, array(&$this, $this->_config));
			$this->hitEvent(N2F_DBEVT_CONNECTION_OPENED, array(&$this));

			if ($n2f->debug->showLevel(N2F_DEBUG_NOTICE)) {
				$n2f->debug->throwNotice(N2F_NOTICE_DB_CONNECTION_OPENED, S('N2F_NOTICE_DB_CONNECTION_OPENED', array($this->extension)), 'n2f_database.ext.php');
			}

			return($this);
		}

		/**
		 * Closes the connection for this n2f_database object.
		 *
		 * @return n2f_database
		 */
		public function close() {
			$n2f = n2f_cls::getInstance();

			$this->hitEvent(N2F_DBEVT_CLOSE_CONNECTION, array(&$this));
			$this->hitEvent(N2F_DBEVT_CONNECTION_CLOSED, array(&$this));
			$this->conn = null;

			if ($n2f->debug->showLevel(N2F_DEBUG_NOTICE)) {
				$n2f->debug->throwNotice(N2F_NOTICE_DB_CONNECTION_CLOSED, S('N2F_NOTICE_DB_CONNECTION_CLOSED', array($this->extension)), 'n2f_database.ext.php');
			}

			return($this);
		}

		/**
		 * Returns true or false based on whether or not the object's connection is active.
		 *
		 * @return boolean
		 */
		public function isOpen() {
			return((bool)$this->hitEvent(N2F_DBEVT_CHECK_CONNECTION, array(&$this)));
		}

		/**
		 * Produces a new n2f_database_query object from the given query.
		 *
		 * @param string $sql
		 * @return n2f_database_query
		 */
		public function query($sql, $options = null) {
			$result = new n2f_database_query($sql, $this, $options);
			$this->hitEvent(N2F_DBEVT_QUERY_CREATED, array(&$result, &$this));

			return($result);
		}

		/**
		 * Produces a new n2f_database_query object (or null on failure) using the requested stored query.
		 *
		 * @param string $key		String value of the key of the stored query to call.
		 * @param array $params		Optional array of parameter keys and values (format: 'key' => $val)
		 * @param array $replacements	Optional array of replacement values for use in structuring the query.
		 * @return n2f_database_query	n2f_database_query object returned by call to n2f_database::query(), null if stored query couldn't be found.
		 */
		public function storedQuery($key, array $params = null, array $replacements = null) {
			$n2f = n2f_cls::getInstance();

			if (!isset(n2f_database::$_storedQueries[$this->extension]) || !isset(n2f_database::$_storedQueries[$this->extension][$key])) {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(N2F_ERROR_DB_INVALID_STORED_QUERY, S('N2F_ERROR_DB_INVALID_STORED_QUERY', array($key)), 'n2f_database.ext.php');
				}

				return(null);
			}

			$stored = n2f_database::$_storedQueries[$this->extension][$key];

			if ($replacements !== null && count($replacements) > 0) {
				foreach ($replacements as $key => $val) {
					$stored[0] = str_replace("_%{$key}%_", $val, $stored[0]);
				}
			}

			$query = $this->query($stored[0], $stored[2]);

			if (count($stored[1]) > 0 && count($stored[1]) == count($params)) {
				$i = 0;

				foreach ($params as $key => $val) {
					$query->addParam($key, $val, $stored[1][$i]);
					$i++;
				}
			} else if (count($stored[1]) > 0 || count($params) > 0) {
				if ($n2f->debug->showLevel(N2F_DEBUG_WARN)) {
					$n2f->debug->throwWarning(N2F_WARN_DB_INCORRECT_STORED_PARAMETER_COUNT, S('N2F_WARN_DB_INCORRECT_STORED_PARAMETER_COUNT'), 'n2f_database.ext.php');
				}
			}

			return($query);
		}
	}

	/**
	 * Core database query class for N2 Framework Yverdon.
	 *
	 */
	class n2f_database_query extends n2f_events {
		/**
		 * Internal reference to the global database handler.
		 *
		 * @var n2f_database
		 */
		public $db;
		/**
		 * SQL string used for this query.
		 *
		 * @var string
		 */
		public $query;
		/**
		 * Collection of parameters for the current query.
		 *
		 * @var array
		 */
		public $params;
		/**
		 * Holds the current result set for the query if applicable.
		 *
		 * @var result
		 */
		public $result;
		/**
		 * Latest error returned by the query.
		 *
		 * @var array
		 */
		private $_errors;
		/**
		 * Internal container for object data.
		 *
		 * @var array
		 */
		protected $_internalData;

		/**
		 * Initializes a new n2f_database_query object.
		 *
		 * @param string $sql
		 * @param n2f_database $db
		 * @return n2f_database_query
		 */
		public function __construct($sql, n2f_database &$db, $options = null) {
			global $_n2f_db_extensions;
			$n2f = n2f_cls::getInstance();
			parent::__construct();
			$this->db = $db;
			$this->query = $sql;
			$this->errors = array();
			$this->params = array();
			$this->result = null;
			$this->_internalData = array();

			$this->addData('options', $options);

			$this->addEvent(N2F_DBEVT_PARAMETER_ADDED);
			$this->addEvent(N2F_DBEVT_QUERY_EXECUTED);
			$this->addEvent(N2F_DBEVT_ROW_RETRIEVED);
			$this->addEvent(N2F_DBEVT_ROWS_RETRIEVED);
			$this->addEvent(N2F_DBEVT_LAST_INC_RETRIEVED);
			$this->addEvent(N2F_DBEVT_NUMROWS_RETRIEVED);
			$this->addEvent(N2F_DBEVT_RESULT_RETRIEVED);
			$this->addEvent(N2F_DBEVT_ADD_PARAMETER, true);
			$this->addEvent(N2F_DBEVT_EXECUTE_QUERY, true);
			$this->addEvent(N2F_DBEVT_GET_ROW, true);
			$this->addEvent(N2F_DBEVT_GET_ROWS, true);
			$this->addEvent(N2F_DBEVT_GET_LAST_INC, true);
			$this->addEvent(N2F_DBEVT_GET_NUMROWS, true);
			$this->addEvent(N2F_DBEVT_GET_RESULT, true);

			if ($db->extension === null || empty($db->extension)) {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(N2F_ERROR_DB_EXTENSION_EMPTY, S('N2F_ERROR_DB_EXTENSION_EMPTY'), 'n2f_database.ext.php');
				}

				$this->addError(S('N2F_ERROR_DB_EXTENSION_EMPTY'));
			} else if ($db->isOpen() !== true) {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(N2F_ERROR_DB_NOT_LOADED, S('N2F_ERROR_DB_NOT_LOADED'), 'n2f_database.ext.php');
				}

				$this->addError(S('N2F_ERROR_DB_NOT_LOADED'));
			} else {
				$this->hookEvent(N2F_DBEVT_ADD_PARAMETER, $_n2f_db_extensions[$db->extension][N2F_DBEVT_ADD_PARAMETER]);
				$this->hookEvent(N2F_DBEVT_EXECUTE_QUERY, $_n2f_db_extensions[$db->extension][N2F_DBEVT_EXECUTE_QUERY]);
				$this->hookEvent(N2F_DBEVT_GET_ROW, $_n2f_db_extensions[$db->extension][N2F_DBEVT_GET_ROW]);
				$this->hookEvent(N2F_DBEVT_GET_ROWS, $_n2f_db_extensions[$db->extension][N2F_DBEVT_GET_ROWS]);
				$this->hookEvent(N2F_DBEVT_GET_LAST_INC, $_n2f_db_extensions[$db->extension][N2F_DBEVT_GET_LAST_INC]);
				$this->hookEvent(N2F_DBEVT_GET_NUMROWS, $_n2f_db_extensions[$db->extension][N2F_DBEVT_GET_NUMROWS]);
				$this->hookEvent(N2F_DBEVT_GET_RESULT, $_n2f_db_extensions[$db->extension][N2F_DBEVT_GET_RESULT]);
			}

			if ($n2f->debug->showLevel(N2F_DEBUG_NOTICE)) {
				$n2f->debug->throwNotice(N2F_NOTICE_DB_QUERY_CREATED, S('N2F_NOTICE_DB_QUERY_CREATED', array($db->extension, $sql)), 'n2f_database.ext.php');
			}

			return($this);
		}

		/**
		 * Mechanism for storing data in the n2f_database_query object's internal data container.
		 *
		 * @param mixed $key
		 * @param mixed $data
		 * @return n2f_database
		 */
		public function addData($key, $data) {
			$this->_internalData[$key] = $data;

			return($this);
		}

		/**
		 * Retrieves data from the n2f_database_query object's internal data container.
		 *
		 * @param mixed $key
		 * @return mixed
		 */
		public function getData($key) {
			if (isset($this->_internalData[$key])) {
				return($this->_internalData[$key]);
			}

			return(null);
		}

		/**
		 * Adds a parameter to the query stack.
		 *
		 * @param string $key
		 * @param mixed $value
		 * @param mixed $type
		 * @return n2f_database_query
		 */
		public function addParam($key, $value, $type) {
			$n2f = n2f_cls::getInstance();

			$result = $this->hitEvent(N2F_DBEVT_ADD_PARAMETER, array(&$this, $key, $value, $type));

			if ($result !== false && $n2f->cfg->dbg->level >= N2F_DEBUG_NOTICE) {
				$n2f->debug->throwNotice(N2F_NOTICE_DB_PARAMETER_ADDED, S('N2F_NOTICE_DB_PARAMETER_ADDED', array($key)), 'n2f_database.ext.php');
			}

			return($this);
		}

		/**
		 * Adds an array of parameters to the query stack.
		 *
		 * @param array $params
		 * @return n2f_database_query
		 */
		public function addParams(array $params) {
			$n2f = n2f_cls::getInstance();

			if (count($params) < 1) {
				if ($n2f->debug->showLevel(N2F_DEBUG_WARN)) {
					$n2f->debug->throwWarning(N2F_WARN_DB_PARAMETERS_NOT_SUPPLIED, S('N2F_WARN_DB_PARAMETERS_NOT_SUPPLIED', array($this->query)), 'n2f_database.ext.php');
				}

				return($this);
			}

			foreach (array_values($params) as $param) {
				if (count($param) != 3) {
					if ($n2f->debug->showLevel(N2F_DEBUG_WARN)) {
						$n2f->debug->throwWarning(N2F_WARN_DB_INVALID_PARAMETER, S('N2F_WARN_DB_INVALID_PARAMETER', array(debugEcho($param))), 'n2f_database.ext.php');
					}

					continue;
				}

				$this->addParam($param[0], $param[1], $param[2]);
			}

			return($this);
		}

		/**
		 * Executes the query.
		 *
		 * @return n2f_database_query
		 */
		public function execQuery() {
			$n2f = n2f_cls::getInstance();

			if (count($this->_errors) < 1) {
				$this->hitEvent(N2F_DBEVT_EXECUTE_QUERY, array(&$this));
				$this->db->queries += 1;

				if ($n2f->cfg->dbg->level >= N2F_DEBUG_NOTICE) {
					$n2f->debug->throwNotice(N2F_NOTICE_DB_QUERY_EXECUTED, S('N2F_NOTICE_DB_QUERY_EXECUTED'), 'n2f_database.ext.php');
				}
			}

			return($this);
		}

		/**
		 * Adds an error to the n2f_database_query object's error stack.
		 *
		 * @param string $string
		 * @return n2f_database_query
		 */
		public function addError($string) {
			if (empty($string)) {
				return($this);
			}

			$this->_errors[] = $string;

			return($this);
		}

		/**
		 * Returns true or false based on whether or not an error has occurred.
		 *
		 * @return boolean
		 */
		public function isError() {
			if (count($this->_errors) > 0) {
				return(true);
			}

			return(false);
		}

		/**
		 * Returns the last populated error string.
		 *
		 * @return string
		 */
		public function fetchError() {
			return($this->_errors[count($this->_errors) - 1]);
		}

		/**
		 * Returns the error stack.
		 *
		 * @return array
		 */
		public function fetchErrors() {
			return($this->_errors);
		}

		/**
		 * Fetches a single row from the result.
		 *
		 * @return mixed
		 */
		public function fetchRow() {
			return($this->hitEvent(N2F_DBEVT_GET_ROW, array(&$this)));
		}

		/**
		 * Fetches all rows from the result.
		 *
		 * @return mixed
		 */
		public function fetchRows() {
			return($this->hitEvent(N2F_DBEVT_GET_ROWS, array(&$this)));
		}

		/**
		 * Fetches a specific field from the result.
		 *
		 * @param integer $offset
		 * @param string $field_name
		 * @return mixed
		 */
		public function fetchResult($offset, $field_name) {
			return($this->hitEvent(N2F_DBEVT_GET_RESULT, array(&$this, $offset, $field_name)));
		}

		/**
		 * Fetches the last automatically incremented value from the query (if applicable).
		 *
		 * @return mixed
		 */
		public function fetchInc($params = null) {
			if ($params !== null) {
				return($this->hitEvent(N2F_DBEVT_GET_LAST_INC, array(&$this, $params)));
			}

			return($this->hitEvent(N2F_DBEVT_GET_LAST_INC, array(&$this)));
		}

		/**
		 * Returns the number of rows from the result.
		 *
		 * @return integer
		 */
		public function numRows() {
			return($this->hitEvent(N2F_DBEVT_GET_NUMROWS, array(&$this)));
		}

		/**
		 * Determines if a provided type is a valid generic type.
		 *
		 * @param integer $type	Integer value to test as parameter type.
		 * @return boolean		Boolean TRUE or FALSE based on the type's validity.
		 */
		public function validParamType($type) {
			if ($type < 0 || $type > 5) {
				return(false);
			}

			return(true);
		}
	}

?>