<?php
require_once 'Field_Text.class.php';

class Field_Password extends Field_Text
{

    protected $_sType = 'password';

    protected $_sClassName = 'fieldpassword';

    protected function _getAttributes($arrAttributes, $bFormDisabled)
    {
        $arrAttributes = parent::_getAttributes($arrAttributes, $bFormDisabled);

        $arrAttributes['value'] = null;
        $arrAttributes['autocomplete'] = 'new-password';

        return $arrAttributes;
    }
}