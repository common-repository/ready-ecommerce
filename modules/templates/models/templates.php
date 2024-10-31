<?php
class templatesModel extends model {
    protected $_allTemplates = array();
    public function get($d = array()) {
        parent::get($d);
        if(empty($this->_allTemplates)) {
            $directories = utils::getDirList(S_TEMPLATES_DIR);
            if(!empty($directories)) {
                foreach($directories as $code => $dir) {
                    if($xml = utils::getXml($dir['path']. 'settings.xml')) {
                        $this->_allTemplates[$code] = $xml;
                        $this->_allTemplates[$code]->prevImg = S_TEMPLATES_PATH. $code. '/screenshot.png';
                    }
                }
            }
            if(is_dir( utils::getCurrentWPThemeDir(). 'toe'. DS )) {
                if($xml = utils::getXml( utils::getCurrentWPThemeDir(). 'toe'. DS. 'settings.xml' )) {
                    $code = utils::getCurrentWPThemeCode();
					if(strpos($code, '/') !== false) {	// If theme is in sub-folder
						$code = explode('/', $code);
						$code = trim( $code[count($code)-1] );
					}
                    $this->_allTemplates[$code] = $xml;
					if(is_file(utils::getCurrentWPThemeDir(). 'screenshot.jpg'))
						$this->_allTemplates[$code]->prevImg = utils::getCurrentWPThemePath(). '/screenshot.jpg';
					else
						$this->_allTemplates[$code]->prevImg = utils::getCurrentWPThemePath(). '/screenshot.png';
                }
            }
        }
        if(isset($d['code']) && isset($this->_allTemplates[ $d['code'] ]))
            return $this->_allTemplates[ $d['code'] ];
        return $this->_allTemplates;
    }
}
?>