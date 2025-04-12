<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');
require_once (DOCUMENT_ROOT . '/models/user.model.php');
require_once (DOCUMENT_ROOT . '/models/recipe.model.php');
require_once (DOCUMENT_ROOT . '/_lib/fw/FW_ImageServiceGallery.class.php');

class Controller_Recipes_Editor extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
        $this->_check_login();
    }

    protected function _get_form($request) {
        $recipe_obj = new Model_Recipe($this->_db);
        $category_list = $recipe_obj->get_category_list();
        $tag_list = $recipe_obj->get_tag_list();
        $ingredients_list = $recipe_obj->get_ingredients_list();

        $form = new FW_Ajax_Form('recipe_editor_form', false);

        $form->setFieldErrors(LANG_FORMFIELD_ERRORS);
        
        $form->addFormField('Hidden', 'recipe_id', false, null, false)
            ->setRegex('#^[0-9]+$#');
        $form->addFormField('Hidden', 'cnt_ingredients', false, null, true)
            ->setRegex('#^[1-9][0-9]*$#');
        $form->addFormField('Hidden', 'cnt_step', false, null, true)
            ->setRegex('#^[1-9][0-9]*$#');
        
        $form->addFormField('Checkbox', 'public', false, null, false);
        $form->addFormField('Select', 'category_id', false, null, true)
            ->setList($category_list);
        $form->addFormField('Text', 'tag_list', false, null, true);
        $form->addFormField('Text', 'recipe_name', false, null, true);
        $form->addFormField('Text', 'persons', false, null, true)
            ->setRegex('#^[0-9]+$#');
        $form->addFormField('Textarea', 'original_text', false, null, false);
        $form->addFormField('File', 'image', false, null, false)
            ->setExtensions('jpg','jpeg','png','webp');
        $form->addFormField('Checkbox', 'del_image', false, null, false);
        

        $form->resolveRequest($request);

        $cnt = 0;
        $cnt_ingeredients = $form->getValue('cnt_ingredients') ? (int)$form->getValue('cnt_ingredients') : 1;
        while($cnt < $cnt_ingeredients) {
            $cnt++;
            $form->addFormField('Checkbox', 'ingredients_is_alternative_'.$cnt, false, '', false);
            $form->addFormField('Text', 'ingredients_quantity_'.$cnt, false, '', true)
            ->setRegex('#^[0-9]+([,.][0-9])?$#');;
            $form->addFormField('Text', 'ingredients_unit_'.$cnt, false, '', true);
            $form->addFormField('Text', 'ingredients_name_'.$cnt, false, '', true);
        }

        $cnt = 0;
        $cnt_step = $form->getValue('cnt_step') ? (int)$form->getValue('cnt_step') : 1;
        while($cnt < $cnt_step) {
            $cnt++;
            $form->addFormField('Textarea', 'step_description_'.$cnt, false, '', true);
        }

        return $form;
    }

    function view() {
        $form_values = array(
            'cnt_ingredients' => 1,
            'cnt_step' => 1
        );
        
        $recipe_obj = new Model_Recipe($this->_db);

        if (!empty($_GET['recipe_id']) && is_numeric($_GET['recipe_id'])) {
            $recipe = $recipe_obj->get($_GET['recipe_id'], Controller_Base::get_user_id());
            if ($recipe) {
                $form_values = $recipe;
                $form_values['cnt_ingredients'] = count($recipe['ingredients_list']);
                $form_values['cnt_step'] = count($recipe['step_list']);
                $form_values['tag_list'] = implode(', ', $recipe['tag_list']);
                $cnt = 0;
                foreach ($recipe['ingredients_list'] as $nr => $row) {
                    $cnt++;
                    $form_values['step_description_'.$cnt] = $row['is_alternative'];
                    $form_values['ingredients_quantity_'.$cnt] = $row['quantity'];
                    $form_values['ingredients_unit_'.$cnt] = $row['unit_name'];
                    $form_values['ingredients_name_'.$cnt] = $row['ingredients_name'];
                }
                $cnt = 0;
                foreach ($recipe['step_list'] as $row) {
                    $cnt++;
                    $form_values['step_description_'.$cnt] = $row;
                }
            }
        }
        
        $form = $this->_get_form($form_values);
        $form->resolveRequest($form_values);

        $tag_list = array_values($recipe_obj->get_tag_list());
        $ingredients_list = array_values($recipe_obj->get_ingredients_list());
        $unit_list = array_values($recipe_obj->get_unit_list());

        require_once (DOCUMENT_ROOT . '/views/recipes/editor.view.html');
    }

    function save() {
        $form = $this->_get_form($_POST);
        $form->resolveRequest();
        if ($form->validate()) {

            function my_trim($value) {
                return mb_trim($value);
            }
            $tag_list = array_map('my_trim', array_column(json_decode($form->getValue('tag_list')), 'value'));

            $ingredients_list = [];
            $cnt = 0;
            while($cnt < (int)$form->getValue('cnt_ingredients')) {
                $cnt++;
                $ingredients_list[] = [
                    'is_alternative' => $form->getValue('ingredients_is_alternative_'.$cnt),
                    'quantity' => $form->getValue('ingredients_quantity_'.$cnt),
                    'unit_name' => $form->getValue('ingredients_unit_'.$cnt),
                    'ingredients_name' => $form->getValue('ingredients_name_'.$cnt)
                ];
            }
            $ingredients_list[0]['is_alternative'] = '0';
        
            $step_list = [];
            $cnt = 0;
            while($cnt < (int)$form->getValue('cnt_step')) {
                $cnt++;
                $step_list[] = $form->getValue('step_description_'.$cnt);
            }

            $image_upload = null;
            if ($form->getValue('image') && !$form->getValue('del_image')) {
                $oImageService = new FW_ImageServiceGallery('recipes', HIDDEN_IMAGEFOLDER_SECURE, array());
                $image_name = uniqid();
                
                $arrUploadedImage = $form->getValue('image');
                $arrUploadedImage = reset($arrUploadedImage);
                $res_img = $oImageService->upload($arrUploadedImage['tmp_name'], $image_name, false);
                
                if (!is_array($res_img)) {
                    return $form->getFormError(LANG_RECIPE_EDITOR_FAIL_IMAGE);
                }

                $oImageService->raw_create_image($oImageService->getBaseDirName().'/'.$image_name, 'webp', null, [900, 600], null, null, false, FW_ImageServiceBase::RESIZE_MODE_COVER);
                $oImageService->raw_create_image($oImageService->getBaseDirName().'/'.$image_name.'_s', 'webp', null, [180, 120], null, null, false, FW_ImageServiceBase::RESIZE_MODE_COVER);
                
                $image_upload = [
                    'orig_image_name' => $res_img['orig_image_name'],
                    'image_name' => $image_name
                ];
            }

            $recipe_obj = new Model_Recipe($this->_db);
            $res = $recipe_obj->save(
                Controller_Base::get_user_id(),
                $form->getValue('recipe_id'),
                $form->getValue('public'), 
                $form->getValue('category_id'), 
                $form->getValue('recipe_name'), 
                $form->getValue('persons'), 
                $form->getValue('original_text'), 
                $tag_list, $ingredients_list, $step_list, 
                $form->getValue('del_image'),
                $image_upload);
            if ($res) {

                $tag_list = array_values($recipe_obj->get_tag_list());
                $ingredients_list = array_values($recipe_obj->get_ingredients_list());
                $unit_list = array_values($recipe_obj->get_unit_list());

                return $form->getFormSuccess(LANG_RECIPE_EDITOR_SUCCESS, [
                    'recipe_id' => $res, 
                    'tag_list' => $tag_list, 
                    'ingredients_list' => $ingredients_list, 
                    'unit_list' => $unit_list, 
                    'image_name' => !empty($image_upload) ? $image_upload['image_name'] : ''
                ]);
            } 
            return $form->getFormError(LANG_RECIPE_EDITOR_FAIL);
        } 
        return $form->getFormError(LANG_RECIPE_EDITOR_INVALID); 
    }
}
