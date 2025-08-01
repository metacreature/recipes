<?php
/*
 File: user.model.php
 Copyright (c) 2025 Clemens K. (https://github.com/metacreature)
 
 MIT License
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in all
 copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 SOFTWARE.
*/


require_once (DOCUMENT_ROOT . '/_lib/base.model.php');

class Model_User extends Model_Base{

    function get_user_list_with_recipes() {
        $where = '';
        if ($this->_user_id) {
            $where = ' OR user_id = '. intval($this->_user_id);
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

    protected function _crypt_password($password) {
        return hash('sha512', SECURE_SALT . $password . SECURE_SALT . $password);
    }

    protected function _calc_remember_token($user_token) {
        $ip = SETTINGS_REMEMBER_LOGIN_USE_IP ? $_SERVER['REMOTE_ADDR'] : '';
        $user_agent = SETTINGS_REMEMBER_LOGIN_USE_USER_AGENT ? $_SERVER['HTTP_USER_AGENT'] : '';
        return create_db_token($user_token, $_SERVER['HTTP_ACCEPT_LANGUAGE']. $user_agent . $ip);
    }

    function addRememberToken($password) {
        $user_token = create_user_token($this->_crypt_password($password),  $_SERVER['REMOTE_ADDR']);
        $db_token = $this->_calc_remember_token($user_token);

        $res = $this->_db->executePreparedQuery(
            'INSERT INTO tbl_user_remember (user_id, db_token, insert_timestamp) VALUES (?, ?, NOW());',
            [$this->_user_id, $db_token]
        );
        return $user_token;
    }

    function removeRememberToken($user_token) {
        $db_token = $this->_calc_remember_token($user_token);
        $res = $this->_db->executePreparedQuery(
            'DELETE FROM tbl_user_remember WHERE db_token = ?;',
            [$db_token]
        );
    }

    function loginRememberToken($user_token) {
        $db_token = $this->_calc_remember_token($user_token);
        $res = $this->_db->executePreparedQuery(
            'SELECT * FROM tbl_user WHERE user_id IN (SELECT user_id FROM tbl_user_remember WHERE db_token = ? AND insert_timestamp > NOW() - INTERVAL ? day);',
            [$db_token, SETTINGS_REMEMBER_LOGIN_EXPIRE]);
        if ($res) {
            $data = $this->_db->fetchAssoc();
            if ($data) {
                $this->setUserId($data['user_id']);
                return $data;
            }
        }
    }

    function get() {
        $res = $this->_db->executePreparedQuery(
            'SELECT * FROM tbl_user WHERE user_id = ?;',
            [$this->_user_id]);
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
                $this->setUserId($data['user_id']);
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

    function forgotten_change($email, $password) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                password = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ? AND email = ?;',
            [$this->_crypt_password($password), $this->_user_id, $email]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }

    function update_profile($user_name) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                user_name = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ?;',
            [$user_name, $this->_user_id]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }

    function update_email($actual_password, $email) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                email = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ? AND password = ?;',
            [$email, $this->_user_id, $this->_crypt_password($actual_password)]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }

    function update_password($actual_password, $password) {
        $res = $this->_db->executePreparedQuery(
            'UPDATE tbl_user SET 
                password = ?,
                last_edited = NOW(),
                cnt_update = cnt_update + 1
            WHERE user_id = ? AND password = ?;',
            [$this->_crypt_password($password), $this->_user_id, $this->_crypt_password($actual_password)]);
        if ($res) {
            if ($this->_db->getAffectedRows() == 1) {
                return true;
            }
        }
        return false;
    }
}