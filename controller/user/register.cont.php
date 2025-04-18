<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');

class Controller_User_Register extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
        if (!SETTINGS_ALLOW_REGISTER) {
            header('HTTP/1.0 403 Forbidden');
            require_once(DOCUMENT_ROOT . '/crawler.html');
            exit;
        }
    }

    protected function _get_form() {
        $form = new FW_Ajax_Form('register_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Text', 'user_name', false, '', true)
            ->setMinLength(6);
        $form->addFormField('Email', 'email', false, '', true);
        $this->_add_create_password_fields($form);
        return $form;
    }

    function view() {
        $this->_logout();
        $form = $this->_get_form();
        require_once (DOCUMENT_ROOT . '/views/user/register.view.html');
    }

    function save() {
        $form = $this->_get_form();
        $form->resolveRequest();
        if ($this->_validate_create_password_form($form)) {
            $user_obj = new Model_User($this->_db);
            $res = $user_obj->create(
                $form->getValue('user_name'),
                $form->getValue('email'), 
                $form->getValue('password'));
            if ($res) {
                return $form->getFormSuccess(LANG_REGISTER_SUCCESS);
            } 
            return $form->getFormError(LANG_REGISTER_FAIL_EMAIL);
        } 
        return $form->getFormError(LANG_FORM_INVALID); 
    }
}