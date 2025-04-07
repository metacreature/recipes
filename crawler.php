<?php

if(preg_match('#\.(xml|txt)$#', $_SERVER['REQUEST_URI'])) {
	require_once('crawler.html');
	exit();
}
$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
if(strpos($host, 'amazonaws.com') !== false) {
    require_once('crawler.html');
    exit();
}

if (preg_match('#(ru|lu)$#i', $host) || (! empty($_SERVER['HTTP_REFERER']) && preg_match('#^[^\/]+[.](ru|lu)\/#', $_SERVER['HTTP_REFERER']))) {
    require_once('crawler.html');
    exit();
}

$url = trim(str_replace('\\', '/', $_SERVER['REQUEST_URI']), " \t\r\n/");
$url = preg_replace('/[\?#].*$/s', '', $url);
if ($url && $url != 'home') {
    $url = explode('/', $url);
    $url = $url[0];
    require_once ('_lib/global.inc.php');
    require_once ('_lib/fw/FW_ContentHandler.class.php');
    require_once('controller/Contents.cont.php');
    $methods = get_class_methods('Contents');
    if (!in_array('page_'.$url, $methods)) {
        require_once('crawler.html');
        exit();
    }
}