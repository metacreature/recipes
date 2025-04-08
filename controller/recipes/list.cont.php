<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');

class Controller_Recipes_List extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
        // $this->_check_login();
    }

    protected function _get_user_list() {
        $res = $this->_db->execcuteUnbufferedQuery('SELECT 
            tbl_user.user_id as user_id,
            tbl_user.user_name as user_name
            FROM tbl_user
            INNER JOIN tbl_recipe USING (user_id)
            WHERE tbl_recipe.public = 1 
            ORDER BY tbl_user.user_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['user_id']] = $row['user_name'];
        }
        return $data;
    }

    protected function _get_category_list() {
        $res = $this->_db->execcuteUnbufferedQuery('SELECT 
            category_id,
            category_name
            FROM tbl_category
            ORDER BY category_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['category_id']] = $row['category_name'];
        }
        return $data;
    }

    protected function _get_tag_list() {
        $res = $this->_db->execcuteUnbufferedQuery('SELECT 
            tag_id,
            tag_name
            FROM tbl_tag
            ORDER BY tag_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['tag_id']] = $row['tag_name'];
        }
        return $data;
    }

    protected function _get_ingredients_list() {
        $res = $this->_db->execcuteUnbufferedQuery('SELECT 
            tag_id,
            tag_name
            FROM tbl_tag
            ORDER BY tag_name ASC;');

        $row_list = $this->_db->getAssocResults();
        $data = [];
        foreach ($row_list as $row) {
            $data[$row['tag_id']] = $row['tag_name'];
        }
        return $data;
    }

    protected function _get_form() {
        $user_list = $this->_get_user_list();
        $category_list = $this->_get_category_list();

        $form = new FW_Ajax_Form('register_form', false);
        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        if (true || count($user_list) > 0) {
            $form->addFormField('Select', 'user_id', false, LANG_LIST_FILTER_USER, false)
                ->setList($user_list);
        }
        $form->addFormField('Password', 'password', false, LANG_LOGIN_PASSWORD, true);
        return $form;
    }

    function view() {
        require_once (DOCUMENT_ROOT . '/views/recipes/list.view.html');
    }
}
