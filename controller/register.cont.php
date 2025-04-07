<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');

class Controller_Register extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
    }

    protected function _get_form() {
        $form = new FW_Ajax_Form('register_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Email', 'email', false, LANG_REGISTER_EMAIL, true);
        $form->addFormField('Password', 'password', false, LANG_REGISTER_PASSWORD, true)
            ->setMinLength(8)
            ->setFieldErrors(['external' => LANG_REGISTER_PASSWORD_ERROR]);
        $form->addFormField('Password', 'password_confirmation', false, LANG_REGISTER_REPEAT_PASSWORD, true)
            ->setFieldErrors(['external' => LANG_REGISTER_REPEAT_PASSWORD_ERROR]);
        return $form;
    }

    protected function _validate_form($form) {
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

    function view() {
        $this->_logout();
        $form = $this->_get_form();
        require_once (DOCUMENT_ROOT . '/views/register.view.html');
    }

    function save() {
        $form = $this->_get_form();
        $form->resolveRequest();
        if ($this->_validate_form($form)) {
            return $form->getFormSuccess(LANG_REGISTER_SUCCESS);
        } else {
            return $form->getFormError(LANG_REGISTER_FAIL);
        }
    }
}