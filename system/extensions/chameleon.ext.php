<?php

	// Grab configuration file
	$n2f = n2f_cls::getInstance();
	$n2f->loadExtension('chameleon/config');


	// Some random settings
	define('CS_VERSION',					'0.8b');
	define('CS_MAX_CHILD_LOCATIONS',			256);
	define('CS_USER_TIMEOUT',				10800);
	define('CS_USE_PCLZIP',					(class_exists('ZipArchive')) ? false : true);
	define('CS_USING_SSL',					(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? false : true);


	// Event constants
	define('CS_EVENT_MAIL',					'cs_event_mail');
	define('CS_EVENT_USER_INIT',				'cs_event_user_init');
	define('CS_EVENT_USER_LOAD',				'cs_event_user_load');
	define('CS_EVENT_USER_AUTH',				'cs_event_user_auth');
	define('CS_EVENT_USER_LOGIN',				'cs_event_user_login');
	define('CS_EVENT_USER_LOGOUT',			'cs_event_user_logout');
	define('CS_EVENT_USER_CHECKPERM',			'cs_event_user_checkperm');
	define('CS_EVENT_USER_FORGOT_PASSWORD',			'cs_event_user_forgot_password');
	define('CS_EVENT_USER_RESET_PASSWORD',			'cs_event_user_reset_password');
	define('CS_EVENT_USER_FORM',				'cs_event_user_form');
	define('CS_EVENT_USER_FORMLOAD',			'cs_event_user_formload');
	define('CS_EVENT_PRE_RENDER',				'cs_event_pre_render');
	define('CS_EVENT_MENU_RENDER',			'cs_event_menu_render');
	define('CS_EVENT_MENUITEM_RENDER',			'cs_event_menuitem_render');


	// Our default content locations
	define('CS_CONTENT_ADMIN_HEADSCRIPTS',		'admin-headscripts');
	define('CS_CONTENT_ADMIN_HEADSTYLES',		'admin-headstyles');
	define('CS_CONTENT_ADMIN_MAIN',			'admin-main');
	define('CS_CONTENT_ADMIN_SIDE',			'admin-side');
	define('CS_CONTENT_ADMIN_USERFORM',		'admin-userform');
	define('CS_CONTENT_FRONT_FOOTER',			'front-footer');
	define('CS_CONTENT_FRONT_MAIN',			'front-main');
	define('CS_CONTENT_FRONT_SIDEBAR',			'front-sidebar');


	// Our default menu locations
	define('CS_MENU_ADMIN_QUICK',				'admin-quick');
	define('CS_MENU_ADMIN_SIDE',				'admin-side');
	define('CS_MENU_ADMIN_USER',				'admin-user');
	define('CS_MENU_FRONT_NAV',				'front-nav');


	// Our base permissions
	define('CS_PERMS_ACCESS_ADMIN',			'cs_access_admin');
	define('CS_PERMS_MANAGE_USERS',			'cs_manage_users');
	define('CS_PERMS_MANAGE_SKINS',			'cs_manage_skins');
	define('CS_PERMS_MANAGE_PACKAGES',			'cs_manage_packages');
	define('CS_PERMS_SUPER_ADMIN',			'cs_super_admin');


	// Only proceed here if we have our config file
	if ($n2f->hasExtension('chameleon/config')) {
		/**
		 * Main class for the site framework.
		 *
		 */
		class chameleon extends n2f_events {
			/**
			 * Holds the prefix for database tables.
			 *
			 * @var string
			 */
			public $dbPrefix = CS_DATABASE_TABLE_PREFIX;
			/**
			 * Holds the currently active skin.
			 *
			 * @var string
			 */
			public $currentSkin = 'default';
			/**
			 * The currently logged in user.
			 *
			 * @var chameleon_user
			 */
			public $user;
			/**
			 * The internal reference database object.
			 *
			 * @var n2f_database
			 */
			private $db;
			/**
			 * The internal template object.
			 *
			 * @var n2f_template
			 */
			private $tpl;
			/**
			 * The internal cache object.
			 *
			 * @var n2f_cache
			 */
			private $cache;
			/**
			 * List of tables in the database that start with CS_DATABASE_TABLE_PREFIX.
			 *
			 * @var array
			 */
			private $tables = array();
			/**
			 * The internal list of authorized modules.
			 *
			 * @var array
			 */
			private $modules = array();
			/**
			 * The internal package list.
			 *
			 * @var array
			 */
			private $packages = array();
			/**
			 * The internal list of authorized extensions.
			 *
			 * @var array
			 */
			private $extensions = array();
			/**
			 * The internal location list.
			 *
			 * @var array
			 */
			private $locations = array();
			/**
			 * The internal list of menu items.
			 *
			 * @var unknown_type
			 */
			private $menuitems = array();
			/**
			 * Singleton instance.
			 *
			 * @var chameleon
			 */
			private static $instance = null;


			/**
			 * Static method to retrieve singleton instance.
			 *
			 * @return chameleon
			 */
			public static function &getInstance() {
				// Check if the instance hasn't been initialized
				if (self::$instance === null) {
					// Initialize it if not
					self::$instance = new chameleon();
				}

				// Set our return instance to a reference to the instance...
				$instance = &self::$instance;

				// Damn that's a lot of "instance" floating around
				return($instance);
			}

			/**
			 * Static method to load all authorized modules in the system.
			 *
			 */
			public static function _initModules() {
				// Our evil global variables
				$n2f = n2f_cls::getInstance();
				$cs = chameleon::getInstance();

				// Add our default modules
				array_unshift($cs->modules, array('key' => 'cs_main', 'mod' => $n2f->cfg->def_mods->start));
				array_unshift($cs->modules, array('key' => 'cs_main', 'mod' => $n2f->cfg->def_mods->error));

				// Loop through active/allowed extensions
				foreach (array_values($cs->extensions) as $ext) {
					// Check if the package is loaded and if we're loading the base extension
					if (isset($cs->packages[$ext['key']]) && $ext['ext'] == $cs->packages[$ext['key']]['meta']->baseExt) {
						// Get the package data
						$pkg = $cs->packages[$ext['key']];

						// Register the extension with N2F
						$n2f->registerExtension($pkg['meta']->baseExt, $pkg['meta']->name, $pkg['meta']->version, $pkg['meta']->author, $pkg['meta']->url);
					}

					// And load them one by one
					$n2f->loadExtension($ext['ext']);
				}


				// Open the modules directory (from where we are)
				$handle = @opendir('modules');

				// If we have a handle on the situation
				if ($handle !== false) {
					// Read first item from handle
					$dir_item = @readdir($handle);

					// Loop through while the item is valid
					while ($dir_item) {
						// If that is a module
						if (is_dir("modules/{$dir_item}") && $cs->_approvedModule($dir_item)) {
							// Create our extension filename
							$filename = ($n2f->cfg->file_struct == N2F_FS_CURRENT) ? "modules/{$dir_item}/sys.ext.php" : "modules/{$dir_item}/cnf/usr.cnf";

							// Check if the extension exists
							if (file_exists($filename)) {
								// And include the extension
								require($filename);
							}
						}

						// Read the next item in the directory
						$dir_item = @readdir($handle);
					}

					// Done with the main module directory, close the handle for the OS
					@closedir($handle);

					// If we're not in the root level directory
					if ($n2f->cfg->site->url_path != '/') {
						// Open the root level directory's modules directory
						$handle = @opendir(N2F_REL_PATH.'modules');

						// And if we had no problems
						if ($handle !== false) {
							// Read the first item in the root level directory
							$dir_item = @readdir($handle);

							// Loop through while the item is valid
							while ($dir_item) {
								// If that is a module and approved
								if (is_dir(N2F_REL_PATH."modules/{$dir_item}") && $cs->_approvedModule($dir_item, true)) {
									// Create our extension filename
									$filename = ($n2f->cfg->file_struct == N2F_FS_CURRENT) ? N2F_REL_PATH."modules/{$dir_item}/sys.ext.php" : N2F_REL_PATH."modules/{$dir_item}/cnf/usr.cnf";

									// Check if the extension exists
									if (file_exists($filename)) {
										// And include the extension
										require($filename);
									}
								}

								// Read the next item in the directory
								$dir_item = @readdir($handle);
							}

							// Done with the root level directory, close the handle for the OS
							@closedir($handle);
						}
					}
				}
			}

			/**
			 * Static method to load the authorized destination module.
			 *
			 */
			public static function _initModule() {
				$n2f = n2f_cls::getInstance();
				$cs = chameleon::getInstance();

				if (!isset($_REQUEST['nmod'])) {
					$_REQUEST['nmod'] = $n2f->cfg->def_mods->start;
				}

				if (stristr($_REQUEST['nmod'], '/') !== false) {
					$nparts = explode('/', $_REQUEST['nmod']);
					$_REQUEST['nmod'] = $nparts[count($nparts) - 1];
				}

				$goodToGo = false;

				if (!CS_ENABLE_DEV_MODE && N2F_URL_PATH != '/setup/') {
					foreach (array_values($cs->modules) as $mod) {
						$mod = $mod['mod'];
						$cur = substr(N2F_URL_PATH, 1) . $_REQUEST['nmod'];

						if ($cur == $mod) {
							$goodToGo = true;

							break;
						}
					}
				} else {
					$goodToGo = true;
				}

				if (!$goodToGo) {
					$_REQUEST['nmod'] = $n2f->cfg->def_mods->error;
					$_REQUEST['error_code'] = N2F_ERRCODE_MODULE_FAILURE;
				}

				if (!isset($_REQUEST['nret'])) {
					$_REQUEST['nret'] = 'page';
				}

				if ($n2f->cfg->file_struct == N2F_FS_CURRENT) {
					switch ($_REQUEST['nret']) {
						case 'data':
							$filename = 'data.php';
							break;
						case 'page':
						default:
							$filename = 'page.php';
							break;
					}
				} else {
					$filename = 'index.php';
				}

				if (file_exists("modules/{$_REQUEST['nmod']}/mod.ext.php") !== false) {
					require("modules/{$_REQUEST['nmod']}/mod.ext.php");
				}

				if (file_exists("modules/{$_REQUEST['nmod']}/{$filename}") === false) {
					if ($n2f->debug->showLevel(N2F_DEBUG_ERROR)) {
						$n2f->debug->throwError(N2F_ERROR_MODULE_FAILURE, S('N2F_ERROR_MODULE_FAILURE', array($_REQUEST['nmod'])), 'system/extensions/chameleon.ext.php');
					}

					if (file_exists("modules/{$n2f->cfg->def_mods->error}/{$filename}") === false) {
						$n2f->dumpDebug();

						exit;
					} else {
						$_REQUEST['error_code'] = N2F_ERRCODE_MODULE_FAILURE;

						require("modules/{$n2f->cfg->def_mods->error}/{$filename}");
					}
				} else {
					require("modules/{$_REQUEST['nmod']}/{$filename}");
				}

				if (($n2f->cfg->dbg->dump_debug || CS_ENABLE_DEV_MODE) && $_REQUEST['nret'] == 'page') {
					$n2f->dumpDebug();
				}
			}

			/**
			 * Static method to trigger processing our user and all that jazz.
			 *
			 * @return null
			 */
			public static function initUser() {
				$c = chameleon::getInstance();
				$u = $c->hitEvent(CS_EVENT_USER_INIT);
				$c->hitEvent(CS_EVENT_USER_LOAD, array($u));
			}

			/**
			 * Static method to pull the current version of a package.
			 *
			 * @param string $key	Name of package.
			 * @return string		Version number if found.
			 */
			public static function getPackageVersion($key) {
				$package = self::getPackageInfo($key);

				if (count($package) < 1) {
					return('0');
				}

				return($package['version']);
			}

			/**
			 * Static method to pull information on a package.
			 *
			 * @param string $key	Name of package.
			 * @return array		Array of information about package, empty if not found.
			 */
			public static function getPackageInfo($key) {
				$query = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $key));
				$query->execQuery();
				$ret = array();

				if (!$query->isError() && $query->numRows() == 1) {
					$ret = $query->fetchRow();
				}

				return($ret);
			}

			/**
			 * Static method to determine if a package is active.
			 *
			 * @param string $key	Name of package.
			 * @return boolean		Boolean TRUE or FALSE based on package's active status.
			 */
			public static function packageIsActive($key) {
				$query = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $key));
				$query->execQuery();

				if ($query->isError() || $query->numRows() != 1) {
					return(false);
				}

				if ($query->fetchResult(0, 'active') != '1') {
					return(false);
				}

				return(true);
			}

			/**
			 * Callback to handle setting the skin for new template objects.
			 *
			 * @param n2f_template $tpl	n2f_template object that has just been instantiated.
			 */
			public static function templateCreated(n2f_template &$tpl) {
				$tpl->setSkin(chameleon::getInstance()->currentSkin);

				return;
			}

			/**
			 * Static method to retrieve a list of any content locations in a given template file.
			 *
			 * @param $file	String value of filename (.tpl not necessary).
			 * @param $module	Optional string value of module for template file, default module used if not supplied.
			 * @param $skin	Optional string value of skin for template file, default skin used if not supplied.
			 * @param $base	Optional string value of base folder for template file, './' used if not supplied.
			 * @param $isAdmin	Boolean toggle for pulling admin settings instead of front-end.
			 * @return array	Array of any content locations found in template file.
			 */
			public static function getContentLocations($file, $module = null, $skin = null, $base = null, $isAdmin = false) {
				$c = chameleon::getInstance();
				$ret = array();

				$fileloc  = ($base !== null) ? "{$base}/" : "./";
				$fileloc .= ($module !== null) ? "{$module}/" : $c->getSetting(($isAdmin) ? 'cs_admin_defmod_start' : 'cs_front_defmod_start') . "/";
				$fileloc .= ($skin !== null) ? "{$skin}/" : $c->getSetting(($isAdmin) ? 'cs_admin_skin' : 'cs_front_skin') . "/";
				$fileloc .= (substr($file, -4) == '.tpl') ? $file : "{$file}.tpl";

				$contents = file_get_contents($fileloc);

				if ($contents !== false) {
					$match = "#<cs:location(.*?)/>#is";
					$matches = array();

					if (preg_match_all($match, $contents, $matches)) {
						for ($i = 0; $i < count($matches[0]); $i++) {
							$attribs = n2f_template::getTagAttributes('cs:location', $matches[0][$i]);

							$ret[$attribs['key']] = true;
						}
					}
				}

				return($ret);
			}

			/**
			 * Static method to retrieve a list of any menu locations in a given template file.
			 *
			 * @param $file	String value of filename (.tpl not necessary).
			 * @param $module	Optional string value of module for template file, default module used if not supplied.
			 * @param $skin	Optional string value of skin for template file, default skin used if not supplied.
			 * @param $base	Optional string value of base folder for template file, './' used if not supplied.
			 * @param $isAdmin	Boolean toggle for pulling admin settings instead of front-end.
			 * @return array	Array of any content locations found in template file.
			 */
			public static function getMenuLocations($file, $module = null, $skin = null, $base = null, $isAdmin = false) {
				$c = chameleon::getInstance();
				$ret = array();

				$fileloc  = ($base !== null) ? "{$base}/" : "./";
				$fileloc .= ($module !== null) ? "{$module}/" : $c->getSetting(($isAdmin) ? 'cs_admin_defmod_start' : 'cs_front_defmod_start') . "/";
				$fileloc .= ($skin !== null) ? "{$skin}/" : $c->getSetting(($isAdmin) ? 'cs_admin_skin' : 'cs_front_skin') . "/";
				$fileloc .= (substr($file, -4) == '.tpl') ? $file : "{$file}.tpl";

				$contents = file_get_contents($fileloc);

				if ($contents !== false) {
					$match = "#<cs:menu(.*?)/>#is";
					$matches = array();

					if (preg_match_all($match, $contents, $matches)) {
						for ($i = 0; $i < count($matches[0]); $i++) {
							$attribs = n2f_template::getTagAttributes('cs:menu', $matches[0][$i]);

							$ret[$attribs['key']] = true;
						}
					}
				}

				return($ret);
			}

			/**
			 * Method to send mail through a replaceable interface.
			 *
			 * @param string $to
			 * @param string $subject
			 * @param string $message
			 * @param string $additional_headers	Optional string value of additional headers.
			 * @param string $additional_parameters	Optional string value of additional parameters.
			 * @return boolean					Boolean TRUE or FALSE signifying success or failure of sending the email.
			 */
			public static function mail($to, $subject, $message, $additional_headers = null, $additional_parameters = null) {
				return(chameleon::getInstance()->hitEvent(CS_EVENT_MAIL, array($to, $subject, $message, $additional_headers, $additional_parameters)));
			}

			/**
			 * Method to perform authentication through a replaceable interface.
			 *
			 * @param string $package_key			String value of the package key to use for permission validation.
			 * @param string $perm_key			String value of the permission key to use for permission validation.
			 * @param string $login_redir			String value to use for login redirection (if there is no logged in user).
			 * @param string $insufficient_redir	String value to use for insufficient permission redirection (if the logged in user can't access this section).
			 * @param string $return_redir		String value to use for return-trip redirection (mostly for logins).
			 * @param string $login_msg			Optional string to use for login redirects (default is 'You must log in to view this page.').
			 * @param string $insufficient_msg		Optional string to use for insufficient permission redirects (default is 'You do not have permission to view this page.').
			 */
			public static function doAuth($package_key, $perm_key, $login_redir, $insufficient_redir, $return_redir, $login_msg = null, $insufficient_msg = null) {
				chameleon::getInstance()->hitEvent(CS_EVENT_USER_AUTH, array($package_key, $perm_key, $login_redir, $insufficient_redir, $return_redir, $login_msg, $insufficient_msg));

				return;
			}

			/**
			 * Method to perform login through a replaceable interface.
			 *
			 * @param string $username	String value of user's username value.
			 * @param string $password	String value of user's password value.
			 * @param boolean $rememberMe	Boolean TRUE or FALSE determining if user should be remembered or not.
			 * @return n2f_return		n2f_return object containing success/failure status and any extra data.
			 */
			public static function doLogin($username, $password, $rememberMe = false) {
				return(chameleon::getInstance()->hitEvent(CS_EVENT_USER_LOGIN, array($username, $password, $rememberMe)));
			}

			/**
			 * Method to perform password reset through a replaceable interface.
			 *
			 * @param string $username	String value of user's username value.
			 * @return n2f_return		n2f_return object containing success/failure status and any extra data.
			 */
			public static function doForgotPassword($username) {
				return(chameleon::getInstance()->hitEvent(CS_EVENT_USER_FORGOT_PASSWORD, array($username)));
			}

			/**
			 * Method to perform password reset through a replaceable interface.
			 *
			 * @param chameleon_user $user       The username of the user
			 * @param string         $password   The new password
			 * @return n2f_return
			 */
			public static function doPasswordReset($user, $password) {
				return(chameleon::getInstance()->hitEvent(CS_EVENT_USER_RESET_PASSWORD, array($user, $password)));
			}

			/**
			 * Method to perform logout through a replaceable interface.
			 *
			 */
			public static function doLogout() {
				chameleon::getInstance()->hitEvent(CS_EVENT_USER_LOGOUT);

				return;
			}

			/**
			 * Method to check user permissions.
			 *
			 * @param string $package_key		Key for package which owns the permission.
			 * @param string $perm_key		Key for the permission.
			 * @param boolean $check_super	Boolean value to turn off using CS_PERMS_SUPER_ADMIN check.
			 * @return boolean				Boolean TRUE or FALSE based on the user having the permission.
			 */
			public static function checkUserPerm($package_key, $perm_key, $check_super = true) {
				return(chameleon::getInstance()->hitEvent(CS_EVENT_USER_CHECKPERM, array($package_key, $perm_key, $check_super)));
			}

			/**
			 * Method to notify listener events of a form modification.
			 *
			 * @param chameleon_user $user	chameleon_user object containing base user information.
			 * @param array $request			Array of variables sent with page request.
			 */
			public static function doUserForm(chameleon_user $user, array $request) {
				return(chameleon::getInstance()->hitEvent(CS_EVENT_USER_FORM, array($user, $request)));
			}

			/**
			 * Method to notify listener events of a form load.
			 *
			 * @param chameleon_user $user	chameleon_user object with base user information.
			 */
			public static function doUserFormLoad(chameleon_user $user) {
				return(chameleon::getInstance()->hitEvent(CS_EVENT_USER_FORMLOAD, array($user)));
			}


			/**
			 * Method to instantiate a new chameleon object.
			 *
			 */
			public function __construct() {
				parent::__construct();
				$n2f = n2f_cls::getInstance();

				// setup our events
				$this->addEvent(CS_EVENT_MAIL, true);
				$this->addEvent(CS_EVENT_USER_INIT, true);
				$this->addEvent(CS_EVENT_USER_AUTH, true);
				$this->addEvent(CS_EVENT_USER_LOGIN, true);
				$this->addEvent(CS_EVENT_USER_LOGOUT, true);
				$this->addEvent(CS_EVENT_USER_CHECKPERM, true);
				$this->addEvent(CS_EVENT_USER_FORGOT_PASSWORD, true);
				$this->addEvent(CS_EVENT_USER_RESET_PASSWORD, true);
				$this->addEvent(CS_EVENT_USER_LOAD);
				$this->addEvent(CS_EVENT_USER_FORM);
				$this->addEvent(CS_EVENT_USER_FORMLOAD);
				$this->addEvent(CS_EVENT_PRE_RENDER);
				$this->addEvent(CS_EVENT_MENU_RENDER);
				$this->addEvent(CS_EVENT_MENUITEM_RENDER);

				// add mail event default
				$this->hookEvent(CS_EVENT_MAIL, array($this, '_mail'));
				$this->hookEvent(CS_EVENT_USER_INIT, array($this, '_initUser'));
				$this->hookEvent(CS_EVENT_USER_AUTH, 'doAuth');
				$this->hookEvent(CS_EVENT_USER_LOGIN, 'doLogin');
				$this->hookEvent(CS_EVENT_USER_LOGOUT, 'doLogout');
				$this->hookEvent(CS_EVENT_USER_CHECKPERM, 'checkUserPerm');
				$this->hookEvent(CS_EVENT_USER_FORGOT_PASSWORD, 'doForgotPassword');
				$this->hookEvent(CS_EVENT_USER_RESET_PASSWORD, 'doPasswordReset');

				// reinitialize the database connection
				$this->db = n2f_database::setInstance(n2f_cls::getInstance(), 'mysqli', false, null, new n2f_cfg_db(array('type' => CS_DATABASE_TYPE, 'host' => CS_DATABASE_HOST, 'port' => CS_DATABASE_PORT, 'name' => CS_DATABASE_NAME, 'user' => CS_DATABASE_USER, 'pass' => CS_DATABASE_PASS)));
				$this->db->open();

				if (!$this->db->isOpen()) {
					echo("Fatal Error: Chameleon couldn't connect to the database.  Check configuration values.");

					if (CS_ENABLE_DEV_MODE) {
						$n2f->dumpDebug();
					}

					exit;
				}

				$tables = array();
				$tQuery = $this->db->storedQuery(CS_SQL_CHECK_TABLES);
				$tQuery->execQuery();

				if ($tQuery->isError()) {
					echo("Fatal Error: Failed to check for tables.  Check configuration values.");

					if (CS_ENABLE_DEV_MODE) {
						echo("<div>Error Information:</div>");
						debugEcho($tQuery->fetchErrors());
					}

					exit;
				}

				if ($tQuery->numRows() > 0) {
					foreach (array_values($tQuery->fetchRows()) as $row) {
						foreach (array_values($row) as $table) {
							$tables[$table] = true;
						}
					}

					$this->tables = $tables;
				}

				// Check for database tables
				if (stripos($_SERVER['REQUEST_URI'], 'setup/') === false && (!isset($tables[CS_DATABASE_TABLE_PREFIX . 'cs_packages']) || !isset($tables[CS_DATABASE_TABLE_PREFIX . 'cs_skins'])
						|| !isset($tables[CS_DATABASE_TABLE_PREFIX . 'cs_settings']) || !isset($tables[CS_DATABASE_TABLE_PREFIX . 'cs_users'])
						|| !isset($tables[CS_DATABASE_TABLE_PREFIX . 'cs_userperms']) || !isset($tables[CS_DATABASE_TABLE_PREFIX . 'cs_perms']) || $this->getSetting('cs_installed') === null)) {
					echo("Fatal Error: Database tables are not installed.  Please try re-installing.");

					exit;
				}

				// Assign default modules
				if (N2F_URL_PATH == '/admin/') {
					$settings = $this->getSettings(array('cs_admin_defmod_start', 'cs_admin_defmod_error'));

					if (is_dir("modules/" . str_replace('admin/', '', $settings['cs_admin_defmod_start']) . "/") && file_exists("modules/" . str_replace('admin/', '', $settings['cs_admin_defmod_start']) . "/page.php")) {
						$n2f->cfg->def_mods->start = $settings['cs_admin_defmod_start'];
					}

					if (is_dir("modules/" . str_replace('admin/', '', $settings['cs_admin_defmod_error']) . "/") && file_exists("modules/" . str_replace('admin/', '', $settings['cs_admin_defmod_error']) . "/page.php")) {
						$n2f->cfg->def_mods->error = $settings['cs_admin_defmod_error'];
					}
				} else if (N2F_URL_PATH == '/') {
					$settings = $this->getSettings(array('cs_front_defmod_start', 'cs_front_defmod_error'));

					if (is_dir("modules/{$settings['cs_front_defmod_start']}/") && file_exists("modules/{$settings['cs_front_defmod_start']}/page.php")) {
						$n2f->cfg->def_mods->start = $settings['cs_front_defmod_start'];
					}

					if (is_dir("modules/{$settings['cs_front_defmod_error']}/") && file_exists("modules/{$settings['cs_front_defmod_error']}/page.php")) {
						$n2f->cfg->def_mods->error = $settings['cs_front_defmod_error'];
					}
				}

				// Update some low-level site config values
				$n2f->cfg->site->domain = ltrim(CS_COOKIE_DOMAIN, '.');
				$n2f->cfg->site->title = CS_SITE_PATH;

				// Register our tags
				n2f_template::setGlobalTag('cs:location', array($this, 'processLocations'));
				n2f_template::setGlobalTag('cs:menu', array($this, 'processMenus'));

				// Initialize our helper objects
				$this->tpl = new n2f_template('static');
				$this->cache = new n2f_cache(CS_CACHE_EXPIRATION, 'cs', false);

				// Assign current skin
				if (N2F_URL_PATH == '/admin/') {
					$this->currentSkin = $this->getSetting('cs_admin_skin');
				} else if (N2F_URL_PATH == '/') {
					$this->currentSkin = $this->getSetting('cs_front_skin');
				}

				// Register template_created callback and set current skin on our internal template
				$n2f->hookEvent(N2F_TPLEVT_CREATED, array('chameleon', 'templateCreated'));
				$this->tpl->setSkin($this->currentSkin);

				if (defined('FORCE_RECACHE')) {
					$this->reloadApproveds(true);
				} else {
					$this->reloadApproveds((CS_ENABLE_DEV_MODE) ? true : false);
				}

				// And account for safemode
				if (isset($_REQUEST['safemode']) && $_REQUEST['safemode'] == true) {
					foreach (array_keys($this->packages) as $key) {
						if ($key != 'cs_packages') {
							unset($this->packages[$key]);
						}
					}

					$this->modules = array(
						array('key' => 'cs_main', 'mod' => 'admin/csPackages'),
						array('key' => 'cs_main', 'mod' => 'admin/csSkins'),
						array('key' => 'cs_main', 'mod' => 'admin/csUsers')
					);
					$this->extensions = array();
				}
			}

			/**
			 * Method to set the current layout module.
			 *
			 * @param string $module	Name of the module to assign.
			 * @return chameleon	chameleon object for chaining.
			 */
			public function setLayoutModule($module) {
				$this->tpl->setModule($module);

				return($this);
			}

			/**
			 * Method to set the current layout file.
			 *
			 * @param string $file	Name of the file to assign.
			 * @return chameleon	chameleon object for chaining.
			 */
			public function setLayoutFile($file) {
				$this->tpl->setFile($file);

				return($this);
			}

			/**
			 * Method to set the current layout skin.
			 *
			 * @param string $skin	Name of the skin to assign.
			 * @return chameleon	chameleon object for chaining.
			 */
			public function setLayoutSkin($skin) {
				$this->tpl->setSkin($skin);

				return($this);
			}

			/**
			 * Method to set the current layout base.
			 *
			 * @param string $base	Base directory to assign.
			 * @return chameleon	chameleon object for chaining.
			 */
			public function setLayoutBase($base) {
				$this->tpl->setBase($base);

				return($this);
			}

			/**
			 * Method to add a field to the system.
			 *
			 * @param string $name	Name of the field being set.
			 * @param mixed $value	Value of the field being set.
			 * @return chameleon	chameleon object for chaining.
			 */
			public function setField($name, $value) {
				n2f_template::setGlobalField($name, $value);

				return($this);
			}

			/**
			 * Set fields in the template (note: $fields needs to be an associative array of fieldname=>fieldvalue pairs).
			 *
			 * @param array $fields	Array of fields being set in the template.
			 * @return chameleon	chameleon object for chaining.
			 */
			public function setFields(array $fields) {
				n2f_template::setGlobalFields($fields);

				return($this);
			}

			/**
			 * Method to register content with a location.
			 *
			 * @param string $locationKey	Location key name.
			 * @param string $content	Content to register with location.
			 * @param integer $pos		Optional desired position for content.
			 * @return chameleon		chameleon object for chaining.
			 */
			public function registerContent($locationKey, $content, $pos = null) {
				if (empty($content) || is_array($content)) {
					return($this);
				}

				if (!is_array($this->locations)) {
					$this->locations = array($locationKey => new chameleon_location($locationKey));
					$this->locations[$locationKey]->addContent($content, $pos);
				} else if (isset($this->locations[$locationKey])) {
					$this->locations[$locationKey]->addContent($content, $pos);
				} else {
					$this->locations[$locationKey] = new chameleon_location($locationKey);
					$this->locations[$locationKey]->addContent($content, $pos);
				}

				return($this);
			}

			/**
			 * Method to add a menu item to the stack.
			 *
			 * @param string $location		String value for location name.
			 * @param chameleon_menuitem $item	Menu item to add to stack.
			 */
			public function registerMenuItem($location, chameleon_menuitem $item) {
				if (!isset($this->menuitems[$location])) {
					$this->menuitems[$location] = array();
				}

				$pos = $item->position;

				if ($pos !== null) {
					$pos = intval($pos);

					if ($pos < 0) {
						array_unshift($this->menuitems[$location], $item);
					} else {
						if (isset($this->menuitems[$location][$pos])) {
							$last = '';
							$next = '';
							$len = count($this->menuitems[$location]) + 1;

							for ($i = $pos; $i < $len; $i++) {
								if (!isset($this->menuitems[$location][$i])) {
									array_push($this->menuitems[$location], $last);

									break;
								}

								$next = $this->menuitems[$location][$i];
								$this->menuitems[$location][$i] = $last;
								$last = $next;
							}

							$this->menuitems[$location][$pos] = $item;
						} else {
							for ($i = (count($this->menuitems[$location]) - 1); $i < $pos; $i++) {
								$this->menuitems[$location][$i] = '';
							}

							$this->menuitems[$location][$pos] = $item;
						}
					}

					return;
				}

				array_push($this->menuitems[$location], $item);

				return;
			}

			/**
			 * Method to render system contents to the end-user.
			 *
			 */
			public function render() {
				$n2f = n2f_cls::getInstance();
				$this->hitEvent(CS_EVENT_PRE_RENDER, array($this, &$this->locations));
				$defmod_users = $this->getSetting('cs_admin_defmod_users');

				// Admin user menu
				$this->registerMenuItem(CS_MENU_ADMIN_USER, new chameleon_menuitem(CS_MENU_ADMIN_USER, null, 'logout', './?nmod=main&logout=true', 'Logout'));
				$this->registerMenuItem(CS_MENU_ADMIN_USER, new chameleon_menuitem(CS_MENU_ADMIN_USER, null, 'visitsite', '../', 'Visit Site', '_blank'));

				// Admin top menu
				$cham_menu_top = new chameleon_menuitem(CS_MENU_ADMIN_QUICK, -1, 'home', './?page=main', 'Chameleon', null, ' title="Chameleon Home"');
				$cham_menu_top->addSubMenu(0, 'main', './?page=main', 'Admin Home');
				$cham_menu_top->addSubMenu(1, 'packagemgr', './?nmod=csPackages', 'Package Manager');
				$cham_menu_top->addSubMenu(2, 'skinmgr', './?nmod=csSkins', 'Skin/Module Manager');
				$cham_menu_top->addSubMenu(3, 'usermgr', './?nmod=' . $defmod_users, 'User Manager');
				$cham_menu_top->icon = array('src' => "resources/images/icons/application_home.png", 'alt' => 'Chameleon');
				$this->registerMenuItem(CS_MENU_ADMIN_QUICK, $cham_menu_top);

				// Admin side menu
				$cham_menu_side = new chameleon_menuitem(CS_MENU_ADMIN_SIDE, -1, 'chameleon', '#chameleon', 'Chameleon');

				if (!isset($_REQUEST['nmod']) || $_REQUEST['nmod'] == 'main' || $_REQUEST['nmod'] == 'error' || $_REQUEST['nmod'] == 'csPackages' || $_REQUEST['nmod'] == 'csSkins' || $_REQUEST['nmod'] == 'csUsers') {
					$cham_menu_side->active = true;
				}

				$cham_menu_side->addSubMenu(0, 'main', './?page=main', 'Admin Home');
				$cham_menu_side->addSubMenu(0, 'packagemgr', './?nmod=csPackages', 'Package Manager');
				$cham_menu_side->addSubMenu(0, 'skinmgr', './?nmod=csSkins', 'Skin/Module Manager');
				$cham_menu_side->addSubMenu(0, 'usermgr', './?nmod=' . $defmod_users, 'User Manager');
				$cham_menu_side->addSubMenu(0, 'front', '../', 'Front-End', '_blank');
				$this->registerMenuItem(CS_MENU_ADMIN_SIDE, $cham_menu_side);

				// Front-end menu items
				$this->registerMenuItem(CS_MENU_FRONT_NAV, new chameleon_menuitem('nav', 0, 'home', './', 'Home', null, null, null, ((!isset($_REQUEST['nmod']) || $_REQUEST['nmod'] == $n2f->cfg->def_mods->start) ? true : false), null, null));

				$this->tpl->render();
				$this->tpl->display();

				return;
			}

			/**
			 * Method to install a package from administration.  Wouldn't call this yourself unless you're just -that- badass.
			 *
			 * @param string $archivePath	String value of archive ZIP filename.
			 * @param boolean $activate	Boolean TRUE or FALSE determining if package should be activated.
			 * @return n2f_return		n2f_return object with state information.
			 */
			public function installPackage($archivePath, $activate = false) {
				$ret = new n2f_return();
				$ret->isGood();

				$archive = null;
				$archiveFileCount = 0;
				$archiveContents = array();

				if (CS_USE_PCLZIP) {
					n2f_cls::getInstance()->loadExtension('chameleon/pclzip');

					$archive = new PclZip($archivePath);
					$contents = $archive->listContent();
					$archiveFileCount = count($contents);

					foreach (array_values($contents) as $file) {
						$archiveContents[$file['filename']] = true;
					}
				} else {
					$archive = new ZipArchive();
					$archive->open($archivePath);
					$archiveFileCount = $archive->numFiles;

					for ($i = 0; $i < $archiveFileCount; $i++) {
						$archiveContents[$archive->getNameIndex($i)] = true;
					}
				}

				$ini = null;
				$conf = null;
				$tmpini = null;
				$tmpname = 'resources/upload_tmp/package_' . md5(time() + mt_rand(1, 25)) . '.ini';

				// get and parse ini file
				if (CS_USE_PCLZIP) {
					$tmp = $archive->extract(PCLZIP_OPT_BY_NAME, 'package.ini', PCLZIP_OPT_EXTRACT_AS_STRING);

					if ($tmp == 0) {
						$ret->isFail();
						$ret->addMsg("Failed to locate package.ini, invalid package.");

						return($ret);
					}

					$tmpini = $tmp[0]['content'];
				} else {
					$tmpini = $archive->getFromName('package.ini');

					if ($tmpini === false || empty($tmpini)) {
						$ret->isFail();
						$ret->addMsg("Failed to locate package.ini, invalid package.");

						return($ret);
					}
				}

				if (@file_put_contents($tmpname, $tmpini) === false) {
					$ret->isFail();
					$ret->addMsg("Failed to perform temp-write with package.ini.");

					return($ret);
				}

				$ini = @parse_ini_file($tmpname, true);
				unlink($tmpname);
				$conf = $ini['Package'];

				if ($ini === false || !is_array($ini) || !isset($ini['Package']) || !isset($ini['Package']['key']) || !isset($ini['Package']['name']) || !isset($ini['Package']['author']) || !isset($ini['Package']['description']) || !isset($ini['Package']['url']) || !isset($ini['Package']['version'])) {
					$ret->isFail();
					$ret->addMsg("Failed to parse package.ini or package.ini was incomplete.");

					return($ret);
				}

				// check for dependencies (in [Dependencies] section of package.ini with pkg_key[] = "" values)
				$depends = (isset($ini['Dependencies'])) ? $ini['Dependencies'] : array();
				$hasDepends = true;

				if (count($depends) == 1) {
					foreach (array_values($depends['pkg_key']) as $key) {
						if (!$this->packageIsActive($key)) {
							$hasDepends = false;

							break;
						}
					}
				}

				if ($hasDepends === false) {
					$ret->isFail();
					$ret->addMsg("Missing dependencies, please check package documentation.");

					return($ret);
				}

				$wasExisting = false;
				$existing = array();

				// search for pre-existing copy of package
				// do version check for simple failure
				$query = $this->db->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $conf['key']))->execQuery();

				if ($query->isError()) {
					$ret->isFail();
					$ret->addMsg("Failed to check for pre-existing version of package, fatal error.");

					return($ret);
				}

				// if existing package but new version...
				// unserialize owned files and permissions
				// for comparison
				if ($query->numRows() > 0) {
					$existing = $query->fetchRow();

					if (version_compare($conf['version'], $existing['version'], '<=')) {
						$ret->isFail();
						$ret->addMsg("A newer or identical version of the {$conf['name']} package is already installed.  Upgrades can only be performed with newer versions.");

						return($ret);
					}

					$wasExisting = true;
					$existing['perms'] = array();

					$pQuery = $this->db->storedQuery(CS_SQL_SELECT_ALL_PERMS_BY_PKGID, array('packageId' => $existing['packageId']))->execQuery();

					if ($pQuery->isError()) {
						$ret->isFail();
						$ret->addMsg("Failed to check for package permissions, fatal error.");

						return($ret);
					}

					if ($pQuery->numRows() > 0) {
						$existing['perms'] = $pQuery->fetchRows();
					}

					if ($existing['files'] != '') {
						$existing['files'] = unserialize($existing['files']);
					} else {
						$existing['files'] = array();
					}
				}

				$new = array(
					'modules'		=> array(),
					'extensions'	=> array(),
					'files'		=> array()
				);

				// loop through archive contents and compare
				$conflicting = array();
				foreach (array_keys($archiveContents) as $fname) {
					if ($fname == 'package.ini') {
						continue;
					}

					if (substr($fname, -1) == '/') {
						if (isset($GLOBALS['directories'][$fname])) {
							continue;
						}

						if (isset($existing['files'][$fname])) {
							unset($existing['files'][$fname]);
						}

						$new['files'][$fname] = true;

						continue;
					}

					if (file_exists(N2F_REL_PATH . $fname) && (!$wasExisting || !isset($existing['files'][$fname]))) {
						$conflicting[] = $fname;
					}

					$new['files'][$fname] = true;

					$fparts = explode('/', $fname);
					$fpartsLen = count($fparts);

					if ($fparts[$fpartsLen - 1] == 'page.php' && $fparts[$fpartsLen - 3] == 'modules') {
						$new['modules'][] = str_replace(array('modules/', '/page.php'), array('', ''), $fname);
					} else if (substr($fname, -8) == '.ext.php' && $fpartsLen == 3 && $fparts[0] == 'system' && $fparts[1] == 'extensions') {
						$new['extensions'][] = substr($fparts[2], 0, -8);
					}

					if (isset($existing['files'][$fname])) {
						unset($existing['files'][$fname]);
					}
				}

				// If file conflicts were found, report them and fail
				if (count($conflicting) > 0) {
					$ret->isFail();
					$ret->addMsg("There are one or more file conflicts with the {$conf['name']} package, please contact the package author.");
					foreach ($conflicting as $conflict ) {
						$ret->addMsg("File conflict: {$conflict}");
					}
					unset($conflicting);
					return($ret);
				}

				// make sure our list of 'new' files is complete
				if ($wasExisting && count($existing['files']) > 0) {
					foreach (array_keys($existing['files']) as $efile) {
						if (!isset($new['files'][$efile])) {
							$new['files'][$efile] = true;
						}
					}
				}

				// do skin check/update/insert if needed
				if (isset($conf['skin']) && !empty($conf['skin'])) {
					$query = $this->db->storedQuery(CS_SQL_SELECT_SKIN_BY_SKIN, array('skin' => $conf['skin']))->execQuery();

					if ($query->isError()) {
						$ret->isFail();
						$ret->addMsg("Failed to check skin installation, fatal error.");

						return($ret);
					} else if ($query->numRows() > 0 && $query->fetchResult(0, 'key') != $conf['key']) {
						$ret->isFail();
						$ret->addMsg("The package skin is already installed on this sytem with another package.");

						return($ret);
					} else if ($query->numRows() < 1) {
						$query = $this->db->storedQuery(CS_SQL_INSERT_SKIN, array('key' => $conf['key'], 'skin' => $conf['skin']))->execQuery();

						if ($query->isError()) {
							$ret->isFail();
							$ret->addMsg("Failed to install skin information, fatal error.");

							return($ret);
						}
					}
				}

				// extract files from archive to destinations
				foreach (array_keys($archiveContents) as $fname) {
					if ($fname == 'package.ini') {
						continue;
					}

					if (CS_USE_PCLZIP) {
						$archive->extract(PCLZIP_OPT_PATH, N2F_REL_PATH, PCLZIP_OPT_BY_NAME, $fname);
					} else {
						$archive->extractTo(N2F_REL_PATH, $fname);
					}
				}

				// Get optional settings
				$conf['baseExt'] = (isset($conf['baseExt'])) ? $conf['baseExt'] : '';
				$conf['startMod'] = (isset($conf['startMod'])) ? $conf['startMod'] : '';
				$conf['errorMod'] = (isset($conf['errorMod'])) ? $conf['errorMod'] : '';

				// update/insert package info
				if ($wasExisting) {
					$query = $this->db->storedQuery(CS_SQL_UPDATE_PACKAGE, array(
						'key' => $conf['key'],
						'modules'	=> serialize($new['modules']),
						'extensions' => serialize($new['extensions']),
						'files' => serialize($new['files']),
						'baseExt' => $conf['baseExt'],
						'startMod' => $conf['startMod'],
						'errorMod' => $conf['errorMod'],
						'name' => $conf['name'],
						'author' => $conf['author'],
						'description' => $conf['description'],
						'url' => $conf['url'],
						'version' => $conf['version'],
						'upgradeFrom' => $existing['version'],
						'active' => ($activate || $existing['active'] == 1) ? 1 : 0,
						'packageId' => $existing['packageId']
					))->execQuery();

					if ($query->isError()) {
						$ret->isFail();
						$ret->addMsg("Failed to update meta information, package may be broken.");

						return($ret);
					}

					if (count($ini['Permissions']) > 0) {
						if (count($existing['perms']) > 0) {
							foreach (array_values($existing['perms']) as $perm) {
								if (isset($ini['Permissions'][$perm['key']])) {
									unset($ini['Permissions'][$perm['key']]);
								}
							}
						}

						foreach ($ini['Permissions'] as $key => $label) {
							$query = $this->db->storedQuery(CS_SQL_INSERT_PERM, array(
								'packageId' => $existing['packageId'],
								'key' => $key,
								'label' => $label
							))->execQuery();

							if ($query->isError()) {
								$ret->isFail();
								$ret->addMsg("Failed to add '{$key}' permission to system, package may be broken.");

								return($ret);
							}
						}
					}

					if ($conf['baseExt'] != '' && ($activate || $existing['active'] == 1)) {
						n2f_cls::getInstance()->loadExtension($conf['baseExt']);
						$recvr = "{$conf['key']}_receiver";

						if (class_exists($recvr) && is_subclass_of($recvr, 'chameleon_receiver')) {
							$cls = new $recvr();
							$cls->upgrade($this, $existing['version']);
						}
					}
				} else {
					$query = $this->db->storedQuery(CS_SQL_INSERT_PACKAGE, array(
						'key' => $conf['key'],
						'modules'	=> serialize($new['modules']),
						'extensions' => serialize($new['extensions']),
						'files' => serialize($new['files']),
						'baseExt' => $conf['baseExt'],
						'startMod' => $conf['startMod'],
						'errorMod' => $conf['errorMod'],
						'name' => $conf['name'],
						'author' => $conf['author'],
						'description' => $conf['description'],
						'url' => $conf['url'],
						'version' => $conf['version'],
						'installed' => date('Y-m-d G:i:s'),
						'upgradeFrom' => '0',
						'active' => ($activate) ? 1 : 0
					))->execQuery();

					if ($query->isError()) {
						$ret->isFail();
						$ret->addMsg("Failed to insert information into database, package installation reverted.");

						foreach (array_keys($new['files']) as $file) {
							if (substr($file, -1) == '/') {
								continue;
							}

							@unlink(N2F_REL_PATH . $file);
							unset($new['files'][$file]);
						}

						foreach (array_keys(array_reverse($new['files'])) as $file) {
							if (substr($file, -1) != '/') {
								continue;
							}

							if (isDirEmpty(N2F_REL_PATH . $file)) {
								@rmdir(N2F_REL_PATH . $file);
							}
						}

						return($ret);
					}

					$packageId = $query->fetchInc();

					if (count($ini['Permissions']) > 0) {
						foreach ($ini['Permissions'] as $key => $label) {
							$query = $this->db->storedQuery(CS_SQL_INSERT_PERM, array(
								'packageId' => $packageId,
								'key' => $key,
								'label' => $label
							))->execQuery();

							if ($query->isError()) {
								$ret->isFail();
								$ret->addMsg("Failed to add '{$key}' permission to system, package may be broken.");

								return($ret);
							}
						}
					}

					if ($conf['baseExt'] != '' && ($activate || $existing['active'] == 1)) {
						n2f_cls::getInstance()->loadExtension($conf['baseExt']);
						$recvr = "{$conf['key']}_receiver";

						if (class_exists($recvr) && is_subclass_of($recvr, 'chameleon_receiver')) {
							$cls = new $recvr();
							$cls->activate($this);
						}
					}
				}

				$this->reloadApproveds(true);

				return($ret);
			}

			/**
			 * Method to update a package's active status in the database/system.
			 *
			 * @param string $key		Name of package.
			 * @param boolean $active	Boolean TRUE or FALSE for toggling the active status of the package.
			 * @return boolean			Boolean TRUE or FALSE based on the update process.
			 */
			public function togglePackageActivation($key, $active) {
				if (!isset($this->packages[$key]) && !isset($_REQUEST['safemode'])) {
					return(false);
				}

				if (isset($_REQUEST['safemode'])) {
					$res = $this->db->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $key));
					$res->execQuery();

					if ($res->isError() || $res->numRows() < 1) {
						return(false);
					}

					$packageId = $res->fetchResult(0, 'packageId');
				} else {
					$packageId = $this->packages[$key]['packageId'];
				}

				$update = $this->db->storedQuery(CS_SQL_TOGGLE_PACKAGE_ACTIVE, array('active' => (($active) ? 1 : 0), 'packageId' => $packageId));
				$update->execQuery();

				if ($update->isError()) {
					return(false);
				}

				if (!$active) {
					$res = $this->db->storedQuery(CS_SQL_SELECT_SKIN_BY_KEY, array('key' => $key));
					$res->execQuery();

					if (!$res->isError() && $res->numRows() > 0) {
						if ($res->fetchResult(0, 'skin') == $this->getSetting('cs_front_skin')) {
							$this->putSetting('cs_front_skin', 'default');
						}
					}
				}

				$this->reloadApproveds(true);

				return(true);
			}

			/**
			 * Method to get a set of package information from the database.
			 *
			 * @param string $orderBy		String value of order-by column.
			 * @param string $orderDir		String value of order-by direction.
			 * @param string $search			Optional string value to use with query.
			 * @param integer $startRowIndex	Optional integer value for starting row offset returned.
			 * @param integer $maximumRows	Optional integer value for maximum rows returned.
			 * @return array				Array of resulting packages from database, empty array upon failure.
			 */
			public function selectPackages($orderBy, $orderDir, $search = null, $startRowIndex = -1, $maximumRows = -1) {
				$query = CS_SQL_SELECT_ALL_PACKAGES_QUERY;
				$params = null;
				$replacements = array(
					'ORDER_BY' => $orderBy,
					'ORDER_DIR' => $orderDir
				);

				if ($search !== null) {
					$search = "%{$search}%";

					$query = CS_SQL_SELECT_PACKAGE_SEARCH;
					$params['key'] = $search;
					$params['name'] = $search;
					$params['author'] = $search;
					$params['description'] = $search;

					if ($startRowIndex > -1 && $maximumRows > 0) {
						$query = CS_SQL_SELECT_PACKAGE_SEARCH_LIMIT;
						$replacements['OFFSET'] = $startRowIndex;
						$replacements['LIMIT'] = $maximumRows;
					}
				} else {
					if ($startRowIndex > -1 && $maximumRows > 0) {
						$query = CS_SQL_SELECT_ALL_PACKAGES_LIMIT;
						$replacements['OFFSET'] = $startRowIndex;
						$replacements['LIMIT'] = $maximumRows;
					}
				}

				$res = $this->db->storedQuery($query, $params, $replacements);
				$res->execQuery();

				if ($res->isError() || $res->numRows() < 1) {
					return(null);
				} else {
					return($res->fetchRows());
				}
			}

			/**
			 * Method to get the number of packages in the database with an optional search parameter.
			 *
			 * @param string $search	Optional search string to use with query.
			 * @return integer		Number of packages in database, 0 upon failure.
			 */
			public function selectPackageCount($search = null) {
				$query = CS_SQL_SELECT_ALL_PACKAGES_COUNT;
				$params = array();

				if ($search !== null) {
					$search = "%{$search}%";

					$query = CS_SQL_SELECT_PACKAGE_SEARCH_COUNT;
					$params['key'] = $search;
					$params['name'] = $search;
					$params['author'] = $search;
					$params['description'] = $search;
				}

				$res = $this->db->storedQuery($query, $params);
				$res->execQuery();

				if ($res->isError() || $res->numRows() < 1) {
					return(0);
				}

				return($res->fetchResult(0, 'COUNT(*)'));
			}

			/**
			 * Method to remove a package from the system.  Does not perform any operation other than those associated with tracking a package.
			 *
			 * @param string $key	Name of package. (No spaces)
			 * @return boolean		Boolean true or false based on package's removal.
			 */
			public function removePackage($key) {
				$ret = new n2f_return();
				$ret->isGood();

				if (!isset($this->packages[$key]) && !isset($_REQUEST['safemode'])) {
					$ret->isFail();
					$ret->addMsg('Invalid package identifier supplied.');
				} else {
					$package = array();

					if (isset($_REQUEST['safemode'])) {
						$res = $this->db->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $key));
						$res->execQuery();

						if ($res->isError() || $res->numRows() < 1) {
							$ret->isFail();
							$ret->addMsg("Failed to get package information.");
						}

						$package = $res->fetchRow();
						$package['files'] = unserialize($package['files']);
					} else {
						$package = $this->packages[$key];
						$package['files'] = $package['meta']->files;
					}

					if (IsSuccess($ret)) {
						$delete = $this->db->storedQuery(CS_SQL_DELETE_PACKAGE, array('packageId' => intval($package['packageId'])));
						$delete->execQuery();

						if ($delete->isError()) {
							$ret->isFail();
							$ret->addMsg('Failed to delete package information from database.');
						} else {
							$res = $this->db->storedQuery(CS_SQL_SELECT_SKIN_BY_KEY, array('key' => $key));
							$res->execQuery();

							if (!$res->isError() && $res->numRows() > 0) {
								if ($res->fetchResult(0, 'skin') == $this->getSetting('cs_front_skin')) {
									$this->putSetting('cs_front_skin', 'default');
								}

								$del = $this->db->storedQuery(CS_SQL_DELETE_SKIN, array('skinId' => $res->fetchResult(0, 'skinId')));
								$del->execQuery();
							}

							foreach (array_keys($package['files']) as $file) {
								if (substr($file, -1) == '/') {
									continue;
								}

								@unlink(N2F_REL_PATH . $file);
								unset($package['files'][$file]);
							}

							foreach (array_keys(array_reverse($package['files'])) as $file) {
								if (substr($file, -1) != '/') {
									continue;
								}

								if (isDirEmpty(N2F_REL_PATH . $file)) {
									@rmdir(N2F_REL_PATH . $file);
								}
							}

							$this->reloadApproveds(true);
						}
					}
				}

				return($ret);
			}

			/**
			 * Method to save a setting into the database.  If the setting already exists, it will be updated with the new value.
			 *
			 * @param string $key	String value of the setting's key name.
			 * @param mixed $value	Mixed (simple) value to use for the setting in the database.
			 * @return boolean		Boolean TRUE or FALSE based on the put's success.
			 */
			public function putSetting($key, $value) {
				if ($this->getSetting($key) !== null) {
					$insert = $this->db->storedQuery(CS_SQL_INSERT_SETTING_LOG, array('key' => $key, 'value' => $this->getSetting($key), 'logged' => date('Y-m-d G:i:s')));
					$insert->execQuery();

					if ($insert->isError()) {
						return(false);
					}

					$update = $this->db->storedQuery(CS_SQL_UPDATE_SETTING, array('value' => $value, 'key' => $key));
					$update->execQuery();

					if (!$update->isError()) {
						return(true);
					}
				} else {
					$insert = $this->db->storedQuery(CS_SQL_INSERT_SETTING, array('key' => $key, 'value' => $value));
					$insert->execQuery();

					if (!$insert->isError()) {
						return(true);
					}
				}

				return(false);
			}

			/**
			 * Method to save multiple settings into the database.  If any of the settings already exist, they will be updated with the new value.  The accepted format is array('key1' => 'val1', 'key2' => 'val2').
			 *
			 * @param array $settings	Array of settings to insert in the database.
			 * @return boolean			Boolean TRUE or FALSE depending on the put's success.
			 */
			public function putSettings(array $settings) {
				if (count($settings) < 1) {
					return(false);
				}

				foreach ($settings as $key => $val) {
					$this->putSetting($key, $val);
				}

				return(true);
			}

			/**
			 * Method to retrieve a setting from the database.
			 *
			 * @param string $key	String value of the setting's key name.
			 * @return mixed		Mixed value of the setting from the database, null if not found.
			 */
			public function getSetting($key) {
				$query = $this->db->storedQuery(CS_SQL_SELECT_SETTING, array('key' => $key));
				$query->execQuery();
				$ret = null;

				if (!$query->isError() && $query->numRows() == 1) {
					$ret = $query->fetchResult(0, 'value');
				}

				return($ret);
			}

			/**
			 * Method to retrieve settings from the database.  Accepted format is array('key1', 'key2').
			 *
			 * @param array $keys	Array of key names to retrieve.
			 * @return array		Array of setting values returned from database.  Format is array('key1' => 'value1', 'key2' => 'value2').  Array is empty upon failure.
			 */
			public function getSettings(array $keys) {
				if (count($keys) < 1) {
					return(null);
				}

				$ret = array();

				foreach (array_values($keys) as $key) {
					$ret[$key] = $this->getSetting($key);
				}

				return($ret);
			}

			/**
			 * Method to delete a setting from the database.  This is a hard delete, so there's no going back Jim!
			 *
			 * @param string $key	String value of the setting's key name.
			 * @return boolean		Boolean TRUE or FALSE based on the delete's success.
			 */
			public function deleteSetting($key) {
				if ($this->getSetting($key) !== null) {
					$delete = $this->db->storedQuery(CS_SQL_DELETE_SETTING, array('key' => $key));
					$delete->execQuery();

					if (!$delete->isError()) {
						return(true);
					}
				}

				return(false);
			}

			/**
			 * Method to retrieve the list of tables installed that begin with CS_DATABASE_TABLE_PREFIX.
			 *
			 * @return array	Array of installed tables, table names are the array keys.
			 */
			public function getTables() {
				return($this->tables);
			}

			/**
			 * Method to process any locations that have been found in the layout template.
			 *
			 * @return string				String value of potentially modified content.
			 */
			public function processLocations() {
				$originalContent = func_get_arg(2);
				$attributes = n2f_template::getTagAttributes('cs:location', $originalContent);

				if (!isset($attributes['key']) || empty($attributes['key']) || !isset($this->locations[$attributes['key']])) {
					return('');
				}

				if (!isset($attributes['type'])) {
					$attributes['type'] = 'default';
				}

				if ($attributes['type'] != 'default' && $attributes['type'] != 'block') {
					$ctype = str_replace(array(' ', '-'), array('_', '_'), $attributes['type']);

					if (class_exists("clstype_{$ctype}") && is_callable(array("clstype_{$ctype}", 'processTag'))) {
						@call_user_func_array(array("clstype_{$ctype}", 'processTag'), array($this, $this->locations[$attributes['key']]));
					}
				}

				return($this->processLocation($attributes['key']));
			}

			/**
			 * Method to process any menus that have been found in the layout template.
			 *
			 * @return string
			 */
			public function processMenus() {
				$currentContent = func_get_arg(1);
				$originalContent = func_get_arg(2);
				$attributes = n2f_template::getTagAttributes('cs:menu', $originalContent);

				if (!isset($attributes['key']) || empty($attributes['key']) || !isset($this->menuitems[$attributes['key']]) || count($this->menuitems[$attributes['key']]) < 1) {
					return($currentContent);
				}

				$this->hitEvent(CS_EVENT_MENU_RENDER, array(&$this, $attributes['key'], &$this->menuitems[$attributes['key']]));

				return($this->processMenu($attributes['key'], $this->menuitems[$attributes['key']]));
			}

			/**
			 * Protected method to process a location by combining its contents and recursively searching for child locations.
			 *
			 * @param string $key		Name of location to parse (used for associating with the system location data).
			 * @param integer $tickNumber	Internal tick counter to provide basic protection against infinite loops.
			 * @return string			String of rendered location contents.
			 */
			protected function processLocation($key, $tickNumber = 0) {
				$contents = $this->locations[$key]->getContent();

				if (count($contents) < 1) {
					return('');
				}

				for ($i = 0; $i < count($contents); $i++) {
					if (isset($contents[$i]) && is_array($contents[$i])) {
						if (count($contents[$i]) > 0) {
							$contents[$i] = implode("\n", $contents[$i]);
						} else {
							$contents[$i] = '';
						}
					}
				}

				$buf = implode("\n", array_values($contents));

				if ($tickNumber < CS_MAX_CHILD_LOCATIONS) {
					$innerLoc = n2f_template::getInnerTag('cs:location', $buf);

					while (count($innerLoc) == 2) {
						if (isset($innerLoc['attributes']['key']) && isset($this->locations[$innerLoc['attributes']['key']])) {
							if (!isset($innerLoc['attributes']['type'])) {
								$innerLoc['attributes']['type'] = 'default';
							}

							if ($innerLoc['attributes']['type'] != 'default' && $innerLoc['attributes']['type'] != 'block') {
								$ctype = str_replace(array(' ', '-'), array('_', '_'), $innerLoc['attributes']['type']);

								if (class_exists("clstype_{$ctype}") && is_callable(array("clstype_{$ctype}", 'processTag'))) {
									@call_user_func_array(array("clstype_{$ctype}", 'processTag'), array($this, $this->locations[$innerLoc['attributes']['key']]));
								}
							}

							$buf = str_replace($innerLoc['matched'], $this->processLocation($innerLoc['attributes']['key'], ($tickNumber + 1)), $buf);
						}

						$innerLoc = n2f_template::getInnerTag('cs:location', $buf);
					}
				}

				return($buf);
			}

			/**
			 * Protected method to process a menu by combining its contents and rendering them into the appropriate template.
			 *
			 * @param string $location		String value of location, used mostly for associating with template files.
			 * @param array $menuitems		Array of chameleon_menuitem objects to render.
			 * @param string $tplSuffix		Optional string to specify suffix of current template key.
			 * @param boolean $isNested		Optional boolean to specify if the current menu node is a nested submenu.
			 * @param boolean $activeParent	Optional boolean to denote if the parent item was active.
			 * @return string				String of rendered menu contents.
			 */
			protected function processMenu($location, array $menuitems, $tplSuffix = '_menu', $isNested = false, $activeParent = false) {
				if (count($menuitems) > 0) {
					$tpl = new n2f_template('dynamic');
					$tpl->setModule('main')->setFile($location . $tplSuffix);

					// If there isn't one, don't bother
					if ($tpl->file === null || empty($tpl->file)) {
						return('');
					}

					$renderedItems = '';

					for ($i = 0; $i < count($menuitems); $i++) {
						if (!isset($menuitems[$i])) {
							continue;
						}

						if (!($menuitems[$i] instanceof chameleon_menuitem)) {
							continue;
						}

						$this->hitEvent(CS_EVENT_MENUITEM_RENDER, array(&$this, &$menuitems[$i]));

						if ($i == 0) {
							$menuitems[$i]->first = true;
						}

						if ($i == (count($menuitems) - 1)) {
							$menuitems[$i]->last = true;
						}

						$renderedItems .= $this->processMenuItem($location, $menuitems[$i], ($tplSuffix == '_submenu') ? true : false, $isNested);
					}

					$tpl->setField('menuItems', $renderedItems);
					$tpl->setField('location', $location);
					$tpl->setField('isSubMenu', ($tplSuffix == '_submenu') ? true : false);
					$tpl->setField('isNested', $isNested);
					$tpl->setField('activeParent', $activeParent);
					$tpl->render();

					return($tpl->fetch());
				}

				return('');
			}

			/**
			 * Protected method to process a menu item by rendering it into a template and doing a recursive check for sub-menus.
			 *
			 * @param string $location		String value of location, used mostly for associating with template files.
			 * @param chameleon_menuitem $item	Object to render into template.
			 * @param boolean $isSubMenu		Optional boolean value to indicate if the current menu item is in a submenu.
			 * @param boolean $isNested		Optional string to specify if the current menu node is a nested submenu.
			 * @return string				String of rendered menuitem contents.
			 */
			protected function processMenuItem($location, chameleon_menuitem $item, $isSubMenu = false, $isNested = false) {
				$tpl = new n2f_template('dynamic');
				$tpl->setModule('main')->setFile($location . '_menuitem');

				if (is_array($item->other) && count($item->other) > 0) {
					$new = '';

					foreach ($item->other as $aname => $avalue) {
						$new .= " {$aname}=\"{$avalue}\"";
					}

					$item->other = $new;
				}

				$tpl->setField('item', $item);
				$tpl->setField('location', $location);
				$tpl->setField('isSubMenu', $isSubMenu);
				$tpl->setField('isNested', $isNested);

				if (count($item->submenus) > 0) {
					$tpl->setField('subMenu', $this->processMenu($location, $item->submenus, '_submenu', $isSubMenu, $item->active));
				} else {
					$tpl->setField('subMenu', ' ');
				}

				$tpl->render();

				return($tpl->fetch());
			}

			/**
			 * Protected method to reload the lists of approved modules and extensions.
			 *
			 * @param boolean $force	Optional value to force recreation of the cache.  Defaults to 'false'.
			 */
			protected function reloadApproveds($force = false) {
				$this->packages = ($this->cache->isCached('packages') && !$force) ? $this->cache->getObject('packages') : array();

				if (count($this->packages) < 1) {
					$packages = array();

					$pQuery = $this->db->storedQuery(CS_SQL_SELECT_ALL_PACKAGES);
					$pQuery->execQuery();

					if (!$pQuery->isError() && $pQuery->numRows() > 0) {
						$packages = $pQuery->fetchRows();
					}

					if (count($packages) > 0) {
						foreach (array_values($packages) as $package) {
							$tmp = array(
								'packageId' => $package['packageId'],
								'key' => $package['key'],
								'meta' => new chameleon_package(
									$package['name'],
									(($package['modules'] == '') ? '' : unserialize($package['modules'])),
									(($package['extensions'] == '') ? '' : unserialize($package['extensions'])),
									(($package['files'] == '') ? '' : unserialize($package['files'])),
									$package['baseExt'],
									$package['startMod'],
									$package['errorMod'],
									$package['author'],
									$package['description'],
									$package['url'],
									$package['version'],
									$package['installed'],
									$package['upgradeFrom'],
									(($package['active'] == '1') ? true : false)
								),
								'receiver' => (@class_exists("{$package['key']}_receiver")) ? "{$package['key']}_receiver" : ''
							);

							$this->packages[$package['key']] = $tmp;
						}

						$this->cache->setObject('packages', $this->packages);

						global $cfg;
						$curr = $cfg['cache']['dir'];

						if (N2F_URL_PATH == '/admin/') {
							$cfg['cache']['dir'] = '../n2f_cache/';

							$cache = new n2f_cache(CS_CACHE_EXPIRATION, 'cs', false);
							$cache->setObject('packages', $this->packages);
							$cfg['cache']['dir'] = $curr;
						} else {
							$cfg['cache']['dir'] = './admin/n2f_cache/';

							$cache = new n2f_cache(CS_CACHE_EXPIRATION, 'cs', false);
							$cache->setObject('packages', $this->packages);
							$cfg['cache']['dir'] = $curr;
						}
					}
				}

				if (count($this->packages) > 0) {
					foreach (array_values($this->packages) as $package) {
						if ($package['meta']->active) {
							if ($package['meta']->modules != '' && count($package['meta']->modules) > 0) {
								foreach (array_values($package['meta']->modules) as $mod) {
									$this->modules[] = array(
										'key' => $package['key'],
										'mod' => $mod
									);
								}
							}

							if ($package['meta']->extensions != '' && count($package['meta']->extensions) > 0) {
								foreach (array_values($package['meta']->extensions) as $ext) {
									$this->extensions[] = array(
										'key' => $package['key'],
										'ext' => $ext
									);
								}
							}
						}
					}
				}
			}

			/**
			 * Protected method to serve as default for sending mail.  Uses PHP mail() function.
			 *
			 */
			protected static function _mail($to, $subject, $message, $additional_headers = null, $additional_parameters = null) {
				return(@mail($to, $subject, $message, (($additional_headers === null) ? '' : $additional_headers), (($additional_parameters === null) ? '' : $additional_parameters)));
			}

			/**
			 * Internal static method to process user and all that jazz.
			 *
			 * @return chameleon_user	chameleon_user object for use with user loading.
			 */
			protected static function _initUser() {
				// Grab our user
				$c = chameleon::getInstance();
				$usess = n2f_session::getInstance();

				if ($usess->exists('curr_user') && $usess->exists('lasttime')) {
					$curr_user = $usess->get('curr_user');
					$lasttime = $usess->get('lasttime');

					if ($lasttime < (time() - CS_USER_TIMEOUT)) {
						$usess->delete('curr_user');
						$curr_user = new chameleon_user();
					}

					$c->user = $curr_user;
					$usess->set('lasttime', time());
				} else {
					$c->user = new chameleon_user();
					$usess->set('lasttime', time());
				}

				if ($c->user->userId < 1 && isset($_COOKIE[CS_REMEMBER_COOKIE])) {
					$query = $c->db->storedQuery(CS_SQL_SELECT_USER_SESSION_BY_KEY, array('key' => $_COOKIE[CS_REMEMBER_COOKIE]))->execQuery();

					if (!$query->isError() && $query->numRows() == 1) {
						$session = $query->fetchRow();

						if (strtotime($session['expires']) > time()) {
							if ($_SERVER['REMOTE_ADDR'] == $session['remoteAddr']) {
								$c->user = new chameleon_user($session['userId']);
								$usess->set('curr_user', $c->user);
								$usess->set('lasttime', time());
							} else {
								$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);

								if ($host == $session['remoteHost']) {
									$c->user = new chameleon_user($session['userId']);
									$usess->set('curr_user', $c->user);
									$usess->set('lasttime', time());
								}
							}
						} else {
							$query = $c->db->storedQuery(CS_SQL_DELETE_USER_SESSION, array('sessionId' => $session['sessionId']))->execQuery();
						}
					}
				}

				return($c->user);
			}

			/**
			 * Checks if provided module path is from an approved module.
			 *
			 * @param string $path		String value of module path to check.
			 * @param boolean $inRoot	Optional boolean value determining if a root module, default is FALSE.
			 * @return boolean			Boolean TRUE or FALSE depending on module's status.
			 */
			protected static function _approvedModule($path, $inRoot = false) {
				// Grab the global instances
				$c = chameleon::getInstance();
				$n2f = n2f_cls::getInstance();

				// If we're in dev mode, all are approved; Same with setup..
				if (CS_ENABLE_DEV_MODE || N2F_URL_PATH == '/setup/') {
					return(true);
				}

				// If we have no approved modules, then no, we aren't approved
				if (count($c->modules) < 1) {
					return(false);
				}

				// Is it a root main module?
				if ($inRoot && ($n2f->cfg->def_mods->start == $path || $n2f->cfg->def_mods->error == $path)) {
					return(true);
				}

				// Store path with prefix for faster comparison
				$path = ($inRoot) ? $path : substr(N2F_URL_PATH, 1) . $path;

				// Begin le loop
				foreach (array_values($c->modules) as $module) {
					// Compare against path
					if ($path == $module['mod']) {
						// We're good, get outta here
						return(true);
					}
				}

				// If we made it here, let's just assume that's a 'no'
				return(false);
			}
		}

		/**
		 * Class to describe a content location.
		 *
		 */
		class chameleon_location {
			/**
			 * Location key (identifier).
			 *
			 * @var string
			 */
			protected $key = '';
			/**
			 * List of content items for the location.
			 *
			 * @var array
			 */
			protected $contents = array();


			/**
			 * Initializes a new content location object.
			 *
			 * @param string $key	String value for location key.
			 */
			public function __construct($key = null) {
				$this->key = $key;
			}

			/**
			 * Retrieves the location key.
			 *
			 * @return string
			 */
			public function getKey() {
				return($this->key);
			}

			/**
			 * Sets the location key.
			 *
			 * @param string $key
			 */
			public function setKey($key) {
				$this->key = $key;
			}

			/**
			 * Retrieves the registered content for the location.
			 *
			 * @return array
			 */
			public function getContent() {
				return($this->contents);
			}

			/**
			 * Adds new content to the location.
			 *
			 * @param string $content	String value of content to add to location.
			 * @param integer $pos		Optional integer value to determine position.
			 */
			public function addContent($content, $pos = null) {
				if ($pos !== null) {
					$pos = intval($pos);

					if ($pos < 0) {
						array_unshift($this->contents, $content);
					} else {
						if (isset($this->contents[$pos])) {
							$last = '';
							$next = '';
							$len = count($this->contents) + 1;

							for ($i = $pos; $i < $len; $i++) {
								if (!isset($this->contents[$i])) {
									array_push($this->contents, $last);

									break;
								}

								$next = $this->contents[$i];
								$this->contents[$i] = $last;
								$last = $next;
							}

							$this->contents[$pos] = $content;
						} else {
							for ($i = (count($this->contents) - 1); $i < $pos; $i++) {
								$this->contents[$i] = '';
							}

							$this->contents[$pos] = $content;
						}
					}

					return;
				}

				array_push($this->contents, $content);

				return;
			}

			/**
			 * Sets the location's content list.
			 *
			 * @param array $content	Array of registered content strings.
			 */
			public function setContent(array $content) {
				$this->contents = $content;
			}
		}

		/**
		 * Class to describe a menu item in the system.
		 *
		 */
		class chameleon_menuitem {
			/**
			 * Location key for menu item (not reliable).
			 *
			 * @var string
			 */
			public $location;
			/**
			 * Position of menu item in location.
			 *
			 * @var integer
			 */
			public $position;
			/**
			 * Key of the menu item.
			 *
			 * @var string
			 */
			public $key;
			/**
			 * Href for the menu item. (<a href="<href>"></a>)
			 *
			 * @var string
			 */
			public $href;
			/**
			 * Target for the menu item. (<a href="" target="<target>"></a>)
			 *
			 * @var string
			 */
			public $target;
			/**
			 * Other attributes for the menu item. (<a href=""<other>></a>)
			 *
			 * @var string
			 */
			public $other;
			/**
			 * Text for the menu item. (<a href=""><text></a>)
			 *
			 * @var string
			 */
			public $text;
			/**
			 * Boolean flag for the first menu item in the location.
			 *
			 * @var boolean
			 */
			public $first;
			/**
			 * Boolean flag for the menu item's active status.
			 *
			 * @var boolean
			 */
			public $active;
			/**
			 * Boolean flag for the last menu item in the location.
			 *
			 * @var boolean
			 */
			public $last;
			/**
			 * Array of submenus.
			 *
			 * @var array
			 */
			public $submenus = array();


			/**
			 * Initializes a menu item object.
			 *
			 * @param string $location	Location key for menu item.
			 * @param integer $position	Position of menu item in location.
			 * @param string $key		Key of the menu item.
			 * @param string $href		Href for the menu item.
			 * @param string $text		Text for the menu item.
			 * @param string $target		Target for the menu item.
			 * @param string $other		Other attributes for the menu item.
			 * @param boolean $first		Boolean flag for the first menu item in the location.
			 * @param boolean $active	Boolean flag for the menu item's active status.
			 * @param boolean $last		Boolean flag for the last menu item in the location.
			 * @param array $submenus	Array of submenu items.
			 */
			public function __construct($location, $position = null, $key, $href, $text, $target = null, $other = null, $first = null, $active = null, $last = null, array $submenus = null) {
				$this->location = $location;
				$this->position = $position;
				$this->key = $key;
				$this->href = $href;
				$this->text = $text;
				$this->target = ($target !== null) ? $target : '';
				$this->other = ($other !== null) ? $other : '';
				$this->first = ($first !== null && $first === true) ? true : false;
				$this->active = ($active !== null && $active === true) ? true : false;
				$this->last = ($last !== null && $last === true) ? true : false;

				if ($submenus !== null && count($submenus) > 0) {
					foreach (array_values($submenus) as $sub) {
						if ($sub instanceof chameleon_menuitem) {
							$this->submenus[] = $sub;
						}
					}
				}
			}

			/**
			 * Method to add a submenu item to the menu item.
			 *
			 * @param integer $position	Position of menu item in location.
			 * @param string $key		Key of the menu item.
			 * @param string $href		Href for the menu item.
			 * @param string $text		Text for the menu item.
			 * @param string $target		Target for the menu item.
			 * @param string $other		Other attributes for the menu item.
			 * @param boolean $first		Boolean flag for the first menu item in the location.
			 * @param boolean $active	Boolean flag for the menu item's active status.
			 * @param boolean $last		Boolean flag for the last menu item in the location.
			 * @param array $submenus	Array of submenu items.
			 */
			public function addSubMenu($position = null, $key, $href, $text, $target = null, $other = null, $first = null, $active = null, $last = null, array $submenus = null) {
				$this->submenus[] = new chameleon_menuitem($this->location, $position, $key, $href, $text, $target, $other, $first, $active, $last, $submenus);
			}
		}

		/**
		 * Class to hold information on a package.
		 *
		 */
		class chameleon_package {
			/**
			 * Array of modules owned by the package.
			 *
			 * @var array
			 */
			public $modules;
			/**
			 * Array of extensions owned by the package.
			 *
			 * @var array
			 */
			public $extensions;
			/**
			 * Array of files/folders owned by the package.
			 *
			 * @var array
			 */
			public $files;
			/**
			 * Key name of base extension, when provided.
			 *
			 * @var string
			 */
			public $baseExt;
			/**
			 * Key name of start module available from package.
			 *
			 * @var string
			 */
			public $startMod;
			/**
			 * Key name of error module available from package.
			 *
			 * @var string
			 */
			public $errorMod;
			/**
			 * Name of package.
			 *
			 * @var string
			 */
			public $name;
			/**
			 * Author's name.
			 *
			 * @var string
			 */
			public $author;
			/**
			 * Short description for package.
			 *
			 * @var string
			 */
			public $description;
			/**
			 * Information url for package.
			 *
			 * @var string
			 */
			public $url;
			/**
			 * Version number for package.
			 *
			 * @var string
			 */
			public $version;
			/**
			 * Date and time package was installed.
			 *
			 * @var string
			 */
			public $installed;
			/**
			 * Previous version if upgrade is pending.  Used internally by package manager.
			 *
			 * @var string
			 */
			public $upgradeFrom;
			/**
			 * Boolean value determining package's active status.
			 *
			 * @var boolean
			 */
			public $active;


			/**
			 * Method to retrieve a chameleon_package object based on a package key.
			 *
			 * @param string $key		Package key for searching database.
			 * @return chameleon_package	chameleon_package object, null if failed.
			 */
			public static function getPackage($key) {
				$ret = null;

				$sel = n2f_database::getInstance()->storedQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, array('key' => $key));
				$sel->execQuery();

				if (!$sel->isError() && $sel->numRows() == 1) {
					$row = $sel->fetchRow();

					$ret = new chameleon_package();
					$ret->modules = ($row['modules'] == '') ? array() : unserialize($row['modules']);
					$ret->extensions = ($row['extensions'] == '') ? array() : unserialize($row['extensions']);
					$ret->files = ($row['files'] == '') ? array() : unserialize($row['files']);
					$ret->baseExt = $row['baseExt'];
					$ret->startMod = $row['startMod'];
					$ret->errorMod = $row['errorMod'];
					$ret->name = $row['name'];
					$ret->author = $row['author'];
					$ret->description = $row['description'];
					$ret->url = $row['url'];
					$ret->version = $row['version'];
					$ret->installed = $row['installed'];
					$ret->upgradeFrom = $row['upgradeFrom'];
					$ret->active = ($row['active'] == 1) ? true : false;
				}

				return($ret);
			}


			/**
			 * Initializes a new chameleon_package object.
			 *
			 * @param string $name		Name of package.
			 * @param array $modules		Array of modules owned by the package.
			 * @param array $extensions	Array of extensions owned by the package.
			 * @param array $files		Array of files owned by the package.
			 * @param string $baseExt	Key name of base extension, when provided.
			 * @param string $startMod	Key name of start module available from package.
			 * @param string $errorMod	Key name of error module available from package.
			 * @param string $author		Author's name.
			 * @param string $description	Short description for package.
			 * @param string $url		Information url for package.
			 * @param string $version	Version number for package.
			 * @param string $installed	Date and time package was installed.
			 * @param string $upgradeFrom	Previous version if upgrade is pending.  Used internally by package manager.
			 * @param boolean $active	Boolean value determining package's active status.
			 */
			public function __construct($name = null, $modules = null, $extensions = null, $files = null, $baseExt = null, $startMod = null, $errorMod = null, $author = null, $description = null, $url = null, $version = null, $installed = null, $upgradeFrom = null, $active = null) {
				$this->modules = $modules;
				$this->extensions = $extensions;
				$this->files = $files;
				$this->baseExt = $baseExt;
				$this->startMod = $startMod;
				$this->errorMod = $errorMod;
				$this->name = $name;
				$this->author = $author;
				$this->description = $description;
				$this->url = $url;
				$this->version = $version;
				$this->installed = $installed;
				$this->upgradeFrom = $upgradeFrom;
				$this->active = $active;
			}
		}

		/**
		 * Class to hold information on a basic user in the system.
		 *
		 */
		class chameleon_user {
			/**
			 * Unique identifier for the user.
			 *
			 * @var integer
			 */
			public $userId;
			/**
			 * Username to identify user.
			 *
			 * @var string
			 */
			public $username;
			/**
			 * Email address for contacting user.
			 *
			 * @var string
			 */
			public $email;
			/**
			 * Password hash for user.
			 *
			 * @var string
			 */
			public $password;
			/**
			 * Randomly generated character set to make password more complex.
			 *
			 * @var string
			 */
			public $salt;
			/**
			 * Expiration date of current salt (Y-m-d G:i:s).
			 *
			 * @var string
			 */
			public $saltExpire;
			/**
			 * Date the user joined the system (Y-m-d G:i:s).
			 *
			 * @var string
			 */
			public $dateJoined;
			/**
			 * Confirmation code for security.
			 *
			 * @var string
			 */
			public $confirm;
			/**
			 * Current active status of the user.
			 *
			 * @var integer
			 */
			public $status;
			/**
			 * Internal reference database object.
			 *
			 * @var n2f_database
			 */
			private $db;


			/**
			 * Static method to log the current state of a user in the database.
			 *
			 * @param chameleon_user $user	User object to log into database.
			 * @return boolean				Boolean TRUE or FALSE based on log action's success.
			 */
			public static function log(chameleon_user $user) {
				if ($user->userId < 1) {
					return(false);
				}

				$ins = n2f_database::getInstance()->storedQuery(CS_SQL_INSERT_USER_LOG, array(
					'userId'		=> $user->userId,
					'username' 	=> $user->username,
					'email'		=> $user->email,
					'password'	=> $user->password,
					'salt'		=> $user->salt,
					'saltExpire'	=> $user->saltExpire,
					'dateJoined'	=> $user->dateJoined,
					'status'		=> $user->status,
					'dateLogged'	=> date('Y-m-d G:i:s')
				));
				$ins->execQuery();

				if ($ins->isError()) {
					return(false);
				}

				return(true);
			}

			/**
			 * Static method to check if a userId has a specific permission.
			 *
			 * @param integer $userId		Unique user identifier.
			 * @param string $package_key		Key for package which owns the permission.
			 * @param string $perm_key		Key for the permission.
			 * @param boolean $check_super	Boolean value to turn off using CS_PERMS_SUPER_ADMIN check.
			 * @return boolean				Boolean TRUE or FALSE based on the user having the permission.
			 */
			public static function userHasPerm($userId, $package_key, $perm_key, $check_super = true) {
				if ($userId === null || $package_key === null || $perm_key === null) {
					return(false);
				}

				if (empty($package_key) || empty($perm_key)) {
					return(false);
				}

				$db = n2f_database::getInstance();

				if ($check_super) {
					$query = $db->storedQuery(CS_SQL_SELECT_PERM_BY_PACKAGEKEY, array('pkg_key' => 'cs_users', 'perm_key' => CS_PERMS_SUPER_ADMIN));
					$query->execQuery();

					if (!$query->isError() && $query->numRows() == 1) {
						$permId = $query->fetchResult(0, 'permId');
						$query = $db->storedQuery(CS_SQL_SELECT_USERPERM_BY_USERID, array('userId' => intval($userId), 'permId' => $permId));
						$query->execQuery();

						if (!$query->isError() && $query->numRows() == 1) {
							return(true);
						}
					}
				}

				$query = $db->storedQuery(CS_SQL_SELECT_PERM_BY_PACKAGEKEY, array('pkg_key' => $package_key, 'perm_key' => $perm_key));
				$query->execQuery();

				if ($query->isError() || $query->numRows() != 1) {
					return(false);
				}

				$permId = $query->fetchResult(0, 'permId');
				$query = $db->storedQuery(CS_SQL_SELECT_USERPERM_BY_USERID, array('userId' => intval($userId), 'permId' => $permId));
				$query->execQuery();

				if ($query->isError() || $query->numRows() != 1) {
					return(false);
				}

				return(true);
			}

			/**
			 * Static method to add a permission to a userId.
			 *
			 * @param integer $userId	Unique user identifier.
			 * @param string $package_key	Key for the package which owns the permission.
			 * @param string $perm_key	Key for the permission.
			 * @return boolean			Boolean TRUE or FALSE based on the add action's success.
			 */
			public static function userAddPerm($userId, $package_key, $perm_key) {
				if ($userId === null || $package_key === null || $perm_key === null) {
					return(false);
				}

				if (empty($package_key) || empty($perm_key)) {
					return(false);
				}

				$db = n2f_database::getInstance();

				$query = $db->storedQuery(CS_SQL_SELECT_PERM_BY_PACKAGEKEY, array('pkg_key' => $package_key, 'perm_key' => $perm_key));
				$query->execQuery();

				if ($query->isError() || $query->numRows() != 1) {
					return(false);
				}

				$permId = $query->fetchResult(0, 'permId');
				$query = $db->storedQuery(CS_SQL_SELECT_USERPERM_BY_USERID, array('userId' => intval($userId), 'permId' => $permId));
				$query->execQuery();

				if ($query->isError()) {
					return(false);
				}

				if ($query->numRows() > 0) {
					return(true);
				}

				$insert = $db->storedQuery(CS_SQL_INSERT_USERPERM, array(
					'permId' => $permId,
					'userId' => intval($userId),
					'granted' => date('Y-m-d G:i:s')
				));
				$insert->execQuery();

				if ($insert->isError()) {
					return(false);
				}

				return(true);
			}

			/**
			 * Static method to delete a permission for a userId.
			 *
			 * @param integer $userId	Unique user identifier.
			 * @param string $package_key	Key for the package which owns the permission.
			 * @param string $perm_key	Key for the permission.
			 * @return boolean			Boolean TRUE or FALSE based on the delete action's success.
			 */
			public static function userDelPerm($userId, $package_key, $perm_key) {
				if ($userId === null || $package_key === null || $perm_key === null) {
					return(false);
				}

				if (empty($package_key) || empty($perm_key)) {
					return(false);
				}

				$db = n2f_database::getInstance();

				$query = $db->storedQuery(CS_SQL_SELECT_PERM_BY_PACKAGEKEY, array('pkg_key' => $package_key, 'perm_key' => $perm_key));
				$query->execQuery();

				if ($query->isError() || $query->numRows() != 1) {
					return(false);
				}

				$permId = $query->fetchResult(0, 'permId');
				$query = $db->storedQuery(CS_SQL_SELECT_USERPERM_BY_USERID, array('userId' => intval($userId), 'permId' => $permId));
				$query->execQuery();

				if ($query->isError()) {
					return(false);
				}

				if ($query->numRows() < 1) {
					return(true);
				}

				$perm = $query->fetchRow();
				$insert = $db->storedQuery(CS_SQL_INSERT_USERPERM_LOG, array(
					'upId' => $perm['upId'],
					'permId' => $perm['permId'],
					'userId' => $perm['userId'],
					'granted' => $perm['granted'],
					'logged' => date('Y-m-d G:i:s')
				));
				$insert->execQuery();

				if ($insert->isError()) {
					return(false);
				}

				$delete = $db->storedQuery(CS_SQL_DELETE_USERPERM, array('upId' => $perm['upId']));
				$delete->execQuery();

				if ($delete->isError()) {
					return(false);
				}

				return(true);
			}

			/**
			 * Static method to generate a random salt string.
			 *
			 * @return string	Randomized 64-character string.
			 */
			public static function generateSalt() {
				$ret = '';
				$chars = array(
					'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
					'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
					'0','1','2','3','4','5','6','7','8','9',
					'~','!','@','#','$','%','^','&','*','(',')','-','+'
				);

				for ($i = 0; $i < 64; $i++) {
					$ret .= $chars[mt_rand(0, 74)];
				}

				return($ret);
			}

			/**
			 * Static method to generate a random alpha numeric code.
			 *
			 * @param int $length
			 * @return string	Randomized 64-character string.
			 */
			public static function generateChars($length) {
				$ret = '';
				$chars = array(
					'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
					'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
					'0','1','2','3','4','5','6','7','8','9'
				);

				$max = count($chars) - 1;
				for ($i = 0; $i < $length; $i++) {
					$ret .= $chars[mt_rand(0, $max)];
				}

				return($ret);
			}

			/**
			 * Static method to search the user database for matching users based on email address and username.
			 *
			 * @param string $searchString	Optional string value to use for searching email/username text.
			 * @param integer $startIndex		Optional integer value to designate the starting offset.
			 * @param integer $maximumRows	Optional integer value to designate the resultset size.
			 * @param string $orderBy		Optional string value to designate the field to use for ordering the resultset.
			 * @param string $orderDir		Optional string value to designate the direction to use for ordering the resultset.
			 * @return array				Array of user information, null if an error is encountered.
			 */
			public static function searchUsers($searchString = null, $startIndex = -1, $maximumRows = -1, $orderBy = 'userId', $orderDir = 'DESC') {
				$query = null;
				$db = n2f_database::getInstance();

				if ($searchString !== null) {
					if ($startIndex == -1 && $maximumRows < 1) {
						$query = $db->storedQuery(CS_SQL_SELECT_USERS_SEARCH, array('username' => $searchString, 'email' => $searchString), array('ORDER_BY' => $orderBy, 'ORDER_DIR' => $orderDir));
					} else {
						$query = $db->storedQuery(CS_SQL_SELECT_USERS_SEARCH_LIMIT, array('username' => $searchString, 'email' => $searchString), array('ORDER_BY' => $orderBy, 'ORDER_DIR' => $orderDir, 'OFFSET' => $startIndex, 'LIMIT' => $maximumRows));
					}
				} else {
					if ($startIndex == -1 || $maximumRows < 1) {
						$query = $db->storedQuery(CS_SQL_SELECT_ALL_USERS_QUERY, null, array('ORDER_BY' => $orderBy, 'ORDER_DIR' => $orderDir));
					} else {
						$query = $db->storedQuery(CS_SQL_SELECT_ALL_USERS_LIMIT, null, array('ORDER_BY' => $orderBy, 'ORDER_DIR' => $orderDir, 'OFFSET' => $startIndex, 'LIMIT' => $maximumRows));
					}
				}

				$query->execQuery();

				if ($query->isError()) {
					return(null);
				}

				return($query->fetchRows());
			}

			/**
			 * Static method to return the total number of matching users based on email address and username.
			 *
			 * @param string $searchString	Optional string value to use for searching email/username text.
			 * @return integer				Integer value representing the number of matches, 0 if an error is encountered.
			 */
			public static function searchUserCount($searchString = null) {
				$query = null;
				$db = n2f_database::getInstance();

				if ($searchString !== null) {
					$query = $db->storedQuery(CS_SQL_SELECT_USERS_SEARCH_COUNT, null, array('username' => $searchString, 'email' => $searchString));
				} else {
					$query = $db->storedQuery(CS_SQL_SELECT_ALL_USERS_COUNT);
				}

				$query->execQuery();

				if ($query->isError()) {
					return(0);
				}

				return($query->fetchResult(0, 'COUNT(*)'));
			}

			/**
			 * Static method to return a set of users from the database.
			 *
			 * @param integer $startIndex		Optional integer value to designate the starting offset.
			 * @param integer $maximumRows	Optional integer value to designate the resultset size.
			 * @param string $orderBy		Optional string value ot designate the field to use for ordering the resultset.
			 * @param string $orderDir		Optional string value to designate the direction to use for ordering the resultset.
			 * @return array				Array of user information, null if an error is encountered.
			 */
			public static function getAllUsers($startIndex = -1, $maximumRows = -1, $orderBy = 'userId', $orderDir = 'DESC') {
				return(self::searchUsers(null, $startIndex, $maximumRows, $orderBy, $orderDir));
			}

			/**
			 * Static method to return the total number of users.
			 *
			 * @return integer	Integer value representing the number of matches, 0 if an error is encountered.
			 */
			public static function getAllUserCount() {
				return(self::searchUserCount(null));
			}

			/**
			 * Static method to return a user based on their email address.
			 *
			 * @param string $email		String value of user email address.
			 * @return chameleon_user	chameleon_user object of found user (empty user upon failure).
			 */
			public static function getUserByEmail($email) {
				$ret = new chameleon_user();
				$db = n2f_database::getInstance();

				if (!validEmail($email)) {
					return($ret);
				}

				$query = $db->storedQuery(CS_SQL_SELECT_USER_BY_EMAIL, array('email' => $email))->execQuery();

				if ($query->isError() || $query->numRows() < 1) {
					return($ret);
				}

				$ret->userId = $query->fetchResult(0, 'userId');
				$ret->update();

				return($ret);
			}

			/**
			 * Static method to return a user based on their username.
			 *
			 * @param string $username	String value of user username.
			 * @return chameleon_user	chameleon_user object of found user (empty user upon failure).
			 */
			public static function getUserByUsername($username) {
				$ret = new chameleon_user();
				$db = n2f_database::getInstance();

				if (empty($username) || strlen($username) < 1) {
					return($ret);
				}

				$query = $db->storedQuery(CS_SQL_SELECT_USER_BY_USERNAME, array('username' => $username))->execQuery();

				if ($query->isError() || $query->numRows() < 1) {
					return($ret);
				}

				$ret->userId = $query->fetchResult(0, 'userId');
				$ret->update();

				return($ret);
			}

			/**
			 * Static method to return a user based on their confirmation code.
			 *
			 * @param string $code	String value of user confirmation code.
			 * @return chameleon_user	chameleon_user object of found user (empty user upon failure).
			 */
			public static function getUserByConfirmCode($code) {
				$ret = new chameleon_user();
				$db = n2f_database::getInstance();

				if (empty($code) || strlen($code) < 1) {
					return($ret);
				}

				$query = $db->storedQuery(CS_SQL_SELECT_USER_BY_CONFIRM, array('confirm' => $code))->execQuery();

				if ($query->isError() || $query->numRows() < 1) {
					return($ret);
				}

				$ret->userId = $query->fetchResult(0, 'userId');
				$ret->update();

				return($ret);
			}


			/**
			 * Initializes a new chameleon_user object.
			 *
			 * @param integer $userId	Optional unique user identifier.
			 */
			public function __construct($userId = null) {
				$this->db = n2f_database::getInstance();

				if ($userId !== null) {
					$this->userId = intval($userId);
					$ret = $this->update();

					if (!IsSuccess($ret)) {
						$this->clear();
					}
				}
			}

			/**
			 * Method to clear the user object to its 'null' state.
			 *
			 */
			public function clear() {
				$this->userId = 0;
				$this->username = null;
				$this->email = null;
				$this->password = null;
				$this->salt = null;
				$this->saltExpire = null;
				$this->dateJoined = null;
				$this->status = 0;

				return;
			}

			/**
			 * Method to update the user object to or from the database.
			 *
			 * @param boolean $toDb	Boolean toggle to determine update direction.
			 * @return n2f_return	n2f_return object with success/failure information.
			 */
			public function update($toDb = false) {
				$ret = new n2f_return();

				if ($this->userId < 1) {
					$ret->isFail();
					$ret->addMsg("Invalid user identifier supplied for update, update aborted.");
				} else {
					if ($toDb) {
						if (!self::log($this)) {
							$ret->isFail();
							$ret->addMsg("Failed to log user information, update aborted.");
						} else {
							$utmp = new chameleon_user($this->userId);
							$ret->isGood();

							if ($utmp->email !== $this->email && self::getUserByEmail($this->email)->userId > 0) {
								$ret->isFail();
								$ret->addMsg("Cannot update user, email already in use by another user.");
							}

							if ($utmp->username !== $this->username && self::getUserByUsername($this->username)->userId > 0) {
								$ret->isFail();
								$ret->addMsg("Cannot update user, username already in use by another user.");
							}

							if ($ret->isSuccess()) {
								$query = $this->db->storedQuery(CS_SQL_UPDATE_USER, array(
									'username' => $this->username,
									'email' => $this->email,
									'password' => $this->password,
									'salt' => $this->salt,
									'saltExpire' => $this->saltExpire,
									'dateJoined' => $this->dateJoined,
									'status' => $this->status,
									'userId' => $this->userId
								));
								$query->execQuery();

								if ($query->isError()) {
									$ret->isFail();
									$ret->addMsg("Failed to update user information, update aborted.");
								} else {
									$ret->isGood();
								}
							}
						}
					} else {
						$user = $this->db->storedQuery(CS_SQL_SELECT_USER_BY_ID, array('userId' => $this->userId));
						$user->execQuery();

						if ($user->isError()) {
							$ret->isFail();
							$ret->addMsg($user->fetchError());
						} else if ($user->numRows() != 1) {
							$ret->isFail();
							$ret->addMsg("Invalid row count returned by database, update aborted.");
						} else {
							$ret->isGood();
							$row = $user->fetchRow();

							$this->username = $row['username'];
							$this->email = $row['email'];
							$this->password = $row['password'];
							$this->salt = $row['salt'];
							$this->saltExpire = $row['saltExpire'];
							$this->dateJoined = $row['dateJoined'];
							$this->status = $row['status'];
						}
					}
				}

				return($ret);
			}

			/**
			 * Method to add the user object to the database.
			 *
			 * @return n2f_return	n2f_return object with success/failure information.
			 */
			public function create() {
				$ret = new n2f_return();

				if ($this->userId > 0) {
					$ret->isFail();
					$ret->addMsg("Invalid user identifier supplied for create, create aborted.");
				} else {
					if (self::getUserByEmail($this->email)->userId > 0 || self::getUserByUsername($this->username)->userId > 0) {
						$ret->isFail();
						$ret->addMsg("Username or email is already in use, create aborted.");
					} else {
						$userId = $this->db->storedQuery(CS_SQL_INSERT_USER, array(
							'username' => $this->username,
							'email' => $this->email,
							'password' => $this->password,
							'salt' => $this->salt,
							'saltExpire' => $this->saltExpire,
							'dateJoined' => $this->dateJoined,
							'status' => $this->status
						));
						$userId->execQuery();

						if ($userId->isError()) {
							$ret->isFail();
							$ret->addMsg("Failed to add user information to database, create aborted.");
						} else {
							$ret->isGood();
							$this->userId = $userId->fetchInc();
						}
					}
				}

				return($ret);
			}

			/**
			 * Method to remove the user object from the database.
			 *
			 * @return n2f_return	n2f_return object with success/failure information.
			 */
			public function delete() {
				$ret = new n2f_return();

				if ($this->userId < 1) {
					$ret->isFail();
					$ret->addMsg("Invalid user identifier supplied for delete, delete aborted.");
				} else {
					if (!self::log($this)) {
						$ret->isFail();
						$ret->addMsg("Failed to log user information, delete aborted.");
					} else {
						$delete = $this->db->storedQuery(CS_SQL_DELETE_USER, array('userId' => $this->userId));
						$delete->execQuery();

						if ($delete->isError()) {
							$ret->isFail();
							$ret->addMsg("Failed to delete user information, delete aborted.");
						} else {
							$ret->isGood();
						}
					}
				}

				return($ret);
			}

			/**
			 * Method to determine if the user has a specific permission.
			 *
			 * @param string $package_key		Key for package which owns the permission.
			 * @param string $perm_key		Key for the permission.
			 * @param boolean $check_super	Boolean value to turn off using CS_PERMS_SUPER_ADMIN check.
			 * @return boolean				Boolean TRUE or FALSE based on the user having the permission.
			 */
			public function hasPerm($package_key, $perm_key, $check_super = true) {
				return(self::userHasPerm($this->userId, $package_key, $perm_key, $check_super));
			}

			/**
			 * Method to add a permission to a user.
			 *
			 * @param string $package_key	Key for the package which owns the permission.
			 * @param string $perm_key	Key for the permission.
			 * @return boolean			Boolean TRUE or FALSE based on the add action's success.
			 */
			public function addPerm($package_key, $perm_key) {
				return(self::userAddPerm($this->userId, $package_key, $perm_key));
			}

			/**
			 * Method to delete a permission for a user.
			 *
			 * @param string $package_key	Key for the package which owns the permission.
			 * @param string $perm_key	Key for the permission.
			 * @return boolean			Boolean TRUE or FALSE based on the delete action's success.
			 */
			public function delPerm($package_key, $perm_key) {
				return(self::userDelPerm($this->userId, $package_key, $perm_key));
			}
		}

		/**
		 * Abstract class for building magic receiver classes.
		 *
		 */
		abstract class chameleon_receiver {
			/**
			 * Method to handle package activation.
			 *
			 * @param chameleon $cs	chameleon object calling the method.
			 */
			public function activate(chameleon $cs) {
				if ($cs === null) {
					return;
				}

				return;
			}

			/**
			 * Method to handle package upgrading.
			 *
			 * @param chameleon $cs		chameleon object calling the method.
			 * @param string $version	String value of previous version number.
			 */
			public function upgrade(chameleon $cs, $version) {
				if ($cs === null || $version === null) {
					return;
				}

				return;
			}

			/**
			 * Method to handle package deactivation.
			 *
			 * @param chameleon $cs	chameleon object calling the method.
			 */
			public function deactivate(chameleon $cs) {
				if ($cs === null) {
					return;
				}

				return;
			}
		}

		/**
		 * Class for the 'rawjs' content location type.
		 *
		 */
		class clstype_rawjs {
			/**
			 * Internal method to process the rawjs content location.
			 *
			 * @param chameleon $cs			chameleon object calling the method.
			 * @param chameleon_location $loc	chameleon_location being modified.
			 */
			public static function processTag(chameleon $cs, chameleon_location $loc) {
				if ($cs === null) {
					return;
				}

				$contents = $loc->getContent();

				if (count($contents) > 0) {
					array_unshift($contents, '<script type="text/javascript">');
					array_push($contents, '</script>');
					$loc->setContent($contents);
				}
			}
		}

		/**
		 * Class for the 'rawcss' content location type.
		 *
		 */
		class clstype_rawcss {
			/**
			 * Internal method to process the rawcss content location.
			 *
			 * @param chameleon $cs			chameleon object calling the method.
			 * @param chameleon_location $loc	chameleon_location being modified.
			 */
			public static function processTag(chameleon $cs, chameleon_location $loc) {
				if ($cs === null) {
					return;
				}

				$contents = $loc->getContent();

				if (count($contents) > 0) {
					array_unshift($contents, '<style type="text/css">');
					array_push($contents, '</style>');
					$loc->setContent($contents);
				}
			}
		}

		/**
		 * Function to grab the contents of a simple template and return them as a string.
		 *
		 * @param string $module	Module for template.
		 * @param string $file	File name of template.
		 * @param string $engine	Optional template engine, defaults to 'dynamic.'
		 * @return string		Contents of template file.
		 */
		function getSimpleTpl($module, $file, $engine = 'dynamic') {
			$tpl = new n2f_template($engine);

			// Assign current skin
			if (N2F_URL_PATH == '/admin/') {
				$tpl->setSkin(chameleon::getInstance()->getSetting('cs_admin_skin'));
			} else if (N2F_URL_PATH == '/') {
				$tpl->setSkin(chameleon::getInstance()->getSetting('cs_front_skin'));
			}

			$tpl->setModule($module)->setFile($file);
			$tpl->render();

			return($tpl->fetch());
		}

		/**
		 * Function to check if a directory is empty.
		 *
		 * @param string $dirpath	String value of directory path.
		 * @return boolean			Boolean TRUE or FALSE based on directory contents.
		 */
		function isDirEmpty($dirpath) {
			$files = @scandir($dirpath);

			if (!$files) {
				return(false);
			}

			if (count($files) > 2) {
				return(false);
			}

			return(true);
		}

		/**
		 * Function to simplify permission check/redirect calls.
		 *
		 * @param string $package_key			String value of the package key to use for permission validation.
		 * @param string $perm_key			String value of the permission key to use for permission validation.
		 * @param string $login_redir			String value to use for login redirection (if there is no logged in user).
		 * @param string $insufficient_redir	String value to use for insufficient permission redirection (if the logged in user can't access this section).
		 * @param string $return_redir		String value to use for return-trip redirection (mostly for logins).
		 * @param string $login_msg			Optional string to use for login redirects (default is 'You must log in to view this page.').
		 * @param string $insufficient_msg		Optional string to use for insufficient permission redirects (default is 'You do not have permission to view this page.').
		 */
		function doAuth($package_key, $perm_key, $login_redir, $insufficient_redir, $return_redir, $login_msg = null, $insufficient_msg = null) {
			$user = chameleon::getInstance()->user;
			$sess = n2f_session::getInstance(null, 'auth');
			$login_msg = ($login_msg === null) ? 'You must log in to view this page.' : $login_msg;
			$insufficient_msg = ($insufficient_msg === null) ? 'You do not have permission to view this page.' : $insufficient_msg;

			if ($user->userId < 1) {
				$sess->set('redir', $return_redir);
				$sess->set('message', $login_msg);

				header("Location: {$login_redir}");
				exit;
			}

			if (!$user->hasPerm($package_key, $perm_key)) {
				$sess->set('redir', $return_redir);
				$sess->set('message', $insufficient_msg);

				header("Location: {$insufficient_redir}");
				exit;
			}

			return;
		}

		/**
		 * Function to generate a new password reset code.
		 *
		 * @param string $username
		 *
		 * @return n2f_return
		 */
		function doForgotPassword($username) {
			$db = n2f_database::getInstance();
			$ret = new n2f_return();
			$ret->isFail();

			$query = $db->storedQuery(CS_SQL_SELECT_USER_BY_USERNAME, array('username' => $username))->execQuery();
			if ($query->isError()) {
				$ret->addMsg("Unable to load the user information.");

				if (CS_ENABLE_DEV_MODE) {
					$ret->addMsg($query->fetchError());
					$ret->addMsg($query->query);
				}

				return($ret);
			}

			if ($query->numRows() != 1) {

				$query = $db->storedQuery(CS_SQL_SELECT_USER_BY_EMAIL, array('email' => $username))->execQuery();
				if ($query->isError()) {
					$ret->addMsg("Unable to load the user information.");

					if (CS_ENABLE_DEV_MODE) {
						$ret->addMsg($query->fetchError());
						$ret->addMsg($query->query);
					}

					return($ret);
				}

				if ($query->numRows() != 1) {
					$ret->addMsg("Unable to locate the user with that username or email.");

					return($ret);
				}
			}

			$user = new chameleon_user($query->fetchResult(0, 'userId'));

			$user->confirm = $user->generateChars(16);
			if (strlen($user->confirm) != 16) {
				$ret->addMsg("Unable to generate a confirmation code.");

				return ($ret);
			}

			$query = $db->storedQuery(CS_SQL_UPDATE_USER_CONFIRM, array('confirm' => $user->confirm, 'userId' => $user->userId))->execQuery();
			if ($query->isError()) {
				$ret->addMsg("Unable to generate a confirmation code.");

				if (CS_ENABLE_DEV_MODE) {
					$ret->addMsg($query->fetchError());
					$ret->addMsg($query->query);
				}

				return($ret);
			}

			$admin = new chameleon_user(1);

			$eTpl = new n2f_template('dynamic');
			$eTpl->setModule('main')->setFile('forgot_email');
			$eTpl->setField('user', $user);
			$eTpl->setField('link', CS_SITE_PATH."admin/?nmod=main&page=reset&confirm=".$user->confirm);

			// Send the reset email
			if (!chameleon::mail($user->email, "Password Reset", $eTpl->render()->fetch(), "From:".$admin->email."\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n")) {
				$ret->isFail();
				$ret->addMsg('Unable to send the confirmation email.');

				return($ret);
			}

			$ret->isGood();
			$ret->addMsg('You should receive an email shortly.<br />Check your spam folder if you do not see it.');

			return($ret);
		}

		/**
		 * Function to reset the users password.
		 *
		 * @param chameleon_user $user       The username of the user
		 * @param string         $password   The new password
		 *
		 * @return n2f_return
		 */
		function doPasswordReset($user, $password) {
			$db = n2f_database::getInstance();
			$ret = new n2f_return();
			$ret->isFail();

			$user->salt = $user->generateSalt();
			$user->saltExpire = date("Y-m-d H:i:s", strtotime("+14 days"));
			$user->password = encStr($password . $user->salt);
			$user->confirm = null;

			$update = $user->update(true);
			if (!IsSuccess($update)) {
				return($update);
			}

			$query = $db->storedQuery(CS_SQL_UPDATE_USER_CONFIRM, array('confirm' => $user->confirm, 'userId' => $user->userId))->execQuery();
			if ($query->isError()) {
				if (CS_ENABLE_DEV_MODE) {
					$ret->addMsg("Unable to remove the confirmation code from your account.");
					$ret->addMsg($query->fetchError());
					$ret->addMsg($query->query);
				}
			}

			$ret->isGood();
			$ret->data = $user;

			return($ret);
		}

		/**
		 * Function to perform login process.
		 *
		 * @param string $username	String value of user's username value.
		 * @param string $password	String value of user's password value.
		 * @param boolean $rememberMe	Boolean TRUE or FALSE determining if user should be remembered or not.
		 * @return n2f_return		n2f_return object containing success/failure status and any extra data.
		 */
		function doLogin($username, $password, $rememberMe = false) {
			$db = n2f_database::getInstance();
			$ret = new n2f_return();
			$ret->isFail();

			$query = $db->storedQuery(CS_SQL_SELECT_USER_BY_USERNAME, array('username' => $username))->execQuery();

			if ($query->isError()) {
				$ret->addMsg("Failed login, check spelling and try again.");

				if (CS_ENABLE_DEV_MODE) {
					$ret->addMsg($query->fetchError());
					$ret->addMsg($query->query);
				}
			} else if ($query->numRows() != 1) {
				$ret->addMsg("Failed login, check spelling and try again.");

				if (CS_ENABLE_DEV_MODE) {
					$ret->addMsg("User not found.");
				}
			} else {
				$usr = new chameleon_user($query->fetchResult(0, 'userId'));

				if (encStr($password . $usr->salt) == $usr->password) {
					if (strtotime($usr->saltExpire) < time()) {
						$usr->salt = $usr->generateSalt();
						$usr->saltExpire = date('Y-m-d G:i:s', (time() + 2592000));
						$usr->password = encStr($password . $usr->salt);
						$usr->update(true);
					}

					if ($rememberMe !== false) {
						$key = md5('uid' . $usr->userId . '-' . microtime());
						$expire = (time() + 864000);

						$query = $db->storedQuery(CS_SQL_INSERT_USER_SESSION, array(
							'userId' => $usr->userId,
							'expires' => date('Y-m-d G:i:s', $expire),
							'remoteAddr' => $_SERVER['REMOTE_ADDR'],
							'remoteHost' => gethostbyaddr($_SERVER['REMOTE_ADDR']),
							'key' => $key
						))->execQuery();

						if (!$query->isError()) {
							setcookie(CS_REMEMBER_COOKIE, $key, $expire, '/', CS_COOKIE_DOMAIN);

							if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
								setcookie(CS_REMEMBER_COOKIE, $key, $expire, '/', CS_COOKIE_DOMAIN, 1);
							}
						}
					}

					$ret->isGood();
					$ret->data = $usr;
				} else {
					$ret->addMsg("Failed login, check spelling and try again.");

					if (CS_ENABLE_DEV_MODE) {
						$ret->addMsg("Invalid password.");
					}
				}
			}

			return($ret);
		}

		/**
		 * Function to perform logout process.
		 *
		 */
		function doLogout() {
			$c = chameleon::getInstance();
			$db = n2f_database::getInstance();
			$sess = n2f_session::getInstance();
			$authSess = n2f_session::getInstance(null, 'auth');

			$sess->delete('curr_user');
			$sess->delete('lasttime');
			$authSess->set('message', 'You have been logged out.');

			$query = $db->storedQuery(CS_SQL_SELECT_USER_SESSIONS, array('userId' => $c->user->userId))->execQuery();

			if (!$query->isError() && $query->numRows() > 0) {
				$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
				$sessions = $query->fetchRows();

				foreach (array_values($sessions) as $session) {
					if ($session['remoteAddr'] == $_SERVER['REMOTE_ADDR'] || $session['remoteHost'] == $host) {
						$db->storedQuery(CS_SQL_DELETE_USER_SESSION, array('sessionId' => $session['sessionId']))->execQuery();
						setcookie(CS_REMEMBER_COOKIE, '', -1, '/', CS_COOKIE_DOMAIN);
						setcookie(CS_REMEMBER_COOKIE, '', -1, '/', CS_COOKIE_DOMAIN, 1);
					}
				}
			}
		}

		/**
		 * Function to perform user permission check.
		 *
		 * @param string $package_key		Key for package which owns the permission.
		 * @param string $perm_key		Key for the permission.
		 * @param boolean $check_super	Boolean value to turn off using CS_PERMS_SUPER_ADMIN check.
		 * @return boolean				Boolean TRUE or FALSE based on the user having the permission.
		 */
		function checkUserPerm($package_key, $perm_key, $check_super = true) {
			return(chameleon::getInstance()->user->hasPerm($package_key, $perm_key, $check_super));
		}

		/**
		 * Function to simplify recover of a session-stored info_boxes object.
		 *
		 * @param n2f_session $sess		n2f_session object to search for info_boxes object.
		 * @param info_boxes $infoboxes	info_boxes object to recover into.
		 */
		function recoverInfoboxesFromSession(n2f_session $sess, info_boxes $infoboxes) {
			if ($sess->exists('infoboxes')) {
				$info = $sess->get('infoboxes');
				$sess->delete('infoboxes');

				if (count($info['errors']) > 0) {
					foreach (array_values($info['errors']) as $err) {
						$infoboxes->throwError('', $err['str'], '');
					}
				}

				if (count($info['notices']) > 0) {
					foreach (array_values($info['notices']) as $not) {
						$infoboxes->throwNotice('', $not['str'], '');
					}
				}

				if (count($info['successes']) > 0) {
					foreach (array_values($info['successes']) as $suc) {
						$infoboxes->throwSuccess('', $suc['str'], '');
					}
				}

				if (count($info['warnings']) > 0) {
					foreach (array_values($info['warnings']) as $war) {
						$infoboxes->throwWarning('', $war['str'], '');
					}
				}
			}

			return;
		}

		/**
		 * Function to simplify the storage of an info_boxes object into the session.
		 *
		 * @param n2f_session $sess		n2f_session object to store info_boxes object into.
		 * @param info_boxes $infoboxes	info_boxes object to retrieve from.
		 */
		function storeInfoboxesInSession(n2f_session $sess, info_boxes $infoboxes) {
			$sess->set('infoboxes', array(
				'errors' => $infoboxes->getErrors(),
				'notices' => $infoboxes->getNotices(),
				'successes' => $infoboxes->getSuccesses(),
				'warnings' => $infoboxes->getWarnings()
			));

			return;
		}

		/**
		 * Function to transfer n2f_return messages into the appropriate infobox group.
		 *
		 * @param info_boxes $infoboxes	info_boxes object to register information with.
		 * @param n2f_return $ret		n2f_return object with state information.
		 */
		function storeReturnInInfoboxes(info_boxes $infoboxes, n2f_return $ret) {
			if (!$ret->hasMsgs()) {
				return;
			}

			storeArrayInInfoboxes($infoboxes, $ret->msgs);

			return;
		}

		/**
		 * Function to transfer an array of strings into the error or success groups of an info_boxes object.
		 *
		 * @param info_boxes $infoboxes	info_boxes object to register strings with.
		 * @param array $messages		Array of strings to register with info_boxes object.
		 * @param boolean $asSuccess		Boolean value determining if strings should be successes or errors.
		 */
		function storeArrayInInfoboxes(info_boxes $infoboxes, array $messages, $asSuccess = false) {
			if (count($messages) < 1) {
				return;
			}

			foreach (array_values($messages) as $msg) {
				if ($asSuccess) {
					$infoboxes->throwSuccess('', $msg, '');
				} else {
					$infoboxes->throwError('', $msg, '');
				}
			}

			return;
		}

		// Pull in our extra extensions
		$n2f->loadExtension('chameleon/globals');
		$n2f->loadExtension('chameleon/schema');

		// Initialize our class
		chameleon::getInstance();
		$n2f->hookEvent(N2F_EVT_MODULES_INIT, array('chameleon', '_initModules'));
		$n2f->hookEvent(N2F_EVT_MODULE_INIT, array('chameleon', '_initModule'));
		$n2f->hookEvent(N2F_EVT_CORE_LOADED, array('chameleon', 'initUser'));
	} else if (stripos($_SERVER['REQUEST_URI'], 'setup/') === false) {
		$path = $_SERVER['REQUEST_URI'];

		if (stripos($path, 'admin/') !== false) {
			$path = '../';
		}

		if (stripos($path, '?') !== false) {
			$path = substr($path, 0, stripos($path, '?'));
		}

		if (substr($path, -1) != '/') {
			$path .= '/';
		}

		$path .= 'setup/';

		header("Location: {$path}");
		exit;
	}

?>