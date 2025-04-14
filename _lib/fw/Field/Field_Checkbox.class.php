<?php
require_once 'Field_Base.class.php';

class Field_Checkbox extends Field_Base
{

    protected $_sClassName = 'fieldcheckbox';

    function __construct($sName)
    {
        parent::__construct($sName);
        $this->setFieldErrors(array(
            'mandatory' => 'checkbox required'
        ));
        $this->setValue('0');
    }

    function setValue($sValue)
    {
        $this->_mValue = empty($sValue) ? '0' : '1';

        return $this;
    }

    function resolveRequest($arrRequest)
    {
        //if (array_key_exists('_available_' . $this->_sName, $arrRequest)) {
            $this->setValue(! empty($arrRequest[$this->_sName]) ? 1 : 0);
        //}
    }

    protected function _validateMandatory()
    {
        if ($this->_bMandatory && ! $this->_mValue) {
            $this->setErrorCode('mandatory');
            return false;
        }
        return true;
    }

    function validate()
    {
        $this->_bValid = true;
        if (! $this->_validateMandatory())
            return false;
        return $this->_checkError();
    }

    protected function _getAttributes($arrAttributes, $bFormDisabled)
    {
        $arrAttributes = parent::_getAttributes($arrAttributes, $bFormDisabled);

        $arrAttributes['type'] = 'checkbox';
        $arrAttributes['value'] = '1';
        if ($this->_mValue) {
            $arrAttributes['checked'] = '';
        }

        return $arrAttributes;
    }

    function printInput($arrAttributes = null, $bFormDisabled = false)
    {
        $arrAttributes = $this->_getAttributes($arrAttributes, $bFormDisabled);
        if ($bFormDisabled || $this->_bDisabled) {
            $arrAttributes['disabled'] = '';
        }
        if (!empty($arrAttributes['autocomplete'])) {
            unset($arrAttributes['autocomplete']);
        }
        return '<input' . $this->_buildAttributesString($arrAttributes) . '>'; // . '<input type="hidden" name="_available_' . $this->_sName . '" value="1">';
    }
}