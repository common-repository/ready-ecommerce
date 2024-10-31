<?php
/**
 * Deprecated class
 */
class route {
   /* static public function _($params) {
        $link = '';
        $query = is_array($params) ? http_build_query($params) : $params;
        $link = S_URL. (strpos(S_URL, '?') === false ? '?' : '&'). $query;
        return $link;
    }
    static public function _e($params) {
        echo self::_($params);
    }
    static public function getGetParams($exclude = array()) {
        $res = array();
        if(isset($_GET) && !empty($_GET)) {
            foreach($_GET as $key => $val) {
                if(in_array($key, $exclude)) continue;
                $res[$key] = $val;
            }
        }
        return $res;
    }
    static public function atach($params) {
        $params = array_merge(self::getGetParams(), $params);
        return self::_($params);
    }*/
    static public function getModule($post_id) {
        /*$categories = wp_get_post_categories($post_id);
        $module = NULL;
        if(in_array(KINO_VIDEO_CATEGORY, $categories)) {
                $module = new video(array('post_id' => $post_id));
        } elseif(in_array(KINO_PERSONALITY_CATEGORY, $categories)) {
                $module = new personality(array('post_id' => $post_id));
        } elseif(in_array(KINO_SOUNDTRACK_CATEGORY, $categories)) {
                $module = new soundtrack(array('post_id' => $post_id));
        }
        if($module) {
                $module->init();
        }
        return $module;*/
    }
}
?>