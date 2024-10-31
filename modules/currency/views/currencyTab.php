<?php
/**
 * Class for currency module tab at options page
 */
class currencyTabView extends view {
    /**
     * Get the content for currency module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       $currency = frame::_()->getModule('currency')->getModel()->get();
       $this->assign('currency', $currency);
       $output = '<div id="currency_form" class="tab_form">'.frame::_()->getModule('currency')->getView('currency')->addCurrency().'</div>';
       $output .= parent::getContent('currencyTab');
       return $output;
   }
}

?>
