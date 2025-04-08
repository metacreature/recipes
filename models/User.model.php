
<?php

require_once (DOCUMENT_ROOT . '/_lib/base.model.php');

class Model_User extends Model_Base{

    protected $_user_id = null;
    protected $_user_name = null;
    protected $_email = null;


    function login($email, $password) {
        $res = $this->_db->executePreparedQuery(
            'SELECT user_id, user_name, email FROM tbl_user WHERE email = ? AND password = ?;',
            [$email, md5(SECURE_SALT . $password . SECURE_SALT)]);
        if ($res) {
            $data = $this->_db->fetchAssoc();
            if ($data) {
                $this->_user_id = null;
                $this->_user_name = null;
                $this->_email = null;
                return $data;
            }
        }
    }

    function create($user_name, $email, $password) {
        return $this->_db->executePreparedQuery(
            'INSERT INTO tbl_user (user_name, email, password) VALUES (?,?,?)',
            [$user_name, $email, md5(SECURE_SALT . $password . SECURE_SALT)]);
    }

}