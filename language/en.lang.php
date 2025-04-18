<?php 

define('LANG_PAGE_TITLE', 'Rezepte');
define('LANG_FORMFIELD_ERRORS', array(
    'pattern' => 'Invalid value!',
    'external' => 'Invalid value!',
    'too_long' => 'Value is too long! (max {LENGTH}, actual {ACTUAL_LENGTH})',
    'too_short' => 'Value is too short! (min {LENGTH})',
    'mandatory' => 'Value required!'
));
define('LANG_FORM_INVALID', 'Please evaluate your input!');

define('LANG_FIELD_USER_USERNAME', 'Username');
define('LANG_FIELD_USER_EMAIL', 'E-Mail');
define('LANG_FIELD_USER_PASSWORD', 'Password');
define('LANG_FIELD_USER_PASSWORD_ERROR', 'Password must contain at minimum one uppercase, lowercase, number and special character!');
define('LANG_FIELD_USER_REPEAT_PASSWORD', 'Repeat password');
define('LANG_FIELD_USER_REPEAT_PASSWORD_ERROR', 'Passwords don\'t match!');
define('LANG_FIELD_USER_ACTUAL_PASSWORD', 'Actual password');

define('LANG_NAVIGATION_LOGIN', 'login');
define('LANG_NAVIGATION_LOGOUT', 'logout');
define('LANG_NAVIGATION_REGISTER', 'register');

define('LANG_REGISTER_HDL', 'Registration');
define('LANG_REGISTER_SAVE', 'register');
define('LANG_REGISTER_FAIL_EMAIL', 'E-Mail already exists!');
define('LANG_REGISTER_SUCCESS', 'Registration was successfull!');

define('LANG_PASSWORD_REQUEST_HDL', 'Forggoten password');
define('LANG_PASSWORD_REQUEST_BUTTON', 'request link');
define('LANG_PASSWORD_REQUEST_SUBJECT', 'Request new password');
define('LANG_PASSWORD_REQUEST_SUCCESS', 'The link to change the password is sent to your e-mail address!');

define('LANG_PASSWORD_CHANGE_HDL', 'Change password');
define('LANG_PASSWORD_CHANGE_SAVE', 'save');
define('LANG_PASSWORD_CHANGE_ERROR_TIME', 'The link expired! <br>Please request a new link!');
define('LANG_PASSWORD_CHANGE_SUCCESS', 'The password was changed successfully!');

define('LANG_PROFILE_SUCCESS', 'Updated succeesfully!');
define('LANG_PROFILE_SAVE', 'update');
define('LANG_PROFILE_DATA_HDL', 'Update profile');
define('LANG_PROFILE_DATA_FAIL', 'Update failed!');
define('LANG_PROFILE_EMAIL_HDL', 'Update E-Mail');
define('LANG_PROFILE_EMAIL_FAIL', 'Update failed!<br>Actual password is wrong or E-Mail allready exists!');
define('LANG_PROFILE_PASSWORD_HDL', 'Update password');
define('LANG_PROFILE_PASSWORD_FAIL', 'Update failed!<br>Actual password is wrong!');

define('CHECK_LOGIN_ERROR_NOT_LOGIN', 'You are not loged in!');

define('LANG_LOGIN_HDL', 'Login');
define('LANG_LOGIN_SAVE', 'login');
define('LANG_LOGIN_FORGOTTEN', 'forgotten password');
define('LANG_LOGIN_FAIL', 'Login failed!');
define('LANG_LOGIN_SUCCESS', 'Login was successful!');


define('LANG_RECIPE_LIST_FILTER_USER', 'User');
define('LANG_RECIPE_LIST_FILTER_CATEGORY', 'Category');
define('LANG_RECIPE_LIST_FILTER_TAG', 'Tag');
define('LANG_RECIPE_LIST_FILTER_INGREDIENTS', 'Ingredients');
define('LANG_RECIPE_LIST_FILTER_NAME', 'Name');
define('LANG_RECIPE_LIST_FILTER_ERROR', 'Something went wrong!');
define('LANG_RECIPE_LIST_ADD', 'add recipe');
define('LANG_RECIPE_LIST_LOAD_DETAIL_ERROR', 'Something went wrong!');
define('LANG_RECIPE_LIST_DETAIL_INGREDINTS_ALTERNATIVE', 'or');

define('LANG_RECIPE_LIST_DELETE_CONFIRM_TITLE', 'Delete?');
define('LANG_RECIPE_LIST_DELETE_CONFIRM_TEXT', 'Do you really want to delete {recipe_name}?');
define('LANG_RECIPE_LIST_DELETE_CONFIRM_CONFIRM', 'yes');
define('LANG_RECIPE_LIST_DELETE_CONFIRM_CANCEL', 'no');
define('LANG_RECIPE_LIST_DELETE_ERROR', 'Something went wrong!');

define('LANG_RECIPE_EDITOR_BACK', 'back');
define('LANG_RECIPE_EDITOR_ADD_NEW', 'add new recipe');
define('LANG_SELECT2_SELECT', 'select');
define('LANG_RECIPE_EDITOR_IMAGE', 'Image');
define('LANG_RECIPE_EDITOR_DEL_IMAGE', 'Delete image');
define('LANG_RECIPE_EDITOR_ORIGINAL', 'Original-Text');
define('LANG_RECIPE_EDITOR_PUBLIC', 'Public');
define('LANG_RECIPE_EDITOR_CATEGORY', 'Category');
define('LANG_RECIPE_EDITOR_TAGLIST', 'Tags');
define('LANG_RECIPE_EDITOR_NAME', 'Name');
define('LANG_RECIPE_EDITOR_PERSONS', 'Person-count');
define('LANG_RECIPE_EDITOR_INGREDIENTS', 'Ingredients');
define('LANG_RECIPE_EDITOR_INGREDIENTS_IS_ALTERNATIVE', 'or');
define('LANG_RECIPE_EDITOR_INGREDIENTS_QUANTITY', 'Quantity');
define('LANG_RECIPE_EDITOR_INGREDIENTS_UNIT', 'Unit');
define('LANG_RECIPE_EDITOR_INGREDIENTS_NAME', 'Name');
define('LANG_RECIPE_EDITOR_STEP', 'Step ');
define('LANG_RECIPE_EDITOR_SAVE', 'save');
define('LANG_RECIPE_EDITOR_FAIL', 'Save failed!');
define('LANG_RECIPE_EDITOR_FAIL_IMAGE', 'Imageupload failed!');
define('LANG_RECIPE_EDITOR_SUCCESS', 'Saved succeesfully!');