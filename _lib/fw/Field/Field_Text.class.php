<?php
require_once 'Field_Base.class.php';

class Field_Text extends Field_Base
{

    protected $_sType = 'text';

    protected $_sClassName = 'fieldtext';

    protected $_sPlaceholder;

    protected $_iMinLength = null;

    protected $_iMaxLength = null;

    function __construct($sName)
    {
        parent::__construct($sName);
        $this->setFieldErrors(array(
            'too_long' => 'input too long <br />(max {LENGTH}, actual {ACTUAL_LENGTH})',
            'too_short' => 'input too short <br />(min {LENGTH}), actual {ACTUAL_LENGTH})'
        ));
    }

    function getMinLength()
    {
        return $this->_iMinLength;
    }

    function setMinLength($iMinLength)
    {
        $this->_iMinLength = $iMinLength;
        return $this;
    }

    function getMaxLength()
    {
        return $this->_iMaxLength;
    }

    function setMaxLength($iMaxLength)
    {
        $this->_iMaxLength = $iMaxLength;
        return $this;
    }

    function getPlaceholder()
    {
        return ! is_null($this->_sPlaceholder) ? $this->_sPlaceholder : '';
    }

    function setPlaceHolder($sPlaceholder)
    {
        $this->_sPlaceholder = $sPlaceholder;
        return $this;
    }

    protected function _validateLength()
    {
        if ($this->_iMinLength && $this->_iMinLength > mb_strlen($this->_mValue)) {
            $this->_bValid = false;
            $this->_sErrorCode = 'too_short';
            if (! $this->_sError) {
                $this->_sError = str_replace(array(
                    '{LENGTH}',
                    '{ACTUAL_LENGTH}'
                ), array(
                    $this->_iMinLength,
                    mb_strlen($this->_mValue)
                ), $this->_arrFieldErrors['too_short']);
            }
            return false;
        }
        if ($this->_iMaxLength && $this->_iMaxLength < mb_strlen($this->_mValue)) {
            $this->_bValid = false;
            $this->_sErrorCode = 'too_long';
            if (! $this->_sError) {
                $this->_sError = str_replace(array(
                    '{LENGTH}',
                    '{ACTUAL_LENGTH}'
                ), array(
                    $this->_iMaxLength,
                    mb_strlen($this->_mValue)
                ), $this->_arrFieldErrors['too_long']);
            }
            return false;
        }
        return true;
    }

    function validate()
    {
        $this->_bValid = true;
        if (! $this->_validateMandatory())
            return false;
        if (! $this->_validateLength())
            return false;
        if (! $this->_validateRegEx())
            return false;
        return $this->_checkError();
    }

    protected function _getAttributes($arrAttributes, $bFormDisabled)
    {
        $arrAttributes = parent::_getAttributes($arrAttributes, $bFormDisabled);

        $arrAttributes['type'] = $this->_sType;
        $arrAttributes['value'] = $this->_mValue;

        $arrAttributes['placeholder'] = $this->getPlaceholder();
        if ($this->_iMaxLength) {
            $arrAttributes['maxlength'] = $this->_iMaxLength;
        }
        return $arrAttributes;
    }

    function printInput($arrAttributes = null, $bFormDisabled = false)
    {
        $arrAttributes = $this->_getAttributes($arrAttributes, $bFormDisabled);

        return '<input' . $this->_buildAttributesString($arrAttributes) . ' />';
    }
}