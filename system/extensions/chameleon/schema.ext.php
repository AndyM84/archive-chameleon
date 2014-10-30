<?php

	$dbPrefix = CS_DATABASE_TABLE_PREFIX;

	// Generic queries
	define('CS_SQL_CHECK_TABLES',					'cs_check_tables');
	define('CS_SQL_DROP_TABLES',					'cs_drop_tables');

	// Package queries
	define('CS_SQL_CREATE_PACKAGES_TABLE',			'cs_create_packages_table');
	define('CS_SQL_INSERT_PACKAGE',				'cs_insert_package');
	define('CS_SQL_UPDATE_PACKAGE',				'cs_update_package');
	define('CS_SQL_DELETE_PACKAGE',				'cs_delete_package');
	define('CS_SQL_TOGGLE_PACKAGE_ACTIVE',			'cs_toggle_package_active');
	define('CS_SQL_SELECT_ALL_PACKAGES',			'cs_select_all_packages');
	define('CS_SQL_SELECT_ALL_PACKAGES_QUERY',		'cs_select_all_packages_query');
	define('CS_SQL_SELECT_ALL_PACKAGES_LIMIT',		'cs_select_all_packages_limit');
	define('CS_SQL_SELECT_ALL_PACKAGES_COUNT',		'cs_select_all_packages_count');
	define('CS_SQL_SELECT_ACTIVE_PACKAGES',			'cs_select_active_packages');
	define('CS_SQL_SELECT_INACTIVE_PACKAGES',		'cs_select_inactive_packages');
	define('CS_SQL_SELECT_PACKAGE_BY_KEY',			'cs_select_package_by_key');
	define('CS_SQL_SELECT_PACKAGE_BY_ID',			'cs_select_package_by_id');
	define('CS_SQL_SELECT_PACKAGE_SEARCH',			'cs_select_package_search');
	define('CS_SQL_SELECT_PACKAGE_SEARCH_LIMIT',		'cs_select_package_search_limit');
	define('CS_SQL_SELECT_PACKAGE_SEARCH_COUNT',		'cs_select_package_search_count');

	// Settings queries
	define('CS_SQL_CREATE_SETTINGS_TABLE',			'cs_create_settings_table');
	define('CS_SQL_CREATE_SETTINGS_LOG_TABLE',		'cs_create_settings_log_table');
	define('CS_SQL_INSERT_SETTING',				'cs_insert_setting');
	define('CS_SQL_UPDATE_SETTING',				'cs_update_setting');
	define('CS_SQL_DELETE_SETTING',				'cs_delete_setting');
	define('CS_SQL_SELECT_SETTING',				'cs_select_setting');
	define('CS_SQL_INSERT_SETTING_LOG',			'cs_insert_setting_log');
	define('CS_SQL_SELECT_SETTING_LOGS',			'cs_select_setting_logs');
	define('CS_SQL_SELECT_SETTING_LOGS_BY_KEY',		'cs_select_setting_logs_by_key');

	// Skin queries
	define('CS_SQL_CREATE_SKINS_TABLE',			'cs_create_skins_table');
	define('CS_SQL_INSERT_SKIN',					'cs_insert_skin');
	define('CS_SQL_UPDATE_SKIN',					'cs_update_skin');
	define('CS_SQL_DELETE_SKIN',					'cs_delete_skin');
	define('CS_SQL_SELECT_SKIN',					'cs_select_skin');
	define('CS_SQL_SELECT_SKIN_BY_SKIN',			'cs_select_skin_by_skin');
	define('CS_SQL_SELECT_SKIN_BY_KEY',			'cs_select_skin_by_key');
	define('CS_SQL_SELECT_ALL_SKINS',				'cs_select_all_skins');
	define('CS_SQL_SELECT_ALL_SKINS_QUERY',			'cs_select_all_skins_query');
	define('CS_SQL_SELECT_ALL_SKINS_LIMIT',			'cs_select_all_skins_limit');
	define('CS_SQL_SELECT_ALL_SKINS_COUNT',			'cs_select_all_skins_count');
	define('CS_SQL_SELECT_SKIN_SEARCH',			'cs_select_skin_search');
	define('CS_SQL_SELECT_SKIN_SEARCH_LIMIT',		'cs_select_skin_search_limit');
	define('CS_SQL_SELECT_SKIN_SEARCH_COUNT',		'cs_select_skin_search_count');

	// User/permission queries
	define('CS_SQL_CREATE_USERS_TABLE',			'cs_create_users_table');
	define('CS_SQL_CREATE_USERS_LOG_TABLE',			'cs_create_users_log_table');
	define('CS_SQL_CREATE_USERPERMS_TABLE',			'cs_create_userperms_table');
	define('CS_SQL_CREATE_USERPERMS_LOG_TABLE',		'cs_create_userperms_log_table');
	define('CS_SQL_CREATE_PERMS_TABLE',			'cs_create_perms_table');
	define('CS_SQL_CREATE_PERMS_LOG_TABLE',			'cs_create_perms_log_table');
	define('CS_SQL_CREATE_USERSESS_TABLE',			'cs_create_user_session_table');
	define('CS_SQL_INSERT_USER',					'cs_insert_user');
	define('CS_SQL_UPDATE_USER',					'cs_update_user');
	define('CS_SQL_UPDATE_USER_CONFIRM',	'cs_update_user_confirm');
	define('CS_SQL_SELECT_USER_BY_CONFIRM',	'cs_select_user_by_confirm');
	define('CS_SQL_DELETE_USER',					'cs_delete_user');
	define('CS_SQL_SELECT_ALL_USERS',				'cs_select_all_users');
	define('CS_SQL_SELECT_ALL_USERS_QUERY',			'cs_select_all_users_query');
	define('CS_SQL_SELECT_ALL_USERS_LIMIT',			'cs_select_all_users_limit');
	define('CS_SQL_SELECT_ALL_USERS_COUNT',			'cs_select_all_users_count');
	define('CS_SQL_SELECT_ALL_USERS_BY_STATUS',		'cs_select_all_users_by_status');
	define('CS_SQL_SELECT_ALL_USERS_BY_STATUS_QUERY',	'cs_select_all_users_by_status_query');
	define('CS_SQL_SELECT_ALL_USERS_BY_STATUS_LIMIT',	'cs_select_all_users_by_status_limit');
	define('CS_SQL_SELECT_ALL_USERS_BY_STATUS_COUNT',	'cs_select_all_users_by_status_count');
	define('CS_SQL_SELECT_USERS_SEARCH',			'cs_select_users_search');
	define('CS_SQL_SELECT_USERS_SEARCH_LIMIT',		'cs_select_users_search_limit');
	define('CS_SQL_SELECT_USERS_SEARCH_COUNT',		'cs_select_users_search_count');
	define('CS_SQL_SELECT_USER_BY_ID',				'cs_select_user_by_id');
	define('CS_SQL_SELECT_USER_BY_USERNAME',		'cs_select_user_by_username');
	define('CS_SQL_SELECT_USER_BY_EMAIL',			'cs_select_user_by_email');
	define('CS_SQL_INSERT_USER_LOG',				'cs_insert_user_log');
	define('CS_SQL_SELECT_USER_LOGS',				'cs_select_user_logs');
	define('CS_SQL_SELECT_USER_LOGS_BY_USERID',		'cs_select_user_logs_by_userid');
	define('CS_SQL_INSERT_USERPERM',				'cs_insert_userperm');
	define('CS_SQL_UPDATE_USERPERM',				'cs_update_userperm');
	define('CS_SQL_DELETE_USERPERM',				'cs_delete_userperm');
	define('CS_SQL_DELETE_ALL_USERPERMS_BY_USERID',	'cs_delete_all_userperms_by_userid');
	define('CS_SQL_DELETE_ALL_USERPERMS_BY_PERMID',	'cs_delete_all_userperms_by_permid');
	define('CS_SQL_SELECT_ALL_USERPERMS',			'cs_select_all_userperms');
	define('CS_SQL_SELECT_USERPERM_BY_USERID',		'cs_select_userperm_by_userid');
	define('CS_SQL_SELECT_USERPERMS_BY_USERID',		'cs_select_userperms_by_userid');
	define('CS_SQL_INSERT_USERPERM_LOG',			'cs_insert_userperm_log');
	define('CS_SQL_SELECT_USERPERM_LOGS',			'cs_select_userperm_logs');
	define('CS_SQL_SELECT_USERPERM_LOGS_BY_USERID',	'cs_select_userperm_logs_by_userid');
	define('CS_SQL_INSERT_PERM',					'cs_insert_perm');
	define('CS_SQL_UPDATE_PERM',					'cs_update_perm');
	define('CS_SQL_DELETE_PERM',					'cs_delete_perm');
	define('CS_SQL_DELETE_ALL_PERMS_BY_PKGID',		'cs_delete_all_perms_by_pkgid');
	define('CS_SQL_SELECT_ALL_PERMS',				'cs_select_all_perms');
	define('CS_SQL_SELECT_ALL_PERMS_BY_PKGID',		'cs_select_all_perms_by_pkgid');
	define('CS_SQL_SELECT_ALL_PERMS_WITH_PKG',		'cs_select_all_perms_with_pkg');
	define('CS_SQL_SELECT_ALL_PERMS_QUERY',			'cs_select_all_perms_query');
	define('CS_SQL_SELECT_ALL_PERMS_LIMIT',			'cs_select_all_perms_limit');
	define('CS_SQL_SELECT_ALL_PERMS_COUNT',			'cs_select_all_perms_count');
	define('CS_SQL_SELECT_PERMS_BY_PACKAGEID',		'cs_select_perms_by_packageid');
	define('CS_SQL_SELECT_PERM_BY_PACKAGEKEY',		'cs_select_perm_by_packagekey');
	define('CS_SQL_SELECT_PERMS_BY_PACKAGEKEY',		'cs_select_perms_by_packagekey');
	define('CS_SQL_INSERT_PERM_LOG',				'cs_insert_perm_log');
	define('CS_SQL_SELECT_PERM_LOGS',				'cs_select_perm_logs');
	define('CS_SQL_SELECT_PERM_LOGS_BY_ID',			'cs_select_perm_logs_by_id');
	define('CS_SQL_INSERT_USER_SESSION',			'cs_insert_user_session');
	define('CS_SQL_UPDATE_USER_SESSION',			'cs_update_user_session');
	define('CS_SQL_DELETE_USER_SESSION',			'cs_delete_user_session');
	define('CS_SQL_SELECT_USER_SESSION_BY_KEY',		'cs_select_user_session_by_key');
	define('CS_SQL_SELECT_USER_SESSIONS',			'cs_select_user_sessions');



	////////////
	// MySQLi //
	////////////

	// Generic queries
	n2f_database::storeQuery(CS_SQL_CHECK_TABLES, 'mysqli', "SHOW TABLES LIKE '{$dbPrefix}%'");
	n2f_database::storeQuery(CS_SQL_DROP_TABLES, 'mysqli',
						"DROP TABLE `{$dbPrefix}cs_packages`".
						", `{$dbPrefix}cs_settings`".
						", `{$dbPrefix}cs_settings_log`".
						", `{$dbPrefix}cs_skins`".
						", `{$dbPrefix}cs_users`".
						", `{$dbPrefix}cs_users_log`".
						", `{$dbPrefix}cs_userperms`".
						", `{$dbPrefix}cs_userperms_log`".
						", `{$dbPrefix}cs_perms`".
						", `{$dbPrefix}cs_perms_log`".
						", `{$dbPrefix}cs_user_sessions`"
	);

	// Package queries
	n2f_database::storeQuery(CS_SQL_CREATE_PACKAGES_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_packages` (
			`packageId` smallint(4) unsigned not null auto_increment,
			`key` varchar(50) not null,
			`modules` text not null,
			`extensions` text not null,
			`files` longtext not null,
			`baseExt` varchar(150) not null,
			`startMod` varchar(150) not null,
			`errorMod` varchar(150) not null,
			`name` varchar(35) not null,
			`author` varchar(150) null,
			`description` text null,
			`url` varchar(350) null,
			`version` varchar(15) not null,
			`installed` datetime not null default '0000-00-00 00:00:00',
			`upgradeFrom` varchar(5) not null default '0',
			`active` tinyint(1) unsigned not null default '0',
			PRIMARY KEY (`packageId`),
			INDEX `key` (`key`(25),`active`),
			INDEX `active` (`active`,`key`(25))
		)");
	n2f_database::storeQuery(CS_SQL_INSERT_PACKAGE, 'mysqli', "INSERT INTO `{$dbPrefix}cs_packages` (`key`, `modules`, `extensions`, `files`, `baseExt`, `startMod`, `errorMod`, `name`, `author`, `description`, `url`, `version`, `installed`, `upgradeFrom`, `active`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_UPDATE_PACKAGE, 'mysqli', "UPDATE `{$dbPrefix}cs_packages` SET `key` = ?, `modules` = ?, `extensions` = ?, `files` = ?, `baseExt` = ?, `startMod` = ?, `errorMod` = ?, `name` = ?, `author` = ?, `description` = ?, `url` = ?, `version` = ?, `upgradeFrom` = ?, `active` = ? WHERE `packageId` = ?", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_PACKAGE, 'mysqli', "DELETE FROM `{$dbPrefix}cs_packages` WHERE `packageId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_TOGGLE_PACKAGE_ACTIVE, 'mysqli', "UPDATE `{$dbPrefix}cs_packages` SET `active` = ? WHERE `packageId` = ?", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PACKAGES, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages`");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PACKAGES_QUERY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PACKAGES_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PACKAGES_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_packages`");
	n2f_database::storeQuery(CS_SQL_SELECT_ACTIVE_PACKAGES, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` WHERE `active` = 1");
	n2f_database::storeQuery(CS_SQL_SELECT_INACTIVE_PACKAGES, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` WHERE `active` = 0");
	n2f_database::storeQuery(CS_SQL_SELECT_PACKAGE_BY_KEY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` WHERE `key` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_PACKAGE_BY_ID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` WHERE `packageId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_PACKAGE_SEARCH, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` WHERE `key` LIKE ? OR `name` LIKE ? OR `author` LIKE ? OR `description` LIKE ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_PACKAGE_SEARCH_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_packages` WHERE `key` LIKE ? OR `name` LIKE ? OR `author` LIKE ? OR `description` LIKE ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_PACKAGE_SEARCH_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_packages` WHERE `key` LIKE ? OR `name` LIKE ? OR `author` LIKE ? OR `description` LIKE ?", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));

	// Settings queries
	n2f_database::storeQuery(CS_SQL_CREATE_SETTINGS_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_settings` (
			`key` varchar(35) not null unique,
			`value` varchar(250) not null,
			PRIMARY KEY (`key`)
		)");
	n2f_database::storeQuery(CS_SQL_CREATE_SETTINGS_LOG_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_settings_log` (
			`logId` bigint(15) unsigned not null auto_increment,
			`key` varchar(35) not null,
			`value` varchar(250) not null,
			`logged` datetime not null,
			PRIMARY KEY (`logId`)
		)");
	n2f_database::storeQuery(CS_SQL_INSERT_SETTING, 'mysqli', "INSERT INTO `{$dbPrefix}cs_settings` (`key`, `value`) VALUES (?, ?)", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_UPDATE_SETTING, 'mysqli', "UPDATE `{$dbPrefix}cs_settings` SET `value` = ? WHERE `key` = ?", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_DELETE_SETTING, 'mysqli', "DELETE FROM `{$dbPrefix}cs_settings` WHERE `key` = ? LIMIT 1", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_SETTING, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_settings` WHERE `key` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_INSERT_SETTING_LOG, 'mysqli', "INSERT INTO `{$dbPrefix}cs_settings_log` (`key`, `value`, `logged`) VALUES (?, ?, ?)", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_SETTING_LOGS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_settings_log`");
	n2f_database::storeQuery(CS_SQL_SELECT_SETTING_LOGS_BY_KEY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_settings_log` WHERE `key` = ?", array(N2F_DBTYPE_STRING));

	// Skin queries
	n2f_database::storeQuery(CS_SQL_CREATE_SKINS_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_skins` (
			`skinId` smallint(3) unsigned not null auto_increment,
			`key` varchar(50) not null,
			`skin` varchar(50) not null,
			PRIMARY KEY (`skinId`),
			INDEX `key` (`key`(25),`skinId`)
		)");
	n2f_database::storeQuery(CS_SQL_INSERT_SKIN, 'mysqli', "INSERT INTO `{$dbPrefix}cs_skins` (`key`, `skin`) VALUES (?, ?)", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_UPDATE_SKIN, 'mysqli', "UPDATE `{$dbPrefix}cs_skins` SET `key` = ?, `skin` = ? WHERE `skinId` = ?", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_SKIN, 'mysqli', "DELETE FROM `{$dbPrefix}cs_skins` WHERE `skinId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_SKIN, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins` WHERE `skinId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_SKIN_BY_SKIN, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins` WHERE `skin` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_SKIN_BY_KEY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins` WHERE `key` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_SKINS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins`");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_SKINS_QUERY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_SKINS_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_SKINS_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_skins`");
	n2f_database::storeQuery(CS_SQL_SELECT_SKIN_SEARCH, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins` WHERE `key` LIKE ? OR `skin` LIKE ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_SKIN_SEARCH_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_skins` WHERE `key` LIKE ? OR `skin` LIKE ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_SKIN_SEARCH_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_skins` WHERE `key` LIKE ? OR `skin` LIKE ?", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));

	// User/permission queries
	n2f_database::storeQuery(CS_SQL_CREATE_USERS_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_users` (
			`userId` int(6) unsigned not null auto_increment,
			`username` varchar(45) not null,
			`email` varchar(350) not null,
			`password` varchar(256) not null,
			`salt` varchar(64) not null,
			`saltExpire` datetime not null,
			`dateJoined` datetime not null,
			`confirm` varchar(16),
			`status` tinyint(1) unsigned not null default '0',
			PRIMARY KEY (`userId`),
			INDEX `uname` (`username`(15),`status`),
			INDEX `joined` (`userId`,`dateJoined`,`status`),
			INDEX `email` (`email`(35),`status`),
			INDEX `confirm` (`confirm`(16),`status`)
		)");
	n2f_database::storeQuery(CS_SQL_CREATE_USERS_LOG_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_users_log` (
			`logId` bigint(10) unsigned not null auto_increment,
			`userId` int(6) unsigned not null,
			`username` varchar(45) not null,
			`email` varchar(350) not null,
			`password` varchar(256) not null,
			`salt` varchar(64) not null,
			`saltExpire` datetime not null,
			`dateJoined` datetime not null,
			`status` tinyint(1) unsigned not null,
			`dateLogged` datetime not null,
			PRIMARY KEY (`logId`),
			INDEX `user` (`userId`)
		)");
	n2f_database::storeQuery(CS_SQL_CREATE_USERPERMS_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_userperms` (
			`upId` int(7) unsigned not null auto_increment,
			`permId` smallint(4) unsigned not null,
			`userId` int(6) unsigned not null,
			`granted` datetime not null,
			PRIMARY KEY (`upId`),
			INDEX `user` (`userId`,`granted`)
		)");
	n2f_database::storeQuery(CS_SQL_CREATE_USERPERMS_LOG_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_userperms_log` (
			`logId` bigint(13) unsigned not null auto_increment,
			`upId` int(7) unsigned not null,
			`permId` smallint(4) unsigned not null,
			`userId` int(6) unsigned not null,
			`granted` datetime not null,
			`logged` datetime not null,
			PRIMARY KEY (`logId`),
			INDEX `user` (`userId`)
		)");
	n2f_database::storeQuery(CS_SQL_CREATE_PERMS_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_perms` (
			`permId` smallint(4) unsigned not null auto_increment,
			`packageId` smallint(4) unsigned not null,
			`key` varchar(50) not null,
			`label` varchar(125) not null,
			PRIMARY KEY (`permId`),
			INDEX `package` (`packageId`),
			INDEX `key` (`key`(15),`packageId`)
		)");
	n2f_database::storeQuery(CS_SQL_CREATE_PERMS_LOG_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_perms_log` (
			`logId` int(7) unsigned not null auto_increment,
			`permId` smallint(4) unsigned not null,
			`packageId` smallint(4) unsigned not null,
			`key` varchar(50) not null,
			`label` varchar(125) not null,
			`logged` datetime not null,
			PRIMARY KEY (`logId`),
			INDEX `perm` (`permId`,`logged`),
			INDEX `package` (`packageId`,`logged`)
		)");
	n2f_database::storeQuery(CS_SQL_CREATE_USERSESS_TABLE, 'mysqli',
						"CREATE TABLE `{$dbPrefix}cs_user_sessions` (
			`sessionId` bigint(10) unsigned not null auto_increment,
			`userId` int(6) unsigned not null,
			`expires` datetime not null,
			`remoteAddr` varchar(45) null,
			`remoteHost` varchar(250) null,
			`key` varchar(64) not null,
			primary key (`sessionId`)
		)");
	n2f_database::storeQuery(CS_SQL_INSERT_USER, 'mysqli', "INSERT INTO `{$dbPrefix}cs_users` (`username`, `email`, `password`, `salt`, `saltExpire`, `dateJoined`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_UPDATE_USER, 'mysqli', "UPDATE `{$dbPrefix}cs_users` SET `username` = ?, `email` = ?, `password` = ?, `salt` = ?, `saltExpire` = ?, `dateJoined` = ?, `status` = ? WHERE `userId` = ?", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_UPDATE_USER_CONFIRM, 'mysqli', "UPDATE `{$dbPrefix}cs_users` SET `confirm` = ? WHERE `userId` = ?", array(N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_USER_BY_CONFIRM, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `confirm` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_DELETE_USER, 'mysqli', "DELETE FROM `{$dbPrefix}cs_users` WHERE `userId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users`");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS_QUERY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_users`");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS_BY_STATUS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `status` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS_BY_STATUS_QUERY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `status` = ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS_BY_STATUS_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `status` = ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERS_BY_STATUS_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_users` WHERE `status` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_USERS_SEARCH, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `username` LIKE ? OR `email` LIKE ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_USERS_SEARCH_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `username` LIKE ? OR `email` LIKE ? ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_USERS_SEARCH_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_users` WHERE `username` LIKE ? OR `email` LIKE ?", array(N2F_DBTYPE_LIKE_STRING, N2F_DBTYPE_LIKE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_USER_BY_ID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `userId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_USER_BY_USERNAME, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `username` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_USER_BY_EMAIL, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users` WHERE `email` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_INSERT_USER_LOG, 'mysqli', "INSERT INTO `{$dbPrefix}cs_users_log` (`userId`, `username`, `email`, `password`, `salt`, `saltExpire`, `dateJoined`, `status`, `dateLogged`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_USER_LOGS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users_log`");
	n2f_database::storeQuery(CS_SQL_SELECT_USER_LOGS_BY_USERID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_users_log` WHERE `userId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_INSERT_USERPERM, 'mysqli', "INSERT INTO `{$dbPrefix}cs_userperms` (`permId`, `userId`, `granted`) VALUES (?, ?, ?)", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_UPDATE_USERPERM, 'mysqli', "UPDATE `{$dbPrefix}cs_userperms` SET `permId` = ?, `userId` = ?, `granted` = ? WHERE `upId` = ?", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_USERPERM, 'mysqli', "DELETE FROM `{$dbPrefix}cs_userperms` WHERE `upId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_ALL_USERPERMS_BY_USERID, 'mysqli', "DELETE FROM `{$dbPrefix}cs_userperms` WHERE `userId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_ALL_USERPERMS_BY_PERMID, 'mysqli', "DELETE FROM `{$dbPrefix}cs_userperms` WHERE `permId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_USERPERMS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_userperms`");
	n2f_database::storeQuery(CS_SQL_SELECT_USERPERM_BY_USERID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_userperms` WHERE `userId` = ? AND `permId` = ?", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_USERPERMS_BY_USERID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_userperms` WHERE `userId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_INSERT_USERPERM_LOG, 'mysqli', "INSERT INTO `{$dbPrefix}cs_userperms_log` (`upId`, `permId`, `userId`, `granted`, `logged`) VALUES (?, ?, ?, ?, ?)", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_USERPERM_LOGS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_userperms_log`");
	n2f_database::storeQuery(CS_SQL_SELECT_USERPERM_LOGS_BY_USERID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_userperms_log` WHERE `userId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_INSERT_PERM, 'mysqli', "INSERT INTO `{$dbPrefix}cs_perms` (`packageId`, `key`, `label`) VALUES (?, ?, ?)", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_UPDATE_PERM, 'mysqli', "UPDATE `{$dbPrefix}cs_perms` SET `packageId` = ?, `key` = ?, `label` = ? WHERE `permId` = ?", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_PERM, 'mysqli', "DELETE FROM `{$dbPrefix}cs_perms` WHERE `permId` = ? LIMIT 1", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_ALL_PERMS_BY_PKGID, 'mysqli', "DELETE FROM `{$dbPrefix}cs_perms` WHERE `packageId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PERMS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_perms`");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PERMS_BY_PKGID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_perms` WHERE `packageId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PERMS_WITH_PKG, 'mysqli', "SELECT pkgs.key AS pkg_key, pkgs.name AS name, perm.* FROM `{$dbPrefix}cs_perms` AS perm INNER JOIN `{$dbPrefix}cs_packages` AS pkgs ON pkgs.packageId = perm.packageId WHERE pkgs.active = 1");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PERMS_QUERY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_perms` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PERMS_LIMIT, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_perms` ORDER BY _%ORDER_BY%_ _%ORDER_DIR%_ LIMIT _%OFFSET%_,_%LIMIT%_");
	n2f_database::storeQuery(CS_SQL_SELECT_ALL_PERMS_COUNT, 'mysqli', "SELECT COUNT(*) FROM `{$dbPrefix}cs_perms`");
	n2f_database::storeQuery(CS_SQL_SELECT_PERMS_BY_PACKAGEID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_perms` WHERE `packageId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_PERM_BY_PACKAGEKEY, 'mysqli', "SELECT perm.* FROM `{$dbPrefix}cs_perms` AS perm INNER JOIN `{$dbPrefix}cs_packages` AS pkgs ON pkgs.packageId = perm.packageId WHERE pkgs.active = 1 AND pkgs.key = ? AND perm.key = ?", array(N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_PERMS_BY_PACKAGEKEY, 'mysqli', "SELECT perm.* FROM `{$dbPrefix}cs_perms` AS perm INNER JOIN `{$dbPrefix}cs_packages` AS pkgs ON pkgs.packageId = perm.packageId WHERE pkgs.active = 1 AND pkgs.key = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_INSERT_PERM_LOG, 'mysqli', "INSERT INTO `{$dbPrefix}cs_perms_log` (`permId`, `packageId`, `key`, `label`, `logged`) VALUES (?, ?, ?, ?, ?)", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_PERM_LOGS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_perms_log`");
	n2f_database::storeQuery(CS_SQL_SELECT_PERM_LOGS_BY_ID, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_perms_log` WHERE `permId` = ?", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_INSERT_USER_SESSION, 'mysqli', "INSERT INTO `{$dbPrefix}cs_user_sessions` (`userId`, `expires`, `remoteAddr`, `remoteHost`, `key`) VALUES (?, ?, ?, ?, ?)", array(N2F_DBTYPE_INTEGER, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING, N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_UPDATE_USER_SESSION, 'mysqli', "UPDATE `{$dbPrefix}cs_user_sessions` SET `expires` = ? WHERE `sessionId` = ?", array(N2F_DBTYPE_STRING, N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_DELETE_USER_SESSION, 'mysqli', "DELETE FROM `{$dbPrefix}cs_user_sessions` WHERE `sessionId` = ? LIMIT 1", array(N2F_DBTYPE_INTEGER));
	n2f_database::storeQuery(CS_SQL_SELECT_USER_SESSION_BY_KEY, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_user_sessions` WHERE `key` = ?", array(N2F_DBTYPE_STRING));
	n2f_database::storeQuery(CS_SQL_SELECT_USER_SESSIONS, 'mysqli', "SELECT * FROM `{$dbPrefix}cs_user_sessions` WHERE `userId` = ?", array(N2F_DBTYPE_INTEGER));

	////////////
	// MySQLi //
	////////////

?>