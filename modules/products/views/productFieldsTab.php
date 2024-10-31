<?php
/**
 * Class for product module tab at options page
 */
class productFieldsTabView extends view {
    /**
     * Get the content for product module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       $productfields = frame::_()->getModule('options')->getModel('productfields')->get();
       $this->assign('productfields', $productfields);
       $output = '<div id="product_field_form" class="tab_form">'.frame::_()->getModule('options')->getView('productfields')->addProductfields().'</div>';
       $output .= parent::getContent('productFieldsTab');
       return $output;
   }
}

?>
