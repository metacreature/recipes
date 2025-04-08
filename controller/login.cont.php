<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');

class Controller_Login extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
    }

    protected function _get_form() {
        $form = new FW_Ajax_Form('register_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Email', 'email', false, LANG_LOGIN_EMAIL, true);
        $form->addFormField('Password', 'password', false, LANG_LOGIN_PASSWORD, true);
        return $form;
    }

    protected function _validate_form($form) {
        $valid = $form->validate();
        $password = $form->getField('password');
        return $valid;
    }

    function view() {
        $this->_logout();
        $form = $this->_get_form();
        require_once (DOCUMENT_ROOT . '/views/login.view.html');
    }

    function save() {
        $form = $this->_get_form();
        $form->resolveRequest();
        if ($this->_validate_form($form)) {
            $user_obj = new Model_User($this->_db);
            $data = $user_obj->login($form->getValue('email'), $form->getValue('password'));
            if ($data) {
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $data['user_id'];
                $_SESSION['user_name'] = $data['user_name'];
                return $form->getFormSuccess(LANG_LOGIN_SUCCESS);
            }
        }
        return $form->getFormError(LANG_LOGIN_FAIL);
        
    }

    function logout() {
        @session_destroy();
        return WEB_URL . '?logout';
    }
}