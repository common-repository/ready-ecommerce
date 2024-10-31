<?php
class mvcHelp {
    static public function getModule($obj, $for = S_CURRENT) {
        $parent = self::getParent($obj);
        if(!$parent) return false;      //Must be overwritten to use file path from trace
        $moduleName = '';
        switch($parent) {
            case 'model':
            case 'view':
            case 'controller':
                $moduleName = self::_getModuleFromClassName(
                        self::getCurrentClass($obj)
                    );
                break;
        }
        if($moduleName) {
            return frame::_()->getModule($moduleName);
        }
        return NULL;
    }
    static public function getParent($obj) {
        return get_parent_class($obj);
    }
    static public function getCurrentClass($obj) {
        return get_class($obj);
    }
    /**
     * Not working
     */
    static private function _getModuleFromModelPath($path) {
        if(!is_array($path)) $path = explode(DS, $path);
        $modName = $path[count($path) - 3];
        return $modName;
    }
    /**
     * Not working
     */
    static private function _getModuleFromViewPath($path) {
        if(!is_array($path)) $path = explode(DS, $path);
        $modName = $path[count($path) - 3];
        return $modName;
    }
    /**
     * Not working
     */
    static private function _getModuleFromControllerPath($path) {
        if(!is_array($path)) $path = explode(DS, $path);
        $modName = $path[count($path) - 2];
        return $modName;
    }
    
    static private function _getModuleFromClassName($class) {
        return strtolower(str_replace(array(
            'Model',
            'View',
            'Controller',
            'admin_'
        ), '', $class));
    }
}
?>
