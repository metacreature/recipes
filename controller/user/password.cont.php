<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');

require_once(DOCUMENT_ROOT . '/_lib/external_libs/phpmailer/src/Exception.php');
require_once(DOCUMENT_ROOT . '/_lib/external_libs/phpmailer/src/PHPMailer.php');

class Controller_User_Password extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
    }

    protected function _gen_key($user_id, $email, $timestamp) {
        return md5($user_id . '_' . SECURE_SALT . $email . '_' . $timestamp . SECURE_SALT . $email);
    }

    protected function _get_forgotten_form() {
        $form = new FW_Ajax_Form('forgotten_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Email', 'email', false, '', true);
        return $form;
    }

    protected function _get_change_form() {
        $form = new FW_Ajax_Form('password_change_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        $form->addFormField('Hidden', 'user_id', false, '', true);
        $form->addFormField('Hidden', 'email', false, '', true);
        $form->addFormField('Hidden', 'ts', false, '', true);
        $form->addFormField('Hidden', 's', false, '', true);
        $this->_add_create_password_fields($form);
        return $form;
    }
 
    function change() {
        $form = $this->_get_change_form();
        $form->resolveRequest();
        require_once (DOCUMENT_ROOT . '/views/user/password.change.view.html');
    }

    function change_submit() {
        $form = $this->_get_change_form();
        $form->resolveRequest();
        if ($form->getValue('ts') < time() - 1800 || $form->getValue('ts') >= time()) {
            return $form->getFormError(LANG_PASSWORD_CHANGE_ERROR_TIME);
        }
        if (!$this->_validate_create_password_form($form)) {
            return $form->getFormError(LANG_FORM_INVALID);
        }

        sleep(rand(1, 5));
        usleep(rand(0, 900000));

        if ($form->getValue('s') == $this->_gen_key($form->getValue('user_id'), $form->getValue('email'), $form->getValue('ts'))) {
            $user_obj = new Model_User($this->_db);
            $user_obj->forgotten_change(
                $form->getValue('user_id'),
                $form->getValue('email'), 
                $form->getValue('password'));
        }
        return $form->getFormSuccess(LANG_PASSWORD_CHANGE_SUCCESS);
    }


    function request() {
        $this->_logout();
        $form = $this->_get_forgotten_form();
        require_once (DOCUMENT_ROOT . '/views/user/password.request.view.html');
    }

    function request_submit() {
        $form = $this->_get_forgotten_form();
        $form->resolveRequest();

        sleep(rand(1, 5));
        usleep(rand(0, 900000));

        if ($form->validate($form)) {
            $user_obj = new Model_User($this->_db);
            $data = $user_obj->forgotten($form->getValue('email'));
            if ($data) {

                $timestamp = time();
                $change_url = WEB_URL . '/user/password/change';
                $change_url .= '?user_id=' . $data['user_id'] . '&email=' . rawurlencode($data['email']);
                $change_url .= '&ts=' . $timestamp . '&s=' . $this->_gen_key($data['user_id'], $data['email'], $timestamp);
                $user_name = $data['user_name'];

                require_once (DOCUMENT_ROOT . '/views/user/password.request.email.'.SELECTED_LANG.'.html');
                $message = ob_get_contents();
                ob_clean();

                try {
                    $mail = new PHPMailer\PHPMailer\PHPMailer;
                    $mail->setLanguage(SELECTED_LANG);
                    $mail->From = EMAIL_FROM_MAIL;
                    $mail->FromName = EMAIL_FROM_NAME;
                    $mail->addAddress($data['email']);
                    $mail->isHTML(true);
                    $mail->Subject = LANG_PASSWORD_REQUEST_SUBJECT;
                    $mail->Body = $message;
                    $mail->AltBody = $mail->html2text($message, true);
                    $mail->send();
                } catch (Exception $e) {}
            }
        }
        return $form->getFormSuccess(LANG_PASSWORD_REQUEST_SUCCESS);
        
    }
}