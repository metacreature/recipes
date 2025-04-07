<?php
require_once 'Field_Text.class.php';

class Field_Textarea extends Field_Text
{

    protected $_sClassName = 'fieldtextarea';

    protected function _getAttributes($arrAttributes, $bFormDisabled)
    {
        $arrAttributes = parent::_getAttributes($arrAttributes, $bFormDisabled);

        unset($arrAttributes['type']);
        unset($arrAttributes['value']);
        unset($arrAttributes['placeholder']);

        return $arrAttributes;
    }

    function printInput($arrAttributes = null, $bFormDisabled = false)
    {
        $arrAttributes = $this->_getAttributes($arrAttributes, $bFormDisabled);

        return '<textarea' . $this->_buildAttributesString($arrAttributes) . '>' . xssProtect($this->_mValue) . '</textarea>';
    }
}