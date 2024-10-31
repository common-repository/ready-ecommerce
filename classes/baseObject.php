<?php
abstract class baseObject {
    protected $_internalErrors = array();
    protected $_haveErrors = false;
	protected $_error_codes = array();
	
    public function pushError($error, $key = '', $error_code = '') {
        if(is_array($error)) {
            $this->_internalErrors = array_merge ($this->_internalErrors, $error);
        } elseif(empty($key)) {
            $this->_internalErrors[] = $error;
        } else {
            $this->_internalErrors[ $key ] = $error;
        }
        $this->_haveErrors = true;
		if(!empty($error_code))
			$this->_error_codes[] = $error_code;
    }
    public function getErrors() {
        return $this->_internalErrors;
    }
    public function haveErrors() {
        return $this->_haveErrors;
    }
	public function getErrorCodes() {
		return $this->_error_codes;
	}
}

