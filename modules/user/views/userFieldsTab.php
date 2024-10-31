<?php
/**
 * Class for user module tab at options page
 */
class userFieldsTabView extends view {
    /**
     * Get the content for user module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       $userfields = frame::_()->getModule('options')->getModel('userfields')->get();
       $usserfieldsArray = array();
       foreach($userfields as $uf)
           $usserfieldsArray[] = $uf;
       $this->assign('userfieldsList', $usserfieldsArray);
       $output = '<div id="user_field_form" class="tab_form">'.frame::_()->getModule('options')->getView('userfields')->addUserfields().'</div>';
       $output .= parent::getContent('userFieldsTab');
       return $output;
   }
}

?>
