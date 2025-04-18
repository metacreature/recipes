<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');

class Controller_User_Login extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
    }

    protected function _get_form() {
        $form = new FW_Ajax_Form('login_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Email', 'email', false, '', true);
        $form->addFormField('Password', 'password', false, '', true);
        return $form;
    }

    function view() {
        $this->_logout();
        $form = $this->_get_form();
        require_once (DOCUMENT_ROOT . '/views/user/login.view.html');
    }

    function save() {
        $form = $this->_get_form();
        $form->resolveRequest();
        if ($form->validate($form)) {
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