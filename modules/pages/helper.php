<?php
/**
 * pagesHelper class for field's elements
 */
class pagesHelper extends toechelper {
   /**
    * Returns the list of all active modules with their codes and labels
    * 
    * @global global $wpdb
    * @return array 
    */
    function getModules(){
        global $wpdb;
        $items = $wpdb->get_results("SELECT code, label FROM `".$wpdb->prefix.S_DB_PREF."modules` WHERE `active` = 1");
        $modules = array();
        foreach ($items as $item) {
            $modules[$item->code] = $item->label;
        }
        return $modules;
    }
   /**
    * Returns the list of pages of the site
    * @global global $wpdb
    * @return array 
    */
    function getPages() {
        global $wpdb;
        $items = $wpdb->get_results("SELECT ID, post_title FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'page' AND `post_status` = 'publish'");
        $pages = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $pages[$item->ID] = $item->post_title;
            }
        }
        return $pages;
    }
}