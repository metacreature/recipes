
<?php

require_once (DOCUMENT_ROOT . '/_lib/base.model.php');

class Model_User extends Model_Base{

    protected function _crypt_password($password) {
        return md5(SECURE_SALT . $password . SECURE_SALT . $password);
    }

    function get_user_list_with_recipes($user_id) {
        $where = '';
        if ($user_id) {
            $where = ' AND user_id != '. intval($user_id);
        }

        $res = $this->_db->executeUnbufferedQuery('SELECT 
            tbl_user.user_id as user_id,
            tbl_user.user_name as user_name
            FROM tbl_user
            INNER JOIN tbl_recipe USING (user_id)
            WHERE tbl_recipe.public = 1 ' . $where . '
            ORDER BY tbl_user.user_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['user_id']] = $row['user_name'];
        }
        return $data;
    }

    function get($user_id) {
        $res = $this->_db->executePreparedQuery(
            'SELECT * FROM tbl_user WHERE user_id = ?;',
            [$user_id]);
        if ($res) {
            $data = $this->_db->fetchAssoc();
            if ($data) {
                return $data;
            }
        }
    }

    function login($email, $password) {
        $res = $this->_db->executePreparedQuery(
            'SELECT * FROM tbl_user WHERE email = ? AND password = ?;',
            [$email, $this->_crypt_password($password)]);
        if ($res) {
            $data = $this->_db->fetchAssoc();
            if ($data) {
                return $data;
            }
        }
    }

    function create($user_name, $email, $password) {
        return $this->_db->executePreparedQuery(
            'INSERT INTO tbl_user (user_name, email, password, last_edited) VALUES (?,?,?, NOW())',
            [$user_name, $email, $this->_crypt_password($password)]);
    }

    
    function forgotten($email) {
        $res = $this->_db->executePreparedQuery(
            'SELECT user_id, user_name, email FROM tbl_user WHERE email = ?;',
            [$email]);
        if ($res) {
            $data = $this->_db->fetchAssoc();
            if ($data) {
                return $data;
            }
        }
    }

    function forgotten_change($user_id, $email, $password) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                password = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ? AND email = ?;',
            [$this->_crypt_password($password), $user_id, $email]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }

    function update_profile($user_id, $user_name) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                user_name = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ?;',
            [$user_name, $user_id]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }

    function update_email($user_id, $actual_password, $email) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                email = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ? AND password = ?;',
            [$email, $user_id, $this->_crypt_password($actual_password)]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }

    function update_password($user_id, $actual_password, $password) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                password = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ? AND password = ?;',
            [$this->_crypt_password($password), $user_id, $this->_crypt_password($actual_password)]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }
}