<?php
/*
 File: list.cont.php
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


require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');
require_once (DOCUMENT_ROOT . '/models/recipe.model.php');

class Controller_Recipes_List extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
        if (SETTINGS_LIST_REQUIRES_LOGIN) {
            $this->_check_login();
        }
    }

    protected function _get_form($get_lists) {
        if ($get_lists) {
            $user_obj = new Model_User($this->_db, Controller_Base::get_user_id());
            $recipe_obj = new Model_Recipe($this->_db, Controller_Base::get_user_id());
            $user_list = $user_obj->get_user_list_with_recipes();
            $category_list = $recipe_obj->get_category_list();
            $tag_list = $recipe_obj->get_tag_list();
            $ingredients_list = $recipe_obj->get_ingredients_list();
        } else {
            $user_list = null;
            $category_list = null;
            $tag_list = null;
            $ingredients_list = null;
        }

        $form = new FW_Ajax_Form('filter_form', false);

        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        
        $form->addFormField('Hidden', 'sort', false, 'x', false)
            ->setRegex('#^[1-9]+$#');
            
        if ($user_list && count($user_list) > 1) {
            $form->addFormField('Select', 'user_ids', false, 'x', false)
                ->setList($user_list)
                ->setMultiple(true)
                ->setRegex('#^[0-9]+$#');
        } else {
            $form->addFormField('Request', 'user_ids', false, 'x', false)
                ->setMultiple(true)
                ->setRegex('#^[0-9]+$#');
        }
        $form->addFormField('Select', 'category_id', false, 'x', false)
                ->setList($category_list)
                ->setMultiple(true)
                ->setRegex('#^[0-9]+$#');
        $form->addFormField('Select', 'tag_id', false, 'x', false)
                ->setList($tag_list)
                ->setMultiple(true)
                ->setRegex('#^[0-9]+$#');
        $form->addFormField('Select', 'ingredients_id', false, 'x', false)
                ->setList($ingredients_list)
                ->setMultiple(true)
                ->setRegex('#^[0-9]+$#');

        $form->addFormField('Text', 'recipe_name', false, 'x', false);

        return $form;
    }

    function view() {
        $form = $this->_get_form(true);
        $form->resolveRequest($_GET);
        $recipe_list = [];
        $recipe_obj = new Model_Recipe($this->_db, Controller_Base::get_user_id());
        if ($form->validate()) {
            $recipe_list = $recipe_obj->list(
                $form->getValue('user_ids'),
                $form->getValue('category_id'),
                $form->getValue('tag_id'),
                $form->getValue('ingredients_id'),
                $form->getValue('recipe_name'),
                $form->getValue('sort'),
                500000,
                0,
            );
        }
        require_once (DOCUMENT_ROOT . '/views/recipes/list.view.html');
    }

    function list() {
        $form = $this->_get_form(false);
        $form->resolveRequest($_POST);
        
        if ($form->validate()) {
            $recipe_obj = new Model_Recipe($this->_db, Controller_Base::get_user_id());
            $data = $recipe_obj->list(
                $form->getValue('user_ids'),
                $form->getValue('category_id'),
                $form->getValue('tag_id'),
                $form->getValue('ingredients_id'),
                $form->getValue('recipe_name'),
                $form->getValue('sort'),
                500000,
                0
            );
            if (is_array($data)) {
                return $form->getFormSuccess('', ['data'=>$data]);
            }
        } 
        return $form->getFormError(LANG_RECIPE_LIST_FILTER_ERROR);
    }

    function get() {
        $form = new FW_Ajax_Form('detail_form', false);
        if (empty($_POST['recipe_id']) || !preg_match('#^\d+$#', $_POST['recipe_id'])) {
            return $form->getFormError(LANG_RECIPE_LIST_LOAD_DETAIL_ERROR);
        }
        $recipe_obj = new Model_Recipe($this->_db, Controller_Base::get_user_id());
        $data = $recipe_obj->get(
            $_POST['recipe_id']
        );
        if (is_array($data)) {
            return $form->getFormSuccess('', ['data'=>$data]);
        }
        return $form->getFormError(LANG_RECIPE_LIST_LOAD_DETAIL_ERROR);
    }

    function delete() {
        $form = new FW_Ajax_Form('detail_form', false);
        if (empty($_POST['recipe_id']) || !preg_match('#^\d+$#', $_POST['recipe_id'])) {
            return $form->getFormError(LANG_RECIPE_LIST_DELETE_ERROR);
        }
        $recipe_obj = new Model_Recipe($this->_db, Controller_Base::get_user_id());
        $res = $recipe_obj->setdelete(
            $_POST['recipe_id']
        );
        if ($res) {
            return $form->getFormSuccess('', []);
        }
        return $form->getFormError(LANG_RECIPE_LIST_DELETE_ERROR);
    }
}
