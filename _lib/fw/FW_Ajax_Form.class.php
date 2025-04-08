<?php

class FW_Ajax_Form
{
    protected $_bFormDisabled = false;

    protected $_arrFormFields = array();

    protected $_arrFieldErrors = array();
    
    protected $_form_name;

    protected $_activate_secure;
    
    protected $_secure_time;

    function __construct($form_name, $activate_secure = false, $secure_time = 0) 
    {
        $this->_form_name = $form_name;
        $this->_activate_secure = $activate_secure;
        $this->_secure_time = $secure_time;
    }

    function addFormField($sType, $sFieldName, $bHelper = true, $sLabel = null, $bMandatory = false, $sDefaultValue = '', $bDisabled = false)
    {
        $sType = 'Field_' . preg_replace('#[^a-zA-Z0-9_]#', '', $sType);
        require_once ('Field/' . $sType . '.class.php');

        $oField = new $sType($sFieldName);
        $oField->setHelper($bHelper);
        $oField->setLabel($sLabel);
        $oField->setMandatory($bMandatory);
        $oField->setValue($sDefaultValue);
        $oField->setDisabled($bDisabled);
        $oField->setFieldErrors($this->_arrFieldErrors);
        $oField->setClassName($oField->getClassName().' form-control');
        $this->_arrFormFields[$sFieldName] = $oField;

        return $oField;
    }

    function setFieldErrors($arrFieldErrors, $mTypes = null)
    {
        if ($mTypes && is_string($mTypes)) {
            $mTypes = array(
                $mTypes
            );
        }
        if (! $mTypes) {
            $this->_arrFieldErrors = array_merge($this->_arrFieldErrors, $arrFieldErrors);
        }
        foreach ($this->_arrFormFields as $oField) {
            if (! $mTypes || in_array($oField->getType(), $mTypes)) {
                $oField->setFieldErrors($arrFieldErrors);
            }
        }
    }

    function getField($sFieldName)
    {
        if (! array_key_exists($sFieldName, $this->_arrFormFields)) {
            return null;
        }
        return $this->_arrFormFields[$sFieldName];
    }

    function getFormDisabled()
    {
        return $this->_bFormDisabled;
    }

    function setFormDisabled($bDisabled = true)
    {
        $this->_bFormDisabled = $bDisabled ? true : false;
    }

    function getValue($sFieldName)
    {
        if (! array_key_exists($sFieldName, $this->_arrFormFields)) {
            return null;
        }
        return $this->_arrFormFields[$sFieldName]->getValue();
    }

    function setValue($sFieldName, $sValue)
    {
        if (! array_key_exists($sFieldName, $this->_arrFormFields)) {
            return null;
        }
        return $this->_arrFormFields[$sFieldName]->setValue($sValue);
    }

    function getValues($bIncludeHelper = false)
    {
        $arrResult = array();
        foreach ($this->_arrFormFields as $sFieldName => $oField) {
            if (!$oField->getDisabled() && ($bIncludeHelper || ! $oField->getHelper())) {
                $arrResult[$sFieldName] = $oField->getValue();
            }
        }
        return $arrResult;
    }

    function setValues($arrValues)
    {
        if (is_array($arrValues)) {
            $arrResult = array();
            foreach ($this->_arrFormFields as $sFieldName => $oField) {
                if (array_key_exists($sFieldName, $arrValues)) {
                    $arrResult[$sFieldName] = $oField->setValue($arrValues[$sFieldName]);
                }
            }
            return $arrResult;
        }
        return null;
    }

    function resolveRequest(&$arrDBData = null)
    {
        if (is_array($arrDBData)) {
            $arrRequest = &$arrDBData;
        } else if ($_REQUEST) {
            $arrRequest = array_merge($_POST, $_GET, $_FILES);
        } else {
            $arrRequest = array();
        }

        foreach ($this->_arrFormFields as $oField) {
            $oField->resolveRequest($arrRequest);
        }
    }

    function validate()
    {
        $is_valid = true;
        
        if ($this->_activate_secure) {
            if (!isset($_SESSION['ajaxform_secure']) 
                || !isset($_POST['secure'])
                || !array_key_exists($_POST['secure'], $_SESSION['ajaxform_secure']) 
                || ($_SESSION['ajaxform_secure'][$_POST['secure']] + $this->_secure_time) > time()) {
                $is_valid = false;
            }
            
            $values = array('secure' => $_POST['secure']);
        } else {
            $values = array();
        }
        

        foreach ($this->_arrFormFields as $oField) {
            if (!$oField->getDisabled() && !$oField->validate()) {
                $is_valid = false;
            }
            if ($this->_activate_secure && !$oField->getDisabled() && !$oField instanceof Field_File) {
                $values[$oField->getName()] = (string)$oField->getValue();
            }
        }
           
        
        if ($this->_activate_secure) {
            ksort($values);
            if (!isset($_POST['secure2'])
                || $_POST['secure2'] != md5(implode('data', $values))) {
                $is_valid = false;
            }
        }
        
        
        return $is_valid;
    }
    
    function getFormSuccess($sMessage, $arr = array())
    {
        return array_merge(array('success' => true, 'message' => $sMessage), $arr);
    }
    
