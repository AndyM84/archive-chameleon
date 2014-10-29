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
	 * $Id: db.mysql.ext.php 85 2010-02-14 22:13:49Z amale $
	 */

	// Global variable(s)
	global $strings;
	$n2f = n2f_cls::getInstance();

	// Error constants
	define('MYSQLDB_ERROR_INVALID_CONFIG',				'0001');
	define('MYSQLDB_ERROR_FAILED_DBSELECT',				'0002');
	define('MYSQLDB_ERROR_FAILED_DBCONNECT',			'0003');
	define('MYSQLDB_ERROR_FAILED_QUERY',				'0004');
	define('MYSQLDB_ERROR_NO_QUERY',					'0005');
	define('MYSQLDB_ERROR_MISSING_MYSQL',				'0006');

	// English error strings
	$strings['en']['MYSQLDB_ERROR_INVALID_CONFIG']		= 'The configuration submitted for the MySQL database extension was invalid.';
	$strings['en']['MYSQLDB_ERROR_FAILED_DBSELECT']		= "The MySQL database extension was unable to select the '_%1%_' database for the following reason(s): _%2%_";
	$strings['en']['MYSQLDB_ERROR_FAILED_DBCONNECT']		= "The MySQL database extension was unable to connect for the following reason(s): _%1%_";
	$strings['en']['MYSQLDB_ERROR_FAILED_QUERY']			= 'Failed to execute a query. (_%1%_)';
	$strings['en']['MYSQLDB_ERROR_NO_QUERY']			= 'Operation performed on a non-query.';
	$strings['en']['MYSQLDB_ERROR_MISSING_MYSQL']		= 'The PHP MySQL extension is not installed on this system, aborting MySQL extension registration.';

	// German error strings
	$strings['de']['MYSQLDB_ERROR_INVALID_CONFIG']		= 'The configuration submitted for the MySQL database extension was invalid.';
	$strings['de']['MYSQLDB_ERROR_FAILED_DBSELECT']		= "The MySQL database extension was unable to select the '_%1%_' database for the following reason(s): _%2%_";
	$strings['de']['MYSQLDB_ERROR_FAILED_DBCONNECT']		= "The MySQL database extension was unable to connect for the following reason(s): _%1%_";
	$strings['de']['MYSQLDB_ERROR_FAILED_QUERY']			= 'Failed to execute a query. (_%1%_)';
	$strings['de']['MYSQLDB_ERROR_NO_QUERY']			= 'Operation performed on a non-query.';
	$strings['de']['MYSQLDB_ERROR_MISSING_MYSQL']		= 'The PHP MySQL extension is not installed on this system, aborting MySQL extension registration.';

	// Spanish error strings
	$strings['es']['MYSQLDB_ERROR_INVALID_CONFIG']		= 'The configuration submitted for the MySQL database extension was invalid.';
	$strings['es']['MYSQLDB_ERROR_FAILED_DBSELECT']		= "The MySQL database extension was unable to select the '_%1%_' database for the following reason(s): _%2%_";
	$strings['es']['MYSQLDB_ERROR_FAILED_DBCONNECT']		= "The MySQL database extension was unable to connect for the following reason(s): _%1%_";
	$strings['es']['MYSQLDB_ERROR_FAILED_QUERY']			= 'Failed to execute a query. (_%1%_)';
	$strings['es']['MYSQLDB_ERROR_NO_QUERY']			= 'Operation performed on a non-query.';
	$strings['es']['MYSQLDB_ERROR_MISSING_MYSQL']		= 'The PHP MySQL extension is not installed on this system, aborting MySQL extension registration.';

	// Swedish error strings
	$strings['se']['MYSQLDB_ERROR_INVALID_CONFIG']		= 'The configuration submitted for the MySQL database extension was invalid.';
	$strings['se']['MYSQLDB_ERROR_FAILED_DBSELECT']		= "The MySQL database extension was unable to select the '_%1%_' database for the following reason(s): _%2%_";
	$strings['se']['MYSQLDB_ERROR_FAILED_DBCONNECT']		= "The MySQL database extension was unable to connect for the following reason(s): _%1%_";
	$strings['se']['MYSQLDB_ERROR_FAILED_QUERY']			= 'Failed to execute a query. (_%1%_)';
	$strings['se']['MYSQLDB_ERROR_NO_QUERY']			= 'Operation performed on a non-query.';
	$strings['se']['MYSQLDB_ERROR_MISSING_MYSQL']		= 'The PHP MySQL extension is not installed on this system, aborting MySQL extension registration.';

	// Create our array of handlers
	$handlers = array(
		N2F_DBEVT_OPEN_CONNECTION					=> 'mysql_openConnection',
		N2F_DBEVT_CLOSE_CONNECTION					=> 'mysql_closeConnection',
		N2F_DBEVT_CHECK_CONNECTION					=> 'mysql_checkConnection',
		N2F_DBEVT_ADD_PARAMETER						=> 'mysql_addParameter',
		N2F_DBEVT_EXECUTE_QUERY						=> 'mysql_executeQuery',
		N2F_DBEVT_GET_ROW							=> 'mysql_getRow',
		N2F_DBEVT_GET_ROWS							=> 'mysql_getRows',
		N2F_DBEVT_GET_LAST_INC						=> 'mysql_getLastInc',
		N2F_DBEVT_GET_NUMROWS						=> 'mysql_getNumRows',
		N2F_DBEVT_GET_RESULT						=> 'mysql_getResult'
	);

	// Check that the MySQL library is available
	if (function_exists('mysql_connect')) {
		n2f_database::addExtension('mysql', $handlers);

		$n2f->registerExtension(
			'n2f_database/mysql',
			'n2f_mysql_database',
			'0.1.1',
			'Andrew Male',
			'http://n2framework.com/'
		);
	} else {
		if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
			$n2f->debug->throwError(MYSQLDB_ERROR_MISSING_MYSQL, S('MYSQLDB_ERROR_MISSING_MYSQL'), 'n2f_database/mysql.ext.php');
		}
	}

	/**
	 * MySQL extension handler for opening a database connection.
	 *
	 * @param n2f_database $db	Current n2f_database object calling the handler
	 * @param n2f_cfg_db $cfg	Configuration value from n2f_cls::cfg for n2f_database object
	 * @return boolean
	 */
	function mysql_openConnection(n2f_database &$db, n2f_cfg_db $cfg) {
		if (!defined('MYSQLDB_CAN_CACHE')) {
			define('MYSQLDB_CAN_CACHE',		(bool)$n2f->hasExtension('cache'));
		}

		if (!isset($cfg->host) || !isset($cfg->name) || !isset($cfg->user) || !isset($cfg->pass)) {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_INVALID_CONFIG, S('MYSQLDB_ERROR_INVALID_CONFIG'), 'n2f_database/mysql.ext.php');
			}

			$db->conn = false;

			return(false);
		} else {
			$db->conn = @mysql_connect($cfg->host, $cfg->user, $cfg->pass, true);

			if ($db->isOpen()) {
				if (!@mysql_select_db($cfg->name, $db->conn)) {
					if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
						$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_DBSELECT, S('MYSQLDB_ERROR_FAILED_DBSELECT', array($cfg->name, mysql_error())), 'n2f_database/mysql.ext.php');
					}

					$db->conn = false;

					return(false);
				}
			} else {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_DBCONNECT, S('MYSQLDB_ERROR_FAILED_DBCONNECT', array(mysql_error())), 'n2f_database/mysql.ext.php');
				}

				return(false);
			}
		}

		return(true);
	}

	/**
	 * MySQL extension handler for closing a database connection.
	 *
	 * @param n2f_database $db	Current n2f_database object calling the handler
	 * @return boolean
	 */
	function mysql_closeConnection(n2f_database &$db) {
		if ($db->isOpen() === false) {
			return(false);
		}

		@mysql_close($db->conn);

		return(true);
	}

	/**
	 * MySQL extension handler for checking a database connection.
	 *
	 * @param n2f_database $db	Current n2f_database object calling the handler
	 * @return boolean
	 */
	function mysql_checkConnection(n2f_database $db) {
		if ($db->conn === false) {
			return(false);
		}

		return(true);
	}

	/**
	 * MySQL extension handler for adding a query parameter.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling the handler
	 * @param string $key				Key name for parameter
	 * @param mixed $value				Value for parameter
	 * @param integer $type				Type indicator for parameter
	 * @return boolean
	 */
	function mysql_addParameter(n2f_database_query &$query, $key, $value, $type) {
		if ($query->validParamType($type) === false) {
			return(false);
		}

		if ($type != N2F_DBTYPE_BINARY && $type != N2F_DBTYPE_RAW_STRING && $type != N2F_DBTYPE_LIKE_STRING) {
			$value = ($type == N2F_DBTYPE_STRING) ? "'" . mysql_cleanParam($value) . "'" : mysql_cleanParam($value);
		} else {
			$value = "'{$value}'";
		}

		$query->params[] = array(
			'key'	=> $key,
			'value'	=> $value,
			'type'	=> $type
		);

		return(true);
	}

	/**
	 * MySQL extension handler for executing a query.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling the handler
	 * @return boolean
	 */
	function mysql_executeQuery(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		if (strlen($query->query) < 1) {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_QUERY, S('MYSQLDB_ERROR_FAILED_QUERY', array('No query string was provided')), 'n2f_database/mysql.ext.php');
			}

			return(false);
		}

		if (count($query->params) > 0) {
			$pos = 0;

			foreach (array_values($query->params) as $param) {
				$pos = strpos($query->query, '?', $pos);

				if ($pos !== false) {
					$before = substr($query->query, 0, $pos);
					$after = substr($query->query, ($pos + 1));
					$newFront = $before . $param['value'];
					$query->query = $newFront . $after;
					$pos = strlen($newFront);
				} else {
					break;
				}
			}
		}

		$res = @mysql_query($query->query);

		if ($res) {
			$query->addData('res', $res);
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_QUERY, S('MYSQLDB_ERROR_FAILED_QUERY', array(mysql_error($query->db->conn))), 'n2f_database/mysql.ext.php');
			}

			$query->addError(S('MYSQLDB_ERROR_FAILED_QUERY', array(mysql_error($query->db->conn))));

			return(false);
		}

		return(true);
	}

	/**
	 * MySQL extension handler for retrieving one row.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return mixed
	 */
	function mysql_getRow(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();
		$ret = false;

		$res = $query->getData('res');

		if ($res !== null) {
			if ($res) {
				if (@mysql_num_rows($res) > 0) {
					$ret = @mysql_fetch_assoc($res);
				}
			} else {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_QUERY, S('MYSQLDB_ERROR_FAILED_QUERY', array('Invalid query for operation')), 'n2f_database/mysql.ext.php');
				}
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_NO_QUERY, S('MYSQLDB_ERROR_NO_QUERY'), 'n2f_database/mysql.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQL extension handler for retrieving all rows from a query.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return array
	 */
	function mysql_getRows(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();
		$ret = array();

		$res = $query->getData('res');

		if ($res !== null) {
			if ($res) {
				if (@mysql_num_rows($res) > 0) {
					while ($ret[] = @mysql_fetch_assoc($res)) { }
					unset($ret[count($ret) - 1]);
				}
			} else {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_QUERY, S('MYSQLDB_ERROR_FAILED_QUERY', array('Invalid query for operation')), 'n2f_database/mysql.ext.php');
				}
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_NO_QUERY, S('MYSQLDB_ERROR_NO_QUERY'), 'n2f_database/mysql.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQL extension handler for retrieving the last AUTO_INCREMENT integer from a query, when available.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return mixed
	 */
	function mysql_getLastInc(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();
		$ret = null;

		$res = $query->getData('res');

		if ($res !== null) {
			if ($res) {
				$ret = @mysql_insert_id();
			} else {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_QUERY, S('MYSQLDB_ERROR_FAILED_QUERY', array('Invalid query for operation')), 'n2f_database/mysql.ext.php');
				}
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_NO_QUERY, S('MYSQLDB_ERROR_NO_QUERY'), 'n2f_database/mysql.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQL extension handler for retrieving the number of rows returned by a query, if any.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return integer
	 */
	function mysql_getNumRows(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();
		$ret = 0;

		$res = $query->getData('res');

		if ($res !== null) {
			if ($res) {
				$ret = @mysql_num_rows($res);
			} else {
				$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_QUERY, S('MYSQLDB_ERROR_FAILED_QUERY', array('Invalid query for operation')), 'n2f_database/mysql.ext.php');
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_NO_QUERY, S('MYSQLDB_ERROR_NO_QUERY'), 'n2f_database/mysql.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQL extension handler for retrieving a specific field from a specific row on a query.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @param integer $offset			Row offset to pull field from
	 * @param string $field_name			Name of field to pull data from
	 * @return mixed
	 */
	function mysql_getResult(n2f_database_query &$query, $offset, $field_name) {
		$n2f = n2f_cls::getInstance();
		$ret = null;

		$res = $query->getData('res');

		if ($res !== null) {
			if ($res) {
				if (@mysql_num_rows($res) > 0) {
					$ret = @mysql_result($res, $offset, $field_name);
				}
			} else {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(MYSQLDB_ERROR_FAILED_QUERY, S('MYSQLDB_ERROR_FAILED_QUERY', array('Invalid query for operation')), 'n2f_database/mysql.ext.php');
				}
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLDB_ERROR_NO_QUERY, S('MYSQLDB_ERROR_NO_QUERY'), 'n2f_database/mysql.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * Returns a sanitized parameter for MySQL.
	 *
	 * @param mixed $value	Value to sanitize
	 * @return mixed
	 */
	function mysql_cleanParam($value) {
		$value = mysql_real_escape_string($value);
		$value = addcslashes($value, "\x00\n\r\'\x1a\x3c\x3e\x25");

		return($value);
	}

?>