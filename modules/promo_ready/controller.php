<?php
class promo_readyController extends controller {
	public function welcomePageSaveInfo() {
		$res = new response();
		// Start usage in any case
		installer::setUsed();
		if($this->getModel()->welcomePageSaveInfo(req::get('get'))) {
			$res->addMessage(lang::_('Information was saved. Thank you!'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		// Start usage in any case
		$originalPage = req::getVar('original_page');
		$returnArr = explode('|', $originalPage);
		$return = $this->getModule()->decodeSlug(str_replace('return=', '', $returnArr[1]));
		$return = admin_url( strpos($return, '?') ? $return : 'admin.php?page='. $return);
		redirect($return);
		//return $res->ajaxExec();
	}
	public function saveUsageStat() {
		$res = new response();
		$code = req::getVar('code');
		if($code)
			$this->getModel()->saveUsageStat($code);
		return $res->ajaxExec();
	}
	public function sendUsageStat() {
		$res = new response();
		$this->getModel()->sendUsageStat();
		$res->addMessage(lang::_('Information was saved. Thank you for your support!'));
		return $res->ajaxExec();
	}
	public function hideUsageStat() {
		$res = new response();
		$this->getModule()->setUserHidedSendStats();
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			S_USERLEVELS => array(
				S_ADMIN => array('welcomePageSaveInfo', 'saveUsageStat', 'sendUsageStat', 'hideUsageStat')
			),
		);
	}
}