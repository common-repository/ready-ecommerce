<?php
/**
 * Class for checkout module tab at options page
 */
class checkoutTabView extends view {
    /**
     * Get the content for checkout module tab
     * 
     * @return type 
     */
   public function getTabContent(){
       $checkoutSuccessText = toeCreateObj('field', array('checkout_success_text', 'textarea'));
       $checkoutSuccessText->setValue( frame::_()->getModule('options')->get('checkout_success_text') );
       $checkoutSuccessText->addHtmlParam('cols', 105);
       $checkoutSuccessText->addHtmlParam('rows', 10);
	   $fromDb = frame::_()->getModule('options')->get('terms');
       $fromDb = utils::jsonDecode($fromDb);
	   $turl = "";
	   $ttext = "";
	   if(!empty($fromDb)) {
	   $turl = $fromDb['termsURL'];
	   $ttext = $fromDb['termsText'];
	   }
       $this->assign('termsURL', $turl);
       $this->assign('termsText', $ttext);
       $this->assign('cartColumns', frame::_()->getModule('user')->getModel('cart')->getColumns());
       $this->assign('steps', frame::_()->getModule('checkout')->getSteps());
       $this->assign('checkoutSuccessText', $checkoutSuccessText);
       $this->assign('checkoutSuccessVariables', frame::_()->getModule('order')->getModel()->getSuccessVariables(array('getKeysOnly' => true)));
       $output = parent::getContent('checkoutTab');
       return $output;
   }
}
?>
