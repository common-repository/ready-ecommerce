<?php


class dummy_dataController extends controller {
	public function installProducts() {
		$res = new response();
		if($this->getModel()->installProducts()) {
			$res->addMessage(lang::_('Dummy Products was installed'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function removeProducts() {
		$res = new response();
		if($this->getModel()->removeProducts()) {
			$res->addMessage(lang::_('Dummy Products was removed'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function reactivateWidgets() {
		$res = new response();
		if($this->getModel()->reactivateWidgets()) {
			$res->addMessage(lang::_('Widgets was reactivated'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function removeDummyDataInstallNotice() {
		$res = new response();
		if(!$this->getModel()->removeDummyDataInstallNotice()) {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
}
