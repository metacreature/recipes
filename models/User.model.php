
<?php

require_once (DOCUMENT_ROOT . '/_lib/base.model.php');

class Model_User extends Model_Base{

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

    function login($email, $password) {
        $res = $this->_db->executePreparedQuery(
            'SELECT user_id, user_name, email FROM tbl_user WHERE email = ? AND password = ?;',
            [$email, md5(SECURE_SALT . $password . SECURE_SALT)]);
        if ($res) {
            $data = $this->_db->fetchAssoc();
            if ($data) {
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