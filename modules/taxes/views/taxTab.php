<?php
/**
 * Class for tax module tab at options page
 */
class taxTabView extends view {
    /**
     * Get the content for tax module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       $taxes = frame::_()->getModule('taxes')->getModel()->get();
       $this->assign('taxes', $taxes);
       $output = '<div id="tax_form" class="tab_form">'.frame::_()->getModule('taxes')->getView('taxes')->addTax().'</div>';
       $output .= parent::getContent('taxTab');
       return $output;
   }
}

?>