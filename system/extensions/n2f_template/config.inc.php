<?php

	/*
	 * $Id$
	 */

	// Register extension
	n2f_cls::getInstance()->registerExtension(
		'n2f_template',
		'n2f_template',
		0.2,
		'Matthew Hykes, Andrew Male',
		'http://n2framework.com/'
	);

	/**
	 * Configuration class for the template engine and extensions.
	 *
	 */
	class n2f_cfg_tpl {
		/**
		 * The current template engine skin.
		 *
		 * @var string
		 */
		public $skin;
		/**
		 * The current template engine expiration time.  (Only works if the Cache extension is installed)
		 *
		 * @var integer
		 */
		public $exp;

		/**
		 * Initializes a new n2f_cfg_tpl object.
		 *
		 * @param array $vals
		 * @return n2f_cfg_tpl
		 */
		public function __construct(array $vals = null) {
			if ($vals === null) {
				$this->skin = 'default';
				$this->exp = 1500;
			} else {
				$this->skin = (isset($vals['skin'])) ? $vals['skin'] : 'default';
				$this->exp = (isset($vals['exp'])) ? $vals['exp'] : 1500;
			}

			return($this);
		}
	}

	// N2 Framework Yverdon Template Event Constants
	define('N2F_TPLEVT_SET_BASE',						'N2F_TPLEVT_SET_BASE');
	define('N2F_TPLEVT_SET_MODULE',					'N2F_TPLEVT_SET_MODULE');
	define('N2F_TPLEVT_SET_SKIN',						'N2F_TPLEVT_SET_SKIN');
	define('N2F_TPLEVT_SET_FILE',						'N2F_TPLEVT_SET_FILE');
	define('N2F_TPLEVT_SET_FIELD',					'N2F_TPLEVT_SET_FIELD');
	define('N2F_TPLEVT_SET_FIELDS',					'N2F_TPLEVT_SET_FIELDS');
	define('N2F_TPLEVT_SET_EXPIRE',					'N2F_TPLEVT_SET_EXPIRE');
	define('N2F_TPLEVT_SET_BINDING',					'N2F_TPLEVT_SET_BINDING');
	define('N2F_TPLEVT_SET_BINDINGS',					'N2F_TPLEVT_SET_BINDINGS');
	define('N2F_TPLEVT_RENDER',						'N2F_TPLEVT_RENDER');
	define('N2F_TPLEVT_FETCH',						'N2F_TPLEVT_FETCH');
	define('N2F_TPLEVT_DISPLAY',						'N2F_TPLEVT_DISPLAY');
	define('N2F_TPLEVT_SET_ALIAS',					'N2F_TPLEVT_SET_ALIAS');
	define('N2F_TPLEVT_SET_GALIAS',					'N2F_TPLEVT_SET_GALIAS');
	define('N2F_TPLEVT_CREATED',						'N2F_TPLEVT_CREATED');
	define('N2F_TPLEVT_BASE_SET',						'N2F_TPLEVT_BASE_SET');
	define('N2F_TPLEVT_MODULE_SET',					'N2F_TPLEVT_MODULE_SET');
	define('N2F_TPLEVT_SKIN_SET',						'N2F_TPLEVT_SKIN_SET');
	define('N2F_TPLEVT_FILE_SET',						'N2F_TPLEVT_FILE_SET');
	define('N2F_TPLEVT_FIELD_SET',					'N2F_TPLEVT_FIELD_SET');
	define('N2F_TPLEVT_FIELDS_SET',					'N2F_TPLEVT_FIELDS_SET');
	define('N2F_TPLEVT_EXPIRE_SET',					'N2F_TPLEVT_EXPIRE_SET');
	define('N2F_TPLEVT_BINDING_SET',					'N2F_TPLEVT_BINDING_SET');
	define('N2F_TPLEVT_BINDINGS_SET',					'N2F_TPLEVT_BINDINGS_SET');
	define('N2F_TPLEVT_RENDERED',						'N2F_TPLEVT_RENDERED');
	define('N2F_TPLEVT_FETCHED',						'N2F_TPLEVT_FETCHED');
	define('N2F_TPLEVT_DISPLAYED',					'N2F_TPLEVT_DISPLAYED');
	define('N2F_TPLEVT_ALIAS_SET',					'N2F_TPLEVT_ALIAS_SET');
	define('N2F_TPLEVT_GALIAS_SET',					'N2F_TPLEVT_GALIAS_SET');

	// N2 Framework Yverdon Template Error Constants
	define('N2F_ERROR_TPL_EXTENSION_NOT_LOADED', 		'0001');
	define('N2F_ERROR_TPL_SIMPLEXML_PARSE',				'0002');

	// N2 Framework Yverdon Template Notice Constants
	define('N2F_NOTICE_TPL_BASE_SET',					'0001');
	define('N2F_NOTICE_TPL_MODULE_SET',				'0002');
	define('N2F_NOTICE_TPL_SKIN_SET',					'0003');
	define('N2F_NOTICE_TPL_FILE_SET',					'0004');
	define('N2F_NOTICE_TPL_FIELD_SET',					'0005');
	define('N2F_NOTICE_TPL_FIELDS_SET',				'0006');
	define('N2F_NOTICE_TPL_EXPIRE_SET',				'0007');
	define('N2F_NOTICE_TPL_BINDING_SET',				'0008');
	define('N2F_NOTICE_TPL_BINDINGS_SET',				'0009');
	define('N2F_NOTICE_TPL_RENDER',					'0010');
	define('N2F_NOTICE_TPL_FETCH',					'0011');
	define('N2F_NOTICE_TPL_DISPLAY',					'0012');
	define('N2F_NOTICE_TPL_GFIELD_SET',				'0013');
	define('N2F_NOTICE_TPL_ALIAS_SET',					'0014');
	define('N2F_NOTICE_TPL_GALIAS_SET',				'0015');
	define('N2F_NOTICE_TPL_GBINDING_SET',				'0016');

	L('en', 'N2F_ERROR_TPL_EXTENSION_NOT_LOADED',		"Template Extension Failed to Load: '_%1%_'");
	L('en', 'N2F_ERROR_TPL_SIMPLEXML_PARSE',			"Error in SimpleXML parser, aborting current process.  Exception was: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_BASE_SET',					"Template Base Set to: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_MODULE_SET',				"Template Module Set to: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_SKIN_SET',					"Template Skin Set to: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_FILE_SET',					"Template File Set to: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_FIELD_SET',				"Template Field Set: '_%1%_' set to '_%2%_'");
	L('en', 'N2F_NOTICE_TPL_FIELDS_SET',				"Template Fields Set using: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_EXPIRE_SET',				"Template Expire Set to: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_BINDING_SET',				"Template Binding Added: '_%1%_: _%2%_'");
	L('en', 'N2F_NOTICE_TPL_BINDINGS_SET',				"Template Bindings Added: _%1%_");
	L('en', 'N2F_NOTICE_TPL_RENDER',					"Template Render Requested");
	L('en', 'N2F_NOTICE_TPL_FETCH',					"Template Fetch Requested");
	L('en', 'N2F_NOTICE_TPL_DISPLAY',					"Template Display Requested");
	L('en', 'N2F_NOTICE_TPL_GFIELD_SET',				"Template global field set: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_ALIAS_SET',				"Template alias set: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_GALIAS_SET',				"Template global alias set: '_%1%_'");
	L('en', 'N2F_NOTICE_TPL_GBINDING_SET',				"Template global binding set: '_%1%_'");

	L('de', 'N2F_ERROR_TPL_EXTENSION_NOT_LOADED',		"Laden der Template-Erweiterung fehlgeschlagen: '_%1%_'");
	L('de', 'N2F_ERROR_TPL_SIMPLEXML_PARSE',			"Error in SimpleXML parser, aborting current process.  Exception was: '_%1%_'");
	L('de', 'N2F_NOTICE_TPL_BASE_SET', 				"Template-Basis auf '_%1%_' gesetzt");
	L('de', 'N2F_NOTICE_TPL_MODULE_SET',				"Template-Modul auf '_%1%_' gesetzt");
	L('de', 'N2F_NOTICE_TPL_SKIN_SET',					"Template-Skin auf '_%1%_' gesetzt");
	L('de', 'N2F_NOTICE_TPL_FILE_SET',					"Template-Datei auf '_%1%_' gesetzt");
	L('de', 'N2F_NOTICE_TPL_FIELD_SET',				"Template-Feld '_%1%_' auf '_%2%_' gesetzt");
	L('de', 'N2F_NOTICE_TPL_FIELDS_SET',				"Template-Felder gesetzt mit  '_%1%_'");
	L('de', 'N2F_NOTICE_TPL_EXPIRE_SET',				"Template-Frist auf '_%1%_' gesetzt");
	L('de', 'N2F_NOTICE_TPL_BINDING_SET',				"Template Binding Added: '_%1%_: _%2%_'");
	L('de', 'N2F_NOTICE_TPL_BINDINGS_SET',				"Template Bindings Added: _%1%_");
	L('de', 'N2F_NOTICE_TPL_RENDER',					"Rendern von Template angefragt");
	L('de', 'N2F_NOTICE_TPL_FETCH',					"Holen von Template angefragt");
	L('de', 'N2F_NOTICE_TPL_DISPLAY',					"Template Display Requested");
	L('de', 'N2F_NOTICE_TPL_GFIELD_SET',				"Template global field set: '_%1%_'");
	L('de', 'N2F_NOTICE_TPL_ALIAS_SET',				"Template alias set: '_%1%_'");
	L('de', 'N2F_NOTICE_TPL_GALIAS_SET',				"Template global alias set: '_%1%_'");
	L('de', 'N2F_NOTICE_TPL_GBINDING_SET',				"Template global binding set: '_%1%_'");

	L('es', 'N2F_ERROR_TPL_EXTENSION_NOT_LOADED',		"La Extensión de Plantilla Falló al Cargar: '_%1%_'");
	L('es', 'N2F_ERROR_TPL_SIMPLEXML_PARSE',			"Error in SimpleXML parser, aborting current process.  Exception was: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_BASE_SET', 				"Plantilla Base Establecida a: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_MODULE_SET',				"Módulo de Plantilla Establecido a: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_SKIN_SET',					"Tema de la Plantilla Establecido a: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_FILE_SET',					"Archivo de la Plantilla Establecido a: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_FIELD_SET',				"Campo de la Plantilla Establecido: '_%1%_' establecido a '_%2%_'");
	L('es', 'N2F_NOTICE_TPL_FIELDS_SET',				"Campos de la Plantilla Establecidos usando: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_EXPIRE_SET',				"Expiración de la Plantilla Establecida a: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_BINDING_SET',				"Template Binding Added: '_%1%_: _%2%_'");
	L('es', 'N2F_NOTICE_TPL_BINDINGS_SET',				"Template Bindings Added: _%1%_");
	L('es', 'N2F_NOTICE_TPL_RENDER',					"Solicitado el Proceso de la Plantilla.");
	L('es', 'N2F_NOTICE_TPL_FETCH',					"Solicitada la Obtención de la Plantilla.");
	L('es', 'N2F_NOTICE_TPL_DISPLAY',					"Template Display Requested");
	L('es', 'N2F_NOTICE_TPL_GFIELD_SET',				"Template global field set: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_ALIAS_SET',				"Template alias set: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_GALIAS_SET',				"Template global alias set: '_%1%_'");
	L('es', 'N2F_NOTICE_TPL_GBINDING_SET',				"Template global binding set: '_%1%_'");

?>