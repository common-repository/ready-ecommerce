<?php
class messengerView extends view {
	public function displayAdminModActivationNotices($notices = array()) {
		$this->assign('notices', $notices);
		parent::display('adminModActivationNotices');
	}
	public function displayModActivationPopup() {
		parent::display('modActivationPopup');
	}
}