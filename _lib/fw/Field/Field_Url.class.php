<?php
require_once 'Field_Text.class.php';

class Field_Url extends Field_Text
{
    function __construct($sName)
    {
        parent::__construct($sName);

        $this->setRegEx(REGEX_URL);
        $this->_sType = 'url';
    }
}