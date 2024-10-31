<?php
/**
 * Class for search_widget module
 * Module represents the search widget
 */
class search_widget extends module {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
        add_filter('posts_where_request', array($this, 'searchQueryConditions'));
    }
    public function registerWidget() {
        return register_widget('toeSearchWidget');
    }
    public function searchQueryConditions($where) {
        global $wpdb;
        $toeSearch = req::getVar('toeSearch');
        if(!empty($toeSearch) && is_array($toeSearch)) {
            $pName = frame::_()->getTable('products')->getTable(true);
            
            if(isset($toeSearch['priceFrom'])) {
                $toeSearch['priceFrom'] = (float) $toeSearch['priceFrom'];
                if($toeSearch['priceFrom'])
                    $where .= ' AND '. $pName. '.price >= '. $toeSearch['priceFrom'];
            }
            if(isset($toeSearch['priceTo'])) {
                $toeSearch['priceTo'] = (float) $toeSearch['priceTo'];
                if($toeSearch['priceTo'])
                    $where .= ' AND '. $pName. '.price <= '. $toeSearch['priceTo'];
            }
            if(!empty($toeSearch['exOptions']) && is_array($toeSearch['exOptions'])) {
                $efValName = frame::_()->getTable('extrafieldsvalue')->getTable(true);
                $toeSearch['exOptions'] = array_map('intval', $toeSearch['exOptions']);
                $exFields = array_map('intval', array_keys($toeSearch['exOptions']));
                $exFieldsDesc = frame::_()->getModule('options')->getModel('productfields')->getFieldsDesc(array('options' => $toeSearch['exOptions']));
                $destCategories = array();
                if(!empty($exFieldsDesc)) {
                    foreach($exFieldsDesc as $d) {
                        if(!empty($d['destination']['categories']) && is_array($d['destination']['categories']))
                            $destCategories = array_merge($destCategories, $d['destination']['categories']);
                    }
                }
                if(!empty($destCategories)) {
                    $where .= ' AND '. $wpdb->posts. '.ID IN (SELECT object_id FROM '. $wpdb->term_relationships. ' WHERE term_taxonomy_id IN ('. implode(', ', $destCategories). '))';
                }
                //Excluded options
                $where .= ' AND '. $wpdb->posts. '.ID NOT IN (SELECT parent_id FROM '. $efValName. ' WHERE opt_id IN ('. implode(', ', $toeSearch['exOptions']). ') AND disabled = 1)';
                //$where .= ' AND '. $pName. '.post_id NOT IN (SELECT pid FROM '. $optExcludeName.' WHERE oid IN ('. implode(', ', $toeSearch['exOptions']). '))';
            }
        }
        return $where;
    }
}
/**
 * Search Widget Class
 */
class toeSearchWidget extends toeWordpressWidget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeSearchWidget', 
            'description' => lang::_('Displays Recent Products block')
        );
        $control_ops = array(
            'id_base' => 'toeSearchWidget'
        );
	parent::__construct( 'toeSearchWidget', lang::_('Ready! Search'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frame::_()->getModule('search_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frame::_()->getModule('search_widget')->getView()->displayForm($instance, $this);
    }
}
?>
