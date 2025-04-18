<?php

require_once (DOCUMENT_ROOT . '/_lib/fw/FW_Ajax_Form.class.php');

class Controller_Base
{
    protected $_db = null;

    function __construct($db) {
        @session_start();
        $this->_db = $db;
    }

    static function is_login() {
        return !empty($_SESSION) && in_array('login', $_SESSION) && $_SESSION['login'];
    }

    static function get_user_id() {
        return !empty($_SESSION) && in_array('user_id', $_SESSION) ? $_SESSION['user_id'] : 0;
    }
    
    static function get_user_name() {
        return !empty($_SESSION) && in_array('user_name', $_SESSION) ? $_SESSION['user_name'] : '';
    }

    protected function _check_login() {
        if (!in_array('login', $_SESSION) || !$_SESSION['login']) {
            @session_destroy();
            if (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false) {
                $target = str_replace("?logout", "", $_SERVER['REQUEST_URI']);
                if ($target == '' || $target == '/') {
                    header('Location: ' . WEB_URL . '/user/login');
                } else {
                    header('Location: ' . WEB_URL . '/user/login?target='.urlencode($target));
                }
            } else {
                header('Content-Type: application/json; charset=utf-8');
		        echo json_encode(['error' => 1, 'message' => CHECK_LOGIN_ERROR_NOT_LOGIN]);
		        ob_end_flush();
            }
            exit;
        }
    }

    protected function _add_create_password_fields($form) {
        $form->addFormField('Password', 'password', false, '', true)
            ->setMinLength(8)
            ->setFieldErrors(['external' => LANG_FIELD_USER_PASSWORD_ERROR]);
        $form->addFormField('Password', 'password_confirmation', false, '', true)
            ->setFieldErrors(['external' => LANG_FIELD_USER_REPEAT_PASSWORD_ERROR]);
    }

    protected function _validate_create_password_form($form) {
        $valid = $form->validate();
        $password = $form->getField('password');
        if ($password->isValid()) {
            foreach( ['[a-z]', '[A-Z]', '[0-9]', '[^a-zA-Z0-9 \t\r\n]'] as $regex) {
                if (!preg_match('#' . $regex . '#', $password->getValue())) {
                    $password->setErrorCode('external');
                    $valid = false;
                }
            }
        }
        $password_confirmation = $form->getField('password_confirmation');
        if ($password_confirmation->getValue() !== $password->getValue()) {
            $password_confirmation->setErrorCode('external');
            $valid = false;
        }
        return $valid;
    }

    protected function _logout() {
        @session_destroy();
    }
}