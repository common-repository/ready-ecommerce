<?php
/**
 * Class for options module tab at options page
 */
class optionTabView extends view {
    /**
     * Get the content for options module tab
     * 
     * @return type 
     */
   public function getTabContent(){
	   // Save tab usage
	   frame::_()->getModule('promo_ready')->saveUsageStat('option.optionTab');
       $options = frame::_()->getModule('options')->getModel('options')->getByCategories();
       $this->assign('options', $options);
       return parent::getContent('optionTab');
   }
}
