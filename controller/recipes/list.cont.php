<?php

require_once (DOCUMENT_ROOT . '/_lib/base.cont.php');

class Controller_Recipes_List extends Controller_Base
{
    function __construct($db) {
        parent::__construct($db);
        // $this->_check_login();
    }

    function view() {
        require_once (DOCUMENT_ROOT . '/views/recipes/list.view.html');
    }
}
