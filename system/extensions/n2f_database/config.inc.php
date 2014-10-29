<?php

	/*
	 * $Id$
	 */

	// Register extension
	n2f_cls::getInstance()->registerExtension(
		'n2f_template',
		'n2f_database',
		0.2,
		'Matthew Hykes, Andrew Male',
		'http://n2framework.com/'
	);

	/**
	 * Configuration class for database class and extensions.
	 *
	 */
	class n2f_cfg_db {
		/**
		 * The current type for the database engine.
		 *
		 * @var string
		 */
		public $type;
		/**
		 * The current host for the database engine.
		 *
		 * @var string
		 */
		public $host;
		/**
		 * The current databse name for the database engine.
		 *
		 * @var string
		 */
		public $name;
		/**
		 * The current username for the database engine.
		 *
		 * @var string
		 */
		public $user;
		/**
		 * The current user password for the database engine.
		 *
		 * @var string
		 */
		public $pass;
		/**
		 * The current port number for the database engine.
		 *
		 * @var integer
		 */
		public $port;
		/**
		 * The current socket for the database engine.
		 *
		 * @var string
		 */
		public $sock;
		/**
		 * The current filename for the database engine.
		 *
		 * @var string
		 */
		public $file;
		/**
		 * The current mode for the database engine.
		 *
		 * @var mixed
		 */
		public $mode;

		/**
		 * Initializes a new n2f_cfg_db object.
		 *
		 * @param array $vals
		 * @return n2f_cfg_db
		 */
		public function __construct(array $vals = null) {
			if ($vals === null) {
				$this->type = null;
				$this->host = null;
				$this->name = null;
				$this->user = null;
				$this->pass = null;
				$this->port = null;
				$this->sock = null;
				$this->file = null;
				$this->mode = null;
			} else {
				$this->type = (isset($vals['type'])) ? $vals['type'] : null;
				$this->host = (isset($vals['host'])) ? $vals['host'] : null;
				$this->name = (isset($vals['name'])) ? $vals['name'] : null;
				$this->user = (isset($vals['user'])) ? $vals['user'] : null;
				$this->pass = (isset($vals['pass'])) ? $vals['pass'] : null;
				$this->port = (isset($vals['port'])) ? $vals['port'] : null;
				$this->sock = (isset($vals['sock'])) ? $vals['sock'] : null;
				$this->file = (isset($vals['file'])) ? $vals['file'] : null;
				$this->mode = (isset($vals['mode'])) ? $vals['mode'] : null;
			}

			return($this);
		}
	}

	// Database Global Types
	define('N2F_DBTYPE_INTEGER',							0);
	define('N2F_DBTYPE_DOUBLE',							1);
	define('N2F_DBTYPE_STRING',							2);
	define('N2F_DBTYPE_BINARY',							3);
	define('N2F_DBTYPE_RAW_STRING',						4);
	define('N2F_DBTYPE_LIKE_STRING',						5);

	// N2 Framework Yverdon Database Event Constants
	define('N2F_DBEVT_OPEN_CONNECTION',					'N2F_DBEVT_OPEN_CONNECTION');
	define('N2F_DBEVT_CLOSE_CONNECTION',					'N2F_DBEVT_CLOSE_CONNECTION');
	define('N2F_DBEVT_CHECK_CONNECTION',					'N2F_DBEVT_CHECK_CONNECTION');
	define('N2F_DBEVT_ADD_PARAMETER',						'N2F_DBEVT_ADD_PARAMETER');
	define('N2F_DBEVT_EXECUTE_QUERY',						'N2F_DBEVT_EXECUTE_QUERY');
	define('N2F_DBEVT_GET_ROW',							'N2F_DBEVT_GET_ROW');
	define('N2F_DBEVT_GET_ROWS',							'N2F_DBEVT_GET_ROWS');
	define('N2F_DBEVT_GET_LAST_INC',						'N2F_DBEVT_GET_LAST_INC');
	define('N2F_DBEVT_GET_NUMROWS',						'N2F_DBEVT_GET_NUMROWS');
	define('N2F_DBEVT_GET_RESULT',						'N2F_DBEVT_GET_RESULT');
	define('N2F_DBEVT_ENGINE_REGISTERED',					'N2F_DBEVT_ENGINE_REGISTERED');
	define('N2F_DBEVT_HANDLER_CREATED',					'N2F_DBEVT_HANDLER_CREATED');
	define('N2F_DBEVT_CONNECTION_OPENED',					'N2F_DBEVT_CONNECTION_OPENED');
	define('N2F_DBEVT_CONNECTION_CLOSED',					'N2F_DBEVT_CONNECTION_CLOSED');
	define('N2F_DBEVT_QUERY_CREATED',						'N2F_DBEVT_QUERY_CREATED');
	define('N2F_DBEVT_PARAMETER_ADDED',					'N2F_DBEVT_PARAMETER_ADDED');
	define('N2F_DBEVT_QUERY_EXECUTED',						'N2F_DBEVT_QUERY_EXECUTED');
	define('N2F_DBEVT_ROW_RETRIEVED',						'N2F_DBEVT_ROW_RETRIEVED');
	define('N2F_DBEVT_ROWS_RETRIEVED',						'N2F_DBEVT_ROWS_RETRIEVED');
	define('N2F_DBEVT_LAST_INC_RETRIEVED',					'N2F_DBEVT_LAST_INC_RETRIEVED');
	define('N2F_DBEVT_NUMROWS_RETRIEVED',					'N2F_DBEVT_NUMROWS_RETRIEVED');
	define('N2F_DBEVT_RESULT_RETRIEVED',					'N2F_DBEVT_RESULT_RETRIEVED');

	// Database Error Number Constants
	define('N2F_ERROR_DB_EXTENSION_NOT_LOADED',				'0001');
	define('N2F_ERROR_DB_EXTENSION_EMPTY',					'0002');
	define('N2F_ERROR_DB_NOT_LOADED',						'0003');
	define('N2F_ERROR_DB_INVALID_STORED_QUERY',				'0004');

	// Database Notice Number Constants
	define('N2F_NOTICE_DB_EXTENSION_LOADED',				'0001');
	define('N2F_NOTICE_DB_CONNECTION_OPENED',				'0002');
	define('N2F_NOTICE_DB_CONNECTION_CLOSED',				'0003');
	define('N2F_NOTICE_DB_QUERY_CREATED',					'0004');
	define('N2F_NOTICE_DB_PARAMETER_ADDED',					'0005');
	define('N2F_NOTICE_DB_QUERY_EXECUTED',					'0006');

	// Database Warning Number Constants
	define('N2F_WARN_DB_PARAMETERS_NOT_SUPPLIED',			'0001');
	define('N2F_WARN_DB_INCORRECT_STORED_PARAMETER_COUNT',		'0002');

	// N2 Framework Yverdon Database English Strings
	L('en', 'N2F_ERROR_DB_EXTENSION_NOT_LOADED',				"The '_%1%_' database extension does not exist in the system.");
	L('en', 'N2F_ERROR_DB_EXTENSION_EMPTY',					"The database handler has no extension set.");
	L('en', 'N2F_ERROR_DB_NOT_LOADED',						"The database handler was not properly loaded or opened.");
	L('en', 'N2F_ERROR_DB_INVALID_STORED_QUERY',				"The requested stored query (_%1%_) was not found in the stack.");
	L('en', 'N2F_NOTICE_DB_EXTENSION_LOADED',				"The '_%1%_' extension was registered with the global database object.");
	L('en', 'N2F_NOTICE_DB_CONNECTION_OPENED',				"The database connection was opened for the '_%1%_' engine.");
	L('en', 'N2F_NOTICE_DB_CONNECTION_CLOSED',				"The database connection was closed for the '_%1%_' engine.");
	L('en', 'N2F_NOTICE_DB_QUERY_CREATED',					"A new query has been created for the '_%1%_' engine.  The query provided was '_%2%_'.");
	L('en', 'N2F_NOTICE_DB_PARAMETER_ADDED',				"A parameter has been added for the key '_%1%_'.");
	L('en', 'N2F_NOTICE_DB_QUERY_EXECUTED',					"A query has been executed.");
	L('en', 'N2F_WARN_DB_PARAMETERS_NOT_SUPPLIED',			"There were no parameters supplied for the query (_%1%_).");
	L('en', 'N2F_WARN_DB_INCORRECT_STORED_PARAMETER_COUNT',	"The provided stored query parameter count did not match the stack types.");

	L('de', 'N2F_ERROR_DB_EXTENSION_NOT_LOADED',				"Die '_%1%_' Datenbankerweiterung existiert nicht im System.");
	L('de', 'N2F_ERROR_DB_EXTENSION_EMPTY',					"Es wurde keine Erweiterung fpr den Datenbank-Handler gesetzt. ");
	L('de', 'N2F_ERROR_DB_NOT_LOADED',						"Der Datenbank-Handler wurde nicht richtig geladen oder geöffnet.");
	L('de', 'N2F_ERROR_DB_INVALID_STORED_QUERY',				"The requested stored query (_%1%_) was not found in the stack.");
	L('de', 'N2F_NOTICE_DB_EXTENSION_LOADED',				"Die Erweiterung '_%1%_' wurde im globalen Datenbankobjekt eingetragen.");
	L('de', 'N2F_NOTICE_DB_CONNECTION_OPENED',				"Die Datenbankverbindung würde für die '_%1%_'-Engine geöffnet.");
 	L('de', 'N2F_NOTICE_DB_CONNECTION_CLOSED',				"Die Datenbankverbindung würde für die '_%1%_'-Engine geschlossen.");
	L('de', 'N2F_NOTICE_DB_QUERY_CREATED',					"Eine neue Anfrage wurde für die '_%1%_'-Engine erstellt.  Die Anfrage lautete '_%2%_'.");
	L('de', 'N2F_NOTICE_DB_PARAMETER_ADDED',				"Ein Parameter würde für den Schlüssel '_%1%_' hinzugefügt.");
	L('de', 'N2F_NOTICE_DB_QUERY_EXECUTED',					"Eine Anfrage wurde ausgeführt.");
	L('de', 'N2F_WARN_DB_PARAMETERS_NOT_SUPPLIED',			"There were no parameters supplied for the query (_%1%_).");
	L('de', 'N2F_WARN_DB_INCORRECT_STORED_PARAMETER_COUNT',	"The provided stored query parameter count did not match the stack types.");

	L('es', 'N2F_ERROR_DB_EXTENSION_NOT_LOADED',				"La extensión de base de datos  '_%1%_' no existe en el sistema.");
	L('es', 'N2F_ERROR_DB_EXTENSION_EMPTY',					"El manejador de base de datos no tiene una extensión establecida.");
	L('es', 'N2F_ERROR_DB_NOT_LOADED',						"El manejador de base de datos no fue cargado o abierto correctamente.");
	L('es', 'N2F_ERROR_DB_INVALID_STORED_QUERY',				"The requested stored query (_%1%_) was not found in the stack.");
	L('es', 'N2F_NOTICE_DB_EXTENSION_LOADED',				"La extensión '_%1%_'  fue registrada con el objeto global de base de datos.");
	L('es', 'N2F_NOTICE_DB_CONNECTION_OPENED',				"La conexión a la base de datos fue abierta para el motor '_%1%_'.");
	L('es', 'N2F_NOTICE_DB_CONNECTION_CLOSED',				"La conexión a la base de datos fue cerrada para el motor '_%1%_'.");
	L('es', 'N2F_NOTICE_DB_QUERY_CREATED',					"Se ha creado una nueva consulta para el motor '_%1%_'.  La consulta proporcionada fue '_%2%_'.");
	L('es', 'N2F_NOTICE_DB_PARAMETER_ADDED',				"Se ha agregado un parámetro para la llave '_%1%_'.");
	L('es', 'N2F_NOTICE_DB_QUERY_EXECUTED',					"Una consulta ha sido ejecutada.");
	L('es', 'N2F_WARN_DB_PARAMETERS_NOT_SUPPLIED',			"There were no parameters supplied for the query (_%1%_).");
	L('es', 'N2F_WARN_DB_INCORRECT_STORED_PARAMETER_COUNT',	"The provided stored query parameter count did not match the stack types.");

?>