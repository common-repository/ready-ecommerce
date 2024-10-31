<?php
abstract class view extends baseObject {
    /* 
     * @deprecated
     */
    protected $_tpl = S_DEFAULT;
    /*
     * @var string name of theme to load from templates, if empty - default values will be used
     */
    protected $_theme = '';
    /*
     * @var string module code for this view
     */
    protected $_code = '';

    public function display($tpl = '') {
        $tpl = (empty($tpl)) ? $this->_tpl : $tpl;

        if(($content = $this->getContent($tpl)) !== false) {
            echo $content;
        }
    }
	public function getPath($tpl) {
		$path = '';
		$code = $this->_code;
		$parentModule = frame::_()->getModule( $this->_code );
		$defaultPlugTheme = frame::_()->getModule('options')->getModel()->get('default_theme');
		/*if(!empty($this->_theme) && file_exists(S_TEMPLATES_DIR. $this->_theme. DS. $code. DS. $tpl. '.php')) {
            $path = S_TEMPLATES_DIR. $this->_theme. DS. $code. DS. $tpl. '.php';
        } else*/
		if(file_exists(utils::getCurrentWPThemeDir(). 'toe'. DS. $code. DS. $tpl. '.php')) {
            $path = utils::getCurrentWPThemeDir(). 'toe'. DS. $code. DS. $tpl. '.php';
        } elseif(file_exists(S_TEMPLATES_DIR. $defaultPlugTheme. DS. $code. DS. $tpl. '.php')) {                             //Look in templates directory at first
            $path = S_TEMPLATES_DIR. $defaultPlugTheme. DS. $code. DS. $tpl. '.php';
        } elseif(file_exists($parentModule->getModDir(). 'views'. DS. 'tpl'. DS. $tpl. '.php')) { //Then try to find it in module directory
            $path = $parentModule->getModDir(). DS. 'views'. DS. 'tpl'. DS. $tpl. '.php';
        }
		return $path;
	}
    public function getContent($tpl = '') {
        $tpl = (empty($tpl)) ? $this->_tpl : $tpl;
        //$code = $this->_code;
        //$parentModule = frame::_()->getModule( $this->_code );
        $path = $this->getPath($tpl);
        //$defaultPlugTheme = frame::_()->getModule('options')->getModel()->get('default_theme');
        /*if(!empty($this->_theme) && file_exists(S_TEMPLATES_DIR. $this->_theme. DS. $code. DS. $tpl. '.php')) {
            $path = S_TEMPLATES_DIR. $this->_theme. DS. $code. DS. $tpl. '.php';
        } elseif(file_exists(S_TEMPLATES_DIR. $defaultPlugTheme. DS. $code. DS. $tpl. '.php')) {                             //Look in templates directory at first
            $path = S_TEMPLATES_DIR. $defaultPlugTheme. DS. $code. DS. $tpl. '.php';
        } elseif(file_exists(utils::getCurrentWPThemeDir(). 'toe'. DS. $code. DS. $tpl. '.php')) {
            $path = utils::getCurrentWPThemeDir(). 'toe'. DS. $code. DS. $tpl. '.php';
        } elseif(file_exists($parentModule->getModDir(). 'views'. DS. 'tpl'. DS. $tpl. '.php')) { //Then try to find it in module directory
            $path = $parentModule->getModDir(). DS. 'views'. DS. 'tpl'. DS. $tpl. '.php';
        }*/ /*elseif(file_exists($tpl)) {
			$path = $tpl;
		}*/
        
        if($path) {
            $content = '';
            ob_start();
            require($path);
            $content = ob_get_contents();
            ob_end_clean();
            
            /* Check errors */
            /**
             * @deprecated moved to errors class
             */
           /* $errors = req::getVar('toeErrors');
            if(!empty($errors)) {
                if(!is_array($errors))
                    $errors = array($errors);
                $content = '<div class="toeErrorMsg">'. implode('<br />', $errors). '</div>'. $content;
                req::clearVar('toeErrors');
            }*/
            /*****/
            return $content;
        }
        return false;
    }
    public function setTheme($theme) {
        $this->_theme = $theme;
    }
    public function getTheme() {
        return $this->_theme;
    }
    public function setTpl($tpl) {
        $this->_tpl = $tpl;
    }
    public function getTpl() {
        return $this->_tpl;
    }
    public function init() {

    }
    public function assign($name, $value) {
        $this->$name = $value;
    }
    public function setCode($code) {
        $this->_code = $code;
    }
    public function getCode() {
        return $this->_code;
    }
	
	/**
	 * This will display form for our widgets
	 */
	public function displayWidgetForm($data = array(), $widget = array(), $formTpl = 'form') {
		$this->assign('data', $data);
        $this->assign('widget', $widget);
		if(frame::_()->isTplEditor()) {
			if($this->getPath($formTpl. '_ext')) {
				$formTpl .= '_ext';
			}
		}
		self::display($formTpl);
	}
	public function getModule() {
		return frame::_()->getModule( $this->_code );
	}
	public function getModel() {
		return frame::_()->getModule( $this->_code )->getController()->getModel();
	}
}
?>