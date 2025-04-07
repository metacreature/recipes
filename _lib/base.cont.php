<?php

require_once (DOCUMENT_ROOT . '/_lib/fw/FW_Ajax_Form.class.php');

class Controller_Base
{
    protected $_db = null;

    function __construct($db) {
        $this->_db = $db;
    }

    protected function _check_login() {
        @session_start();
        if (!in_array('login', $_SESSION) || !$_SESSION['login']) {
            @session_destroy();
            if (strpos($_SERVER['HTTP_ACCEPT'], 'text/json') === false) {
                header('Location: ' . WEB_URL);
            } else {
                header('Content-Type: application/json; charset=utf-8');
		        echo json_encode(['error' => 'Not logged in!']);
		        ob_end_flush();
            }
            exit;
        }
    }

    protected function _logout() {
        @session_start();
        @session_destroy();
    }
}