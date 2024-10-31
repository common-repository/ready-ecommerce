<?php
/**
 * Class for templates module tab at options page
 */
class templatesTabView extends view {
    /**
     * Get the content for templates module tab
     * 
     * @return type 
     */
    public function getTabContent(){
       $templates = frame::_()->getModule('templates')->getModel()->get();
       if(empty($templates)) {
           $tpl = 'noTemplates';
       } else {
           $this->assign('templates', dispatcher::applyFilters('templatesListToAdminTab', $templates));
           $this->assign('default_theme', frame::_()->getModule('options')->getModel()->get('default_theme'));
           $tpl = 'templatesTab';
       }
       return parent::getContent($tpl);
   }
}
