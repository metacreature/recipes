<?php
require_once 'Field_Base.class.php';

class Field_Request extends Field_Base
{

    function validate()
    {
        $this->_bValid = true;
        if (! $this->_validateMandatory())
            return false;
        if (! $this->_validateRegEx())
            return false;
        return $this->_checkError();
    }

    function printInput($arrAttributes = null, $bFormDisabled = false)
    {
        return '';
    }
}