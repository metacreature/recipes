<?php


class Model_Base
{
    protected $_db = null;

    function __construct($db) {
        $this->_db = $db;
    }
}