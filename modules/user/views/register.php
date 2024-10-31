<?php
class registerView extends view {
    
    /**
     * Get login form
     * @param string $pageContent - used for WP, here it store primary page content
     * @param string $d['fieldsOnly'] - if true - no "form" tag will be specified
     * @param string $d['fieldsSpace'] - use to make all user fields names like array, $d['fieldsSpace']['someFieldName'], @see checkoutView::getAll()
     * @return string - html registration content
     */
    public function getContent($pageContent = '', $d = array('fieldsOnly' => false, 'fieldsSpace' => '') , $tpl = 'registrationForm') {
        $metaFields = frame::_()->getModule('user')->getModel('user')->getUserMeta(0, 'registration');
        //$standartFields = frame::_()->getModule('user')->getModel()->getStandartFieldsAsObjects();
        if(!empty($d['fieldsSpace'])) {
            /*foreach($standartFields as $f) {
                $f->setName($d['fieldsSpace']. '['. $f->getName(). ']');
                $f->setErrorEl(true);
            }*/
            if(!empty($metaFields)) {
                foreach($metaFields as $f) {
                    $f->setName($d['fieldsSpace']. '['. $f->getName(). ']');
                    $f->setErrorEl(true);
                }
            }
        }
        //$this->assign('standartFields', $standartFields);
        $this->assign('checkoutSkipRegister', (int) frame::_()->getModule('options')->get('allow_guest_checkout'));
        $this->assign('metaFields', $metaFields);
        $this->assign('toeReturn', req::getVar('toeReturn'));
        $this->assign('fieldsOnly', $d['fieldsOnly']);
        return parent::getContent('registrationForm');
    }
}
?>
