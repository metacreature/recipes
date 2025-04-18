<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');

class Controller_User_Profile extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
        $this->_check_login();
    }

    protected function _get_profile_form() {
        $form = new FW_Ajax_Form('profile_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Text', 'user_name', false, '', true)
            ->setMinLength(6);
        return $form;
    }

    protected function _get_email_form() {
        $form = new FW_Ajax_Form('email_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Password', 'actual_password', false, '', true);
        $form->addFormField('Email', 'email', false, '', true);
        return $form;
    }

    protected function _get_password_form() {
        $form = new FW_Ajax_Form('password_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Password', 'actual_password', false, '', true);
        $this->_add_create_password_fields($form);
        return $form;
    }

    function view() {
        $user_obj = new Model_User($this->_db);
        $data = $user_obj->get(Controller_Base::get_user_id());
        $profile_form = $this->_get_profile_form();
        $profile_form->resolveRequest($data);
        $email_form = $this->_get_email_form();
        $email_form->resolveRequest($data);
        $password_form = $this->_get_password_form();
        require_once (DOCUMENT_ROOT . '/views/user/profile.view.html');
    }

    function update_profile() {
        $form = $this->_get_profile_form();
        $form->resolveRequest();
        if ($form->validate()) {
            $user_obj = new Model_User($this->_db);
            $res = $user_obj->update_profile(
                Controller_Base::get_user_id(),
                $form->getValue('user_name'));
            if ($res) {
                $_SESSION['user_name'] = $form->getValue('user_name');
                return $form->getFormSuccess(LANG_PROFILE_SUCCESS);
            } 
            return $form->getFormError(LANG_PROFILE_DATA_FAIL);
        } 
        return $form->getFormError(LANG_FORM_INVALID); 
    }

    function update_email() {
        $form = $this->_get_email_form();
        $form->resolveRequest();
        if ($form->validate()) {
            $user_obj = new Model_User($this->_db);
            $res = $user_obj->update_email(
                Controller_Base::get_user_id(),
                $form->getValue('actual_password'),
                $form->getValue('email'));
            if ($res) {
                return $form->getFormSuccess(LANG_PROFILE_SUCCESS);
            } 
            return $form->getFormError(LANG_PROFILE_EMAIL_FAIL);
        } 
        return $form->getFormError(LANG_FORM_INVALID); 
    }

    function update_password() {
        $form = $this->_get_password_form();
        $form->resolveRequest();
        if ($this->_validate_create_password_form($form)) {
            $user_obj = new Model_User($this->_db);
            $res = $user_obj->update_password(
                Controller_Base::get_user_id(),
                $form->getValue('actual_password'),
                $form->getValue('password'));
            if ($res) {
                return $form->getFormSuccess(LANG_PROFILE_SUCCESS);
            } 
            return $form->getFormError(LANG_PROFILE_PASSWORD_FAIL);
        } 
        return $form->getFormError(LANG_FORM_INVALID); 
    }
}