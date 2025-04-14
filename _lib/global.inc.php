<?php

// FW-Includes
require_once ('fw/func.inc.php');
require_once ('fw/FW_ErrorLogger.static.php');
require_once ('fw/FW_MySQLDataBaseLayer.class.php');

require_once ('language/german.lang.php');

// settings
define('SETTINGS_LANDING_PAGE', 'recipes/list');
define('SETTINGS_ALLOW_REGISTER', false);
define('SETTINGS_LIST_REQUIRES_LOGIN', false);



// FW-Config
define('TEST_SERVER', strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);
define('USER_TIMEZONE', 'Europe/Vienna');
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].(TEST_SERVER ? '' : '/rezepte'));
define('GALLERY_ROOT', DOCUMENT_ROOT . '/gallery');
define('WEB_ROOT', '');
define('GALLERY_WEB_ROOT', '/gallery');
define('SECURE_SALT', '18dd27efc08342874wgdfhdhz28265203e964c9');
define('HIDDEN_IMAGEFOLDER_SECURE', 'kjdfgutalocveabta');



// config
if (TEST_SERVER) {
	
	// db-config
	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'metacreature');
	define('DB_PASSWORD', 'jhdfd865#838383');
	define('DB_NAME', 'rezepte');
	define('DB_PERSISTENT', false);
	
    define('DEBUG_MODE', true);
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', true);
    define('WEB_URL', 'http://rezepte.localhost');
    define('WEB_DOMAIN', 'rezepte.localhost');
} else {
	
	// db-config
	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'domain_metacreature');
	define('DB_PASSWORD', 'hkzuyzhjyd');
	define('DB_NAME', 'domain_rezepte');
	define('DB_PERSISTENT', false);
	
    define('DEBUG_MODE', false);
    ini_set('error_reporting', 0);
    ini_set('display_errors', false);
    define('WEB_URL', 'https://rezepte.metacreature.com');
    define('WEB_DOMAIN', 'rezepte.metacreature.com');
}

date_default_timezone_set(USER_TIMEZONE);





