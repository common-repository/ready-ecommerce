<?php
class pagesController extends controller {
    public function recreatePages() {
		$res = new response();
		if($this->getModel()->recreatePages()) {
			$res->addMessage(lang::_('Pages was recreated'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			S_USERLEVELS => array(
				S_ADMIN => array('recreatePages')
			),
		);
	}
}

