<?php
require_once 'Field_Base.class.php';

class Field_Hidden extends Field_Base
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
        return '<input type="hidden" id="' . $this->_sName . '" name="' . $this->_sName . '" value="' . xssProtect($this->_mValue) . '">';
    }
}