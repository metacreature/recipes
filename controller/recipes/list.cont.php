<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');
require_once (DOCUMENT_ROOT . '/models/recipe.model.php');

class Controller_Recipes_List extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
        // $this->_check_login();
    }

    protected function _get_form() {
        $user_obj = new Model_User($this->_db);
        $recipe_obj = new Model_Recipe($this->_db);
        $user_list = $user_obj->get_user_list_with_recipes(Controller_Base::get_user_id());
        $category_list = $recipe_obj->get_category_list();
        $tag_list = $recipe_obj->get_tag_list();
        $ingredients_list = $recipe_obj->get_ingredients_list();

        $form = new FW_Ajax_Form('filter_form', false);

        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        
        $form->addFormField('Hidden', 'page', false, 'x', true)
            ->setValue('0')
            ->setRegex('#^[0-9]+$#');
        if (count($user_list) > 0) {
            $form->addFormField('Select', 'user_id', false, 'x', false)
                ->setList($user_list)
                ->setMultiple(true);
        }
        $form->addFormField('Select', 'category_id', false, 'x', false)
                ->setList($category_list)
                ->setMultiple(true);
        $form->addFormField('Select', 'tag_id', false, 'x', false)
                ->setList($tag_list)
                ->setMultiple(true);
        $form->addFormField('Select', 'ingredients_id', false, 'x', false)
                ->setList($ingredients_list)
                ->setMultiple(true);

        $form->addFormField('Text', 'recipe_name', false, 'x', false);

        return $form;
    }

    function view() {
        $form = $this->_get_form();
        $form->resolveRequest($_GET);
        $recipe_list = [];
        $recipe_obj = new Model_Recipe($this->_db);
        if ($form->validate()) {
            $recipe_list = $recipe_obj->list(
                Controller_Base::get_user_id(),
                $form->getValue('user_id'),
                $form->getValue('category_id'),
                $form->getValue('tag_id'),
                $form->getValue('ingredients_id'),
                $form->getValue('recipe_name'),
                5000,
                0
            );
        }
        require_once (DOCUMENT_ROOT . '/views/recipes/list.view.html');
    }

    function list() {
        $form = $this->_get_form();
        $form->resolveRequest($_POST);
        
        if ($form->validate()) {
            $recipe_obj = new Model_Recipe($this->_db);
            $offset = 50 * intval($form->getValue('page'));
            $offset = $offset > 0 ? $offset - 1 : 0;
            $data = $recipe_obj->list(
                Controller_Base::get_user_id(),
                $form->getValue('user_id'),
                $form->getValue('category_id'),
                $form->getValue('tag_id'),
                $form->getValue('ingredients_id'),
                $form->getValue('recipe_name'),
                5000,
                $offset
            );
            if (is_array($data)) {
                return $form->getFormSuccess('', ['data'=>$data]);
            }
        } 
        return $form->getFormError(LANG_RECIPE_LIST_FILTER_ERROR);
    }
}
