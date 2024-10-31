<?php
class loginView extends view {
    /**
     * Get login form
     * @param string $pageContent - used for WP, here it store primary page content
     * @param string $d['fieldsOnly'] - if true - no "form" tag will be specified
     */
    public function getContent($pageContent = '', $d = array('fieldsOnly' => false, 'toeReturn' => '', 'hideRegistrationLink' => false)) {
        $toeReturn = req::getVar('toeReturn');
        if(empty($toeReturn) && !empty($d['toeReturn']))
            $toeReturn = $d['toeReturn'];
        
        if(!$d['hideRegistrationLink']) {
            $dataForRegLink = array('mod' => 'user', 'action' => 'getRegisterForm');
            if(!empty($toeReturn)) {
                $dataForRegLink['data'] = array('toeReturn' => $toeReturn);
            }
            $this->assign('registrationLink', frame::_()->getModule('pages')->getLink($dataForRegLink));
        }
        $this->assign('toeReturn', $toeReturn);
        $this->assign('fieldsOnly', $d['fieldsOnly']);
        return parent::getContent('loginForm');
    }
	public function getPasswordForgotFormHtml() {
		return parent::getContent('passwordForgot');
	}
}
?>
