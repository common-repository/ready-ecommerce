<?php
/**
 * Class for pages module tab at options page
 */
class pagesTabView extends view {
    /**
     * Get the content for pages module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       return parent::getContent('pagesTab');;
   }
}
?>