<?php
class wpUpdater {
	protected $_plugDir = '';
	protected $_plugFile = '';
	protected $_plugSlug = '';
	
    public function __construct($pluginDir, $pluginFile = '', $pluginSlug = '') {
		$this->_plugDir = $pluginDir;
		$this->_plugFile = $pluginFile;
		$this->_plugSlug = $pluginSlug;
    }
    static public function getInstance($pluginDir, $pluginFile = '', $pluginSlug = '') {
        static $instances = array();
		// Instance key
		$instKey = $pluginDir. '/'. $pluginFile;
        if(!isset($instances[ $instKey ])) {
            $instances[ $instKey ] = new wpUpdater($pluginDir, $pluginFile, $pluginSlug);
        }
        return $instances[ $instKey ];
    }
    public function checkForPluginUpdate($checkedData) {
		return $checkedData;
    }
    public function myPluginApiCall($def, $action, $args) {
		return $def;
    }
}
