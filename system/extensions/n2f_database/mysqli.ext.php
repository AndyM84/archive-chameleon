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
	 * $Id: db.mysqli.ext.php 85 2010-02-14 22:13:49Z amale $
	 */

	// Global variable(s)
	$n2f = n2f_cls::getInstance();

	// MySQLi option constants
	define('MYSQLIDB_OPTION_AUTOCOMMIT',					1);
	define('MYSQLIDB_OPTION_PREPARED',						2);

	// Error constants
	define('MYSQLIDB_ERROR_INVALID_CONFIG',					'0001');
	define('MYSQLIDB_ERROR_FAILED_CONNECT',					'0002');
	define('MYSQLIDB_ERROR_FAILED_PARAMETER',				'0003');
	define('MYSQLIDB_ERROR_FAILED_QUERY_PREPARE',			'0004');
	define('MYSQLIDB_ERROR_OPEN_RESULT',					'0005');
	define('MYSQLIDB_ERROR_FAILED_QUERY',					'0006');
	define('MYSQLIDB_ERROR_NO_RESULT',						'0007');
	define('MYSQLIDB_ERROR_MISSING_MYSQLI',					'0008');

	// English error strings
	L('en', 'MYSQLIDB_ERROR_INVALID_CONFIG',				'The configuration submitted for the MySQLi database extension was invalid.');
	L('en', 'MYSQLIDB_ERROR_FAILED_CONNECT',				'Failed to connect to the MySQL database. (_%1%_)');
	L('en', 'MYSQLIDB_ERROR_FAILED_PARAMETER',				'A parameter failed to load for the following reason(s): _%1%_');
	L('en', 'MYSQLIDB_ERROR_FAILED_QUERY_PREPARE',			'Failed to prepare the query for execution.');
	L('en', 'MYSQLIDB_ERROR_OPEN_RESULT',					'Unable to execute query, another result is currently open or has failed.');
	L('en', 'MYSQLIDB_ERROR_FAILED_QUERY',					'Failed to execute the query.');
	L('en', 'MYSQLIDB_ERROR_NO_RESULT',					'No result was available or open.');
	L('en', 'MYSQLIDB_ERROR_MISSING_MYSQLI',				'The PHP MySQLi extension is not installed on this system, aborting MySQLi extension registration.');

	// German error strings
	L('de', 'MYSQLIDB_ERROR_INVALID_CONFIG',				'The configuration submitted for the MySQLi database extension was invalid.');
	L('de', 'MYSQLIDB_ERROR_FAILED_CONNECT',				'Failed to connect to the MySQL database. (_%1%_)');
	L('de', 'MYSQLIDB_ERROR_FAILED_PARAMETER',				'A parameter failed to load for the following reason(s): _%1%_');
	L('de', 'MYSQLIDB_ERROR_FAILED_QUERY_PREPARE',			'Failed to prepare the query for execution.');
	L('de', 'MYSQLIDB_ERROR_OPEN_RESULT',					'Unable to execute query, another result is currently open or has failed.');
	L('de', 'MYSQLIDB_ERROR_FAILED_QUERY',					'Failed to execute the query.');
	L('de', 'MYSQLIDB_ERROR_NO_RESULT',					'No result was available or open.');
	L('de', 'MYSQLIDB_ERROR_MISSING_MYSQLI',				'The PHP MySQLi extension is not installed on this system, aborting MySQLi extension registration.');

	// Spanish error strings
	L('es', 'MYSQLIDB_ERROR_INVALID_CONFIG',				'The configuration submitted for the MySQLi database extension was invalid.');
	L('es', 'MYSQLIDB_ERROR_FAILED_CONNECT',				'Failed to connect to the MySQL database. (_%1%_)');
	L('es', 'MYSQLIDB_ERROR_FAILED_PARAMETER',				'A parameter failed to load for the following reason(s): _%1%_');
	L('es', 'MYSQLIDB_ERROR_FAILED_QUERY_PREPARE',			'Failed to prepare the query for execution.');
	L('es', 'MYSQLIDB_ERROR_OPEN_RESULT',					'Unable to execute query, another result is currently open or has failed.');
	L('es', 'MYSQLIDB_ERROR_FAILED_QUERY',					'Failed to execute the query.');
	L('es', 'MYSQLIDB_ERROR_NO_RESULT',					'No result was available or open.');
	L('es', 'MYSQLIDB_ERROR_MISSING_MYSQLI',				'The PHP MySQLi extension is not installed on this system, aborting MySQLi extension registration.');

	// Swedish error strings
	L('se', 'MYSQLIDB_ERROR_INVALID_CONFIG',				'The configuration submitted for the MySQLi database extension was invalid.');
	L('se', 'MYSQLIDB_ERROR_FAILED_CONNECT',				'Failed to connect to the MySQL database. (_%1%_)');
	L('se', 'MYSQLIDB_ERROR_FAILED_PARAMETER',				'A parameter failed to load for the following reason(s): _%1%_');
	L('se', 'MYSQLIDB_ERROR_FAILED_QUERY_PREPARE',			'Failed to prepare the query for execution.');
	L('se', 'MYSQLIDB_ERROR_OPEN_RESULT',					'Unable to execute query, another result is currently open or has failed.');
	L('se', 'MYSQLIDB_ERROR_FAILED_QUERY',					'Failed to execute the query.');
	L('se', 'MYSQLIDB_ERROR_NO_RESULT',					'No result was available or open.');
	L('se', 'MYSQLIDB_ERROR_MISSING_MYSQLI',				'The PHP MySQLi extension is not installed on this system, aborting MySQLi extension registration.');

	// Create our array of handlers
	$handlers = array(
		N2F_DBEVT_OPEN_CONNECTION						=> 'mysqli_openConnection',
		N2F_DBEVT_CLOSE_CONNECTION						=> 'mysqli_closeConnection',
		N2F_DBEVT_CHECK_CONNECTION						=> 'mysqli_checkConnection',
		N2F_DBEVT_ADD_PARAMETER							=> 'mysqli_addParameter',
		N2F_DBEVT_EXECUTE_QUERY							=> 'mysqli_executeQuery',
		N2F_DBEVT_GET_ROW								=> 'mysqli_getRow',
		N2F_DBEVT_GET_ROWS								=> 'mysqli_getRows',
		N2F_DBEVT_GET_LAST_INC							=> 'mysqli_getLastInc',
		N2F_DBEVT_GET_NUMROWS							=> 'mysqli_getNumRows',
		N2F_DBEVT_GET_RESULT							=> 'mysqli_getResult'
	);

	if (function_exists('mysqli_autocommit')) {
		n2f_database::addExtension('mysqli', $handlers);

		$n2f->registerExtension(
			'n2f_database/mysqli',
			'n2f_mysqli_database',
			'0.2.1',
			'Andrew Male',
			'http://n2framework.com/'
		);
	} else {
		if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
			$n2f->debug->throwError(MYSQLIDB_ERROR_MISSING_MYSQLI, S('MYSQLIDB_ERROR_MISSING_MYSQLI'), 'mysqli.ext.php');
		}
	}


	/**
	 * MySQLi extension handler for opening a database connection.
	 *
	 * @param n2f_database $db	Current n2f_database object calling the handler
	 * @param n2f_cfg_db $cfg	Configuration value from n2f_cls::cfg for n2f_database object
	 * @return boolean
	 */
	function mysqli_openConnection(n2f_database &$db, n2f_cfg_db $cfg) {
		$n2f = n2f_cls::getInstance();

		if (!defined('MYSQLIDB_CAN_CACHE')) {
			define('MYSQLIDB_CAN_CACHE',		(bool)$n2f->hasExtension('cache'));
		}

		if (!isset($cfg->host) || !isset($cfg->user) || !isset($cfg->pass) || !isset($cfg->name)) {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_INVALID_CONFIG, S('MYSQLIDB_ERROR_INVALID_CONFIG'), 'mysqli.ext.php');
			}

			$db->conn = false;

			return(false);
		} else {
			if ($cfg->port !== null && $cfg->sock !== null) {
				$db->conn = @mysqli_connect($cfg->host, $cfg->user, $cfg->pass, $cfg->name, $cfg->port, $cfg->sock);
			} else if ($cfg->port !== null) {
				$db->conn = @mysqli_connect($cfg->host, $cfg->user, $cfg->pass, $cfg->name, $cfg->port);
			} else {
				$db->conn = @mysqli_connect($cfg->host, $cfg->user, $cfg->pass, $cfg->name);
			}

			if (mysqli_connect_error() != '') {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(MYSQLIDB_ERROR_FAILED_CONNECT, S('MYSQLIDB_ERROR_FAILED_CONNECT', array(mysqli_connect_error())), 'mysqli.ext.php');
				}

				$db->conn = false;

				return(false);
			}
		}

		$db->hookEvent(N2F_DBEVT_QUERY_CREATED, 'mysqli_queryCreated');

		return(true);
	}

	/**
	 * MySQLi
	 *
	 * @param n2f_database_query $query
	 * @param n2f_database $db
	 */
	function mysqli_queryCreated(n2f_database_query &$query, n2f_database &$db) {
		if ($db->isOpen()) {
			$opts = $query->getData('options');

			if ($opts !== null && $opts & MYSQLIDB_OPTION_PREPARED) {
				$qName = md5(time());
				$query->addData('qName', $qName);

				$db->conn->real_query('PREPARE '.$qName.' FROM \''.$query->query.'\'');
			}
		}
	}

	/**
	 * MySQLi extension handler for closing a database connection.
	 *
	 * @param n2f_database $db	Current n2f_database object calling the handler
	 * @return boolean
	 */
	function mysqli_closeConnection(n2f_database &$db) {
		if ($db->isOpen() === false) {
			return(false);
		}

		$queries = $db->getData('queries');

		if ($queries !== null) {
			foreach (array_values($queries) as $query) {
				if ($query instanceof mysqli_stmt || $query instanceof mysqli_result) {
					$query->close();
				}
			}
		}

		$db->conn->close();

		return(true);
	}

	/**
	 * MySQLi extension handler for checking a database connection.
	 *
	 * @param n2f_database $db	Current n2f_database object calling the handler
	 * @return boolean
	 */
	function mysqli_checkConnection(n2f_database $db) {
		if (!isset($db->conn) || $db->conn == false) {
			return(false);
		}

		return(true);
	}

	/**
	 * MySQLi extension handler for adding parameters to a prepared statement.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @param string $key				Key name of parameter (not supported)
	 * @param mixed $value				Value of parameter
	 * @param mixed $type				Type of parameter (N2F_DBTYPE_*)
	 * @return boolean
	 */
	function mysqli_addParameter(n2f_database_query &$query, $key, $value, $type) {
		$n2f = n2f_cls::getInstance();

		$opts = $query->getData('options');

		if ($query->db->isOpen()) {
			if ($query->validParamType($type) === false) {
				if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
					$n2f->debug->throwError(MYSQLIDB_ERROR_FAILED_PARAMETER, S('MYSQLIDB_ERROR_FAILED_PARAMETER', array('Invalid type provided for parameter')), 'mysqli.ext.php');
				}

				return(false);
			} else {
				$value = ($type != N2F_DBTYPE_BINARY && $type != N2F_DBTYPE_LIKE_STRING && $type != N2F_DBTYPE_RAW_STRING) ? mysqli_sanitizeData($query, $value, $type) : "'{$value}'";

				switch ($type) {
					case N2F_DBTYPE_BINARY:
						$type = 'b';
						break;
					case N2F_DBTYPE_DOUBLE:
						$type = 'd';
						break;
					case N2F_DBTYPE_INTEGER:
						$type = 'i';
						break;
					case N2F_DBTYPE_LIKE_STRING:
					case N2F_DBTYPE_RAW_STRING:
					case N2F_DBTYPE_STRING:
					default:
						$type = 's';
						break;
				}

				$query->params[] = array(
					'key'	=> $key,
					'value'	=> $value,
					'type'	=> $type
				);

				if ($opts !== null && $opts & MYSQLIDB_OPTION_PREPARED && $query->db->isOpen()) {
					$query->db->conn->real_query('SET @v'.(count($query->params) - 1).' = \''.$value.'\'');
				}
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_FAILED_PARAMETER, S('MYSQLIDB_ERROR_FAILED_PARAMETER', array('The database connection was closed')), 'mysqli.ext.php');
			}

			return(false);
		}

		return(true);
	}

	/**
	 * MySQLi extension handler for executing the query.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return boolean
	 */
	function mysqli_executeQuery(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		$result = $query->getData('result');
		$opts = $query->getData('options');

		if ($result !== null || $query->isError()) {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_OPEN_RESULT, S('MYSQLIDB_ERROR_OPEN_RESULT'), 'mysqli.ext.php');
			}

			$query->addError(S('MYSQLIDB_ERROR_OPEN_RESULT'));

			return(false);
		}

		if (count($query->params) > 0 && strpos($query->query, '?') !== false) {
			if ($opts !== null && $opts & MYSQLIDB_OPTION_PREPARED) {
				$qName = $query->getData('qName');
				$vars = array();

				foreach (array_keys($query->params) as $offset) {
					$vars[] = '@v'.$offset;
				}

				$result = $query->db->conn->query('EXECUTE '.$qName.' USING '.implode(', ', array_values($vars)));
			} else {
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

				$result = $query->db->conn->query($query->query);
			}
		} else {
			$result = $query->db->conn->query($query->query);
		}

		if ($result) {
			$query->addData('result', $result);

			$queries = $query->db->getData('queries');

			if ($queries !== null) {
				$queries[] = $result;
			} else {
				$queries = array($result);
			}

			$query->db->addData('queries', $queries);

			if ($opts !== null && $opts & MYSQLIDB_OPTION_AUTOCOMMIT) {
				mysqli_doCommit($query);
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_FAILED_QUERY, S('MYSQLIDB_ERROR_FAILED_QUERY'), 'mysqli.ext.php');
			}

			$query->addError(S('MYSQLIDB_ERROR_FAILED_QUERY'));
			$query->addError($query->db->conn->error);

			return(false);
		}

		return(true);
	}

	/**
	 * Static method for turning off MySQL's autocommit feature.
	 *
	 * @param n2f_database_query $query	Query to attempt turning off the autocommit feature for
	 * @return null
	 */
	function mysqli_doAutoCommit(n2f_database_query &$query) {
		if ($query->db->isOpen()) {
			$query->db->conn->autocommit(false);
		}

		return(null);
	}

	/**
	 * Static method for beginning a transaction on a query.
	 *
	 * @param n2f_database_query $query	Query to attempt starting a transaction on
	 * @return null
	 */
	function mysqli_doTranscation(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		if ($query->db->isOpen()) {
			$result = $query->db->conn->query("START TRANSACTION");

			if (!$result && $n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_FAILED_QUERY, S('MYSQLIDB_ERROR_FAILED_QUERY'), 'mysqli.ext.php');
			}
		}

		return(null);
	}

	/**
	 * Static method for committing a transaction on a query.
	 *
	 * @param n2f_database_query $query	Query to attempt commit on
	 * @return null
	 */
	function mysqli_doCommit(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		if ($query->db->isOpen()) {
			$result = $query->db->conn->query("COMMIT");

			if (!$result && $n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_FAILED_QUERY, S('MYSQLIDB_ERROR_FAILED_QUERY'), 'mysqli.ext.php');
			}
		}

		return(null);
	}

	/**
	 * Static method for rolling back a transaction on a query.
	 *
	 * @param n2f_database_query $query	Query to attempt rollback on
	 * @return null
	 */
	function mysqli_doRollback(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		if ($query->db->isOpen()) {
			$result = $query->db->conn->query("ROLLBACK");

			if (!$result && $n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_FAILED_QUERY, S('MYSQLIDB_ERROR_FAILED_QUERY'), 'mysqli.ext.php');
			}
		}

		return(null);
	}

	/**
	 * MySQLi extension handler for pulling one row from the query.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return mixed
	 */
	function mysqli_getRow(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		$ret = null;

		$result = $query->getData('result');

		if ($result !== null) {
			$ret = $result->fetch_assoc();
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_NO_RESULT, S('MYSQLIDB_ERROR_NO_RESULT'), 'mysqli.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQLi extension handler for pulling all rows from a query.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return array
	 */
	function mysqli_getRows(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		$ret = array();

		$result = $query->getData('result');

		if ($result !== null) {
			while ($ret[] = $result->fetch_assoc()) { }
			unset($ret[count($ret) - 1]);
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_NO_RESULT, S('MYSQLIDB_ERROR_NO_RESULT'), 'mysqli.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQLi extension handler for getting the last AUTO_INCREMENT value produced by a query's INSERT statement.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return mixed
	 */
	function mysqli_getLastInc(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		$ret = null;

		$result = $query->getData('result');

		if ($result !== null) {
			if ($query->db->conn->insert_id > 0) {
				$ret = $query->db->conn->insert_id;
			}
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_NO_RESULT, S('MYSQLIDB_ERROR_NO_RESULT'), 'mysqli.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQLi extension handler for getting the number of rows returned by a result.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @return integer
	 */
	function mysqli_getNumRows(n2f_database_query &$query) {
		$n2f = n2f_cls::getInstance();

		$ret = 0;

		$result = $query->getData('result');

		if ($result !== null) {
			$ret = $result->num_rows;
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_NO_RESULT, S('MYSQLIDB_ERROR_NO_RESULT'), 'mysqli.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQLi extension handler for getting a field from a query.
	 *
	 * @param n2f_database_query $query	Current n2f_database_query object calling handler
	 * @param unknown_type $offset		Row offset to get field value from
	 * @param unknown_type $field_name		Name of field to get value from
	 * @return mixed
	 */
	function mysqli_getResult(n2f_database_query &$query, $offset, $field_name) {
		$n2f = n2f_cls::getInstance();

		$ret = null;

		$result = $query->getData('result');

		if ($result !== null) {
			$result->data_seek($offset);
			$row = $result->fetch_assoc();
			$ret = $row[$field_name];
		} else {
			if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
				$n2f->debug->throwError(MYSQLIDB_ERROR_NO_RESULT, S('MYSQLIDB_ERROR_NO_RESULT'), 'mysqli.ext.php');
			}
		}

		return($ret);
	}

	/**
	 * MySQLi extension handler for sanitizing data used in a query.
	 *
	 * @param n2f_database_query $query	Query object to associate with sanitization
	 * @param mixed $data				Data to sanitize
	 * @param boolean $type				Whether or not data is being used in a LIKE statement
	 * @return mixed
	 */
	function mysqli_sanitizeData(n2f_database_query &$query, $data, $type) {
		if ($query->db->isOpen()) {
			$data = mysqli_real_escape_string($query->db->conn, $data);


			if ($type != N2F_DBTYPE_INTEGER) {
				$data = "'{$data}'";
			}
		}

		return($data);
	}

?>