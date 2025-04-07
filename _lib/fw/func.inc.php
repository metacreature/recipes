<?php
define('REGEX_EMAIL', '#^[a-z0-9]+([_\.-][a-z0-9]+)*@[a-z0-9]+([_\.-][a-z0-9]+)*\.[a-z]{2,6}$#i');
define('REGEX_URL', '#^((http|https|ftp)\://)?[a-z0-9\-\.]+\.[a-z]{2,6}/?([a-z0-9 \-\._\?\,\'/\\\+&amp;%\$\#\=~])*$#i');
define('REGEX_PROTOCOL', '#^(http|https|ftp):\/\/#i');

define('FORMAT_DATE', 'd.m.Y');
define('FORMAT_TIME', 'H:i:s');
define('FORMAT_DATETIME', 'd.m.Y H:i:s');

// session functions
function my_session_start()
{
    if (empty($GLOBALS['bSessionStarted'])) {
        session_start();
        ignore_user_abort(true);
        setcookie('PHPSESSID', session_id(), null, '/', WEB_DOMAIN);
        $GLOBALS['bSessionStarted'] = true;
    }
}

function my_session_destroy()
{
    if (! empty($GLOBALS['bSessionStarted'])) {
        session_destroy();
        ignore_user_abort(true);
        $GLOBALS['bSessionStarted'] = false;
    }
}

function iniUserKey()
{
    $sUserKey = ! empty($_COOKIE[APP_KEY . '_usk']) ? $_COOKIE[APP_KEY . '_usk'] : (! empty($_GET[APP_KEY . '_usk']) ? $_GET[APP_KEY . '_usk'] : '');
    if (! $sUserKey && empty($_SESSION[APP_KEY . '_user_key'])) {
        $_SESSION[APP_KEY . '_user_key'] = uniqid();
    } else if (preg_match('#^[a-zA-Z0-9]{5,30}$#', $sUserKey)) {
        if (empty($_SESSION[APP_KEY . '_user_key'])) {
            $_SESSION[APP_KEY . '_user_key'] = $sUserKey;
        }
    } else if (empty($_SESSION[APP_KEY . '_user_key'])) {
        $_SESSION[APP_KEY . '_user_key'] = uniqid();
    }
    setcookie(APP_KEY . '_usk', $_SESSION[APP_KEY . '_user_key'], time() + 3600 * 24 * 700, '/', WEB_DOMAIN);
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);

        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (filetype($dir . '/' . $object) == 'dir') {
                    rrmdir($dir . '/' . $object);
                } else {
                    unlink($dir . '/' . $object);
                }
            }
        }

        reset($objects);
        rmdir($dir);
    }
}

// security
function getIdSecureLink($id)
{
    return $id . '&id_secure=' . md5($id . SECURE_SALT . $id);
}

function checkIdSecureLink($id)
{
    if (empty($_GET['id_secure']) || $_GET['id_secure'] !== md5($id . SECURE_SALT . $id)) {
        return false;
    } else {
        return true;
    }
}

function replaceTrackingLinks($sText)
{
    return preg_replace_callback('#href="([^"]+)"#iU', '_replaceTrackingLinksCallback', $sText);
}

function _replaceTrackingLinksCallback($matches)
{
    return 'href="' . WEB_ROOT . '/l.php' . HOTLINK_PROTECTION . '&t=link&l=' . rawurlencode($matches[1]) . '"';
}

// other functions
function cloneObj($oObj)
{
    if (is_object($oObj)) {
        return clone $oObj;
    }
    return $oObj;
}

function mb_trim(&$sString)
{
    return trim($sString);
}

function xssProtect(&$sString)
{
    return htmlspecialchars($sString, ENT_COMPAT, 'UTF-8');
}

function preint_r($mValue)
{
    echo '<pre>';
    print_r($mValue);
    echo '</pre>';
}
