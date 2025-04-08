<?php

require_once ('_lib/global.inc.php');

$db = FW_MySQLDataBaseLayer::singleton();
ignore_user_abort(true);

// get request
$url = str_replace('\\', '/', $_SERVER['REQUEST_URI']);
$url = trim(preg_replace('/[\?#].*$/s', '', $url), " \t\r\n/");
$slashpos = strrpos($url, '/');

// get controller and function
$controller_request = substr($url, 0, $slashpos === false ? null : $slashpos);

if ($url && preg_match('#^[a-z_\/]+$#', $url) && file_exists('controller/' . $url  . '.cont.php')) {
	$controller_file = 'controller/' . $url;
	$function = 'view';
} elseif ($controller_request && preg_match('#^[a-z_\/]+$#', $controller_request) && file_exists('controller/' . $controller_request  . '.cont.php')) {
	$controller_file = 'controller/' . $controller_request;
	$function = $slashpos === false ? '' : substr($url, $slashpos + 1);
} else if (!$controller_request) {
	$controller_file = 'controller/' . LANDING_PAGE;
	$function = 'view';
} else {
	die('controller "' . $controller_request . '" does not exist!');
}

$controller_name = implode('_', array_map('ucfirst', preg_split('#[\/_]#', $controller_file)));


// execute
@require_once($controller_file  . '.cont.php');
$obj_controller = new $controller_name($db);

if ($function && preg_match('#^[a-z_]+$#', $function) && method_exists($obj_controller, $function)) {
	ob_start();

	$result = [$obj_controller, $function]();

	if (is_string($result)) {
		header('Location: ' . $result);
		exit();
	} elseif (is_array($result)) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($result);
		ob_end_flush();
	} else {
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		ob_end_flush();
	}
} else {
	die('function "' . $function . '" does not exist!');
}

exit();


