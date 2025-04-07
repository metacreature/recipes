<?php
require_once 'Field_Text.class.php';

class Field_Email extends Field_Text
{
    function __construct($sName)
    {
        parent::__construct($sName);

        $this->setRegEx(REGEX_EMAIL);
        $this->_sType = 'email';
    }
}