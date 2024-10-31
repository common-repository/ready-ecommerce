<?php
class dispatcher {
    static protected $_events = array();
    static protected $_pref = 'toe_';
    /**
     * Add event to dispacher
     * @param string $event name of event
     * @param mixed $params if string given - name of a function, if array - first element must be an object, second - object's method (must be public)
     */
    static public function add($event, $params) {
        if(!isset(self::$_events[$event])) self::$_events[$event] = array();
        self::$_events[$event][] = array('do' => $params);
    }
    /**
     * Metod execute some edded earlier event
     * @param string $event event name
     */
    static public function exec($event, $data = array()) {
        if(!empty(self::$_events[$event])) {
            foreach(self::$_events[$event] as $e) {
                $do = $e['do'];
                if(is_array($do)) {
                    if(is_object($do[0]) && method_exists($do[0], $do[1])) {
                        $method = $do[1];
                        $do[0]->$method($data);
                    }
                } else {
                    if(function_exists($do)) {
                        $e($data);
                    }
                }
            }
        }
    }
    static public function addAction($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
        if(strpos($tag, 'toe_') === false) 
            $tag = self::$_pref. $tag;
        return add_action( $tag, $function_to_add, $priority, $accepted_args );
    }
    static public function doAction($tag) {
        if(strpos($tag, 'toe_') === false)
            $tag = self::$_pref. $tag;
        $numArgs = func_num_args();
        if($numArgs > 2) {
            $args = array();
            for($i = 1; $i < $numArgs; $i++) {
                $args[] = func_get_arg($i);
            }
        } elseif($numArgs == 2) {
            $args = func_get_arg(1);
        } else
            $args = NULL;
        return do_action($tag, $args);
    }
    static public function addFilter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
        if(strpos($tag, 'toe_') === false)
            $tag = self::$_pref. $tag;
        return add_filter( $tag, $function_to_add, $priority, $accepted_args );
    }
    static public function applyFilters($tag, $value) {
        if(strpos($tag, 'toe_') === false)
            $tag = self::$_pref. $tag;
        if(func_num_args() > 2) {
            $args = array($tag);
            for($i = 1; $i < func_num_args(); $i++) {
                $args[] = func_get_arg($i);
            }
            return call_user_func_array('apply_filters', $args);
        } else {
            return apply_filters( $tag, $value );
        }
    }
}
?>