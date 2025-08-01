<?php
/*
 File: settings.inc.php
 Copyright (c) 2025 Clemens K. (https://github.com/metacreature)
 
 MIT License
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.
*/

define('TEST_SERVER', strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);

// settings
define('SETTINGS_ALLOW_REGISTER', true);
define('SETTINGS_LIST_REQUIRES_LOGIN', false);
define('SETTINGS_REMEMBER_LOGIN_USE_IP', false);
define('SETTINGS_REMEMBER_LOGIN_USE_USER_AGENT', false);
define('SETTINGS_REMEMBER_LOGIN_EXPIRE', 365);
define('SETTINGS_DEFAULT_LANG', 'en');
define('SETTINGS_CURRENCY', '€');


// Email-config
define('EMAIL_FROM_NAME', 'Recipes by Metacreature');
define('EMAIL_FROM_MAIL', 'no-reply@metacreature.com');
define('EMAIL_RETURN_PATH', 'metacreature@metacreature.com');
define('EMAIL_GREETING_NAME', 'Metacreature');



// FW-Config
define('USER_TIMEZONE', 'Europe/Vienna');
define('SECURE_SALT', '18dd27efc08342874wgdfhdhz28265203e964c9');
define('HIDDEN_IMAGEFOLDER_SECURE', 'kjdfgutalocveabta');


if (TEST_SERVER) {
	
	// db-config
	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'metacreature');
	define('DB_PASSWORD', 'jhdfd865#838383');
	define('DB_NAME', 'recipes');
	define('DB_PERSISTENT', false);
	
	// error-logging
    define('DEBUG_MODE', true);
	define('DEBUG_EXECUTION_TIME', true);
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', true);

	// domain
    define('WEB_URL', 'http://recipes.localhost');
    define('WEB_DOMAIN', 'recipes.localhost');
} else {
	
	// db-config
	define('DB_HOST', 'localhost');
	define('DB_USERNAME', 'domain_metacreature');
	define('DB_PASSWORD', 'hkzuyzhjyd');
	define('DB_NAME', 'domain_recipes');
	define('DB_PERSISTENT', false);
	
	// error-logging
    define('DEBUG_MODE', false);
	define('DEBUG_EXECUTION_TIME', false);
    ini_set('error_reporting', 0);
    ini_set('display_errors', false);

	// domain
    define('WEB_URL', 'https://recipes.metacreature.com');
    define('WEB_DOMAIN', 'recipes.metacreature.com');
}

// other (don't modify)
define('SETTINGS_LANDING_PAGE', '/recipes/list');
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].(TEST_SERVER ? '' : ''));
define('GALLERY_ROOT', DOCUMENT_ROOT . '/gallery');
define('WEB_ROOT', '');
define('GALLERY_WEB_ROOT', '/gallery');

date_default_timezone_set(USER_TIMEZONE);





