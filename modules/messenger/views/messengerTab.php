<?php

/**
 * Class for messenger module tab at options page
 */
class messengerTabView extends view {
    /**
     * Get the content for messenger module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       $templates = frame::_()->getModule('messenger')->getModel('email_templates')->getAll();
       $this->assign('templates', $templates);
       return parent::getContent('messengerTab');
   }
   /**
    * Get Edit Template Form
    * @return string 
    */
   public function editTemplate() {
       $id = req::getVar('id');
       if (!is_numeric($id)) {
           return false;
       }
       $template = frame::_()->getModule('messenger')->getModel('email_templates')->get(array('id' => $id));
       
       $this->assign('template', $template);
       $this->assign('id', $id);
       $this->assign('method', 'put');
       return parent::getContent('editTemplate');
    }
}
?>