    function getFormWarning($sMessage, $arr = array())
    {
        return array_merge(array('warning' => true, 'message' => $sMessage), $arr);
    }
    
    function getFormError($sMessage, $arr = array())
    {
        $arrFieldErrors = array();
        foreach ($this->_arrFormFields as $sFieldName => $oField) {
            if ($oField->getError()) {
                $arrFieldErrors[$sFieldName] = $oField->getError();
            }
        }
        return array_merge(array('error' => true, 'field_errors' => $arrFieldErrors, 'message' => $sMessage), $arr);
    }

    function printStartTag()
    {
        $sReturn = '';
        $sEncType = '';
        foreach ($this->_arrFormFields as $oField) {
            if ($oField instanceof Field_Hidden) {
                $sReturn .= $oField->printInput();
            } else if ($oField instanceof Field_File) {
                $sEncType = ' enctype="multipart/form-data" ';
            }
        }
        
        if($this->_activate_secure) {
            $secure = md5(uniqid(SECURE_SALT, true));
            if (!isset($_SESSION['ajaxform_secure'])) {
                $_SESSION['ajaxform_secure'] = array();
            }
            $_SESSION['ajaxform_secure'][$secure] = time();
        }
        
        return '
			<form method="post" ' . $sEncType . ' autocomplete="off" class="ajax-form form-horizontal ' . $this->_form_name . '" data-name="'.$this->_form_name.'">'.
			($this->_activate_secure ? '<input type="hidden" name="secure" value="' . $secure . '" />' : '').
            $sReturn;
    }

    function printEndTag()
    {
        return '
			</form>';
    }

    function printSubmitLine($sDisplayValue, $sClassName = null, $sEndpoint = null)
    {
        if (! $this->_bFormDisabled) {
            return '
				<div class="button-line"><label>&nbsp;</label>' . $this->printSubmit($sDisplayValue, $sClassName, $sEndpoint) . '<div class="clear"></div></div>';
        }
        return '';
    }

    function printSubmit($sDisplayValue, $sClassName = null, $sEndpoint = null)
    {
        return '<a href="#" '.($sEndpoint ? 'data-endpoint="' .$sEndpoint  . '"' : '').' class="btn btn-ajax-submit' . ($sClassName ? ' ' . $sClassName : ''). '"><span>' . $sDisplayValue . '</span></a>';
    }

    function printLabel($sFieldName)
    {
        if (! array_key_exists($sFieldName, $this->_arrFormFields)) {
            return null;
        }
        $oField = $this->_arrFormFields[$sFieldName];
        if ($oField->getLabel()) {
            return '<label class="col-xs-12 col-md-4 control-label" for="' . $sFieldName . '">' . $oField->getLabel() . ($oField->getMandatory() ? ' *' : '') . '</label>';
        } else {
            return '<label class="col-xs-12 col-md-4 control-label" for="' . $sFieldName . '">&nbsp;</label>';
        }
    }

    function printInput($sFieldName, $arrFieldAttributes = null)
    {
        if (! array_key_exists($sFieldName, $this->_arrFormFields)) {
            return null;
        }
        return $this->_arrFormFields[$sFieldName]->printInput($arrFieldAttributes, $this->_bFormDisabled);
    }

    function getLineClassName($sFieldName)
    {
        if (! array_key_exists($sFieldName, $this->_arrFormFields)) {
            return null;
        }
        $oField = $this->_arrFormFields[$sFieldName];
        $sClassName = ''; 
        
        if ($this->_bFormDisabled || $oField->getDisabled()) {
            $sClassName .= ' line-disabled';
        } else {
            if ($oField->getMandatory()) {
                $sClassName .= 'line-mandatory';
            }
        }
        return mb_trim($sClassName);
    }

    function printLine($sFieldName, $arrFieldAttributes = null, $sLineClassNames = '')
    {
        if (! array_key_exists($sFieldName, $this->_arrFormFields)) {
            return null;
        }

        $sClassName = 'field-line form-group row ';
        $sClassName .= $this->getLineClassName($sFieldName);
        $sClassName .= $sLineClassNames ? ' ' . $sLineClassNames : '';

        return '
				<div class="' . mb_trim($sClassName) . '">' .
                    $this->printLabel($sFieldName) .
                    '<div class="field-wrapper col-xs-12 col-md-8">' .
                        $this->printInput($sFieldName, $arrFieldAttributes) .
                '</div><div class="clear"></div></div>';
    }

    function printHTMLLine($sHTML = '&nbsp;')
    {
        return '
				<div class="field-line">' . $sHTML . '<div class="clear"></div></div>';
    }

    function printForm($sSubmitLabel, $sSubmitEndpoint) {
        $sReturn = $this->printStartTag();

        $sReturn .= '<div class="ajax-form-response"></div>';
    	
        foreach ($this->_arrFormFields as $sFieldName => $oField) {
            if (!($oField instanceof Field_Hidden)) {
                $sReturn .= $this->printLine($sFieldName);
            } 
        }
        $sReturn .= $this->printSubmitLine($sSubmitLabel, 'save', $sSubmitEndpoint);
        $sReturn .= $this->printEndTag();
        return $sReturn;
    }
}