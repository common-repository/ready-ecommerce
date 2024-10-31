<?php
class countriesController extends controller {
	public function save() {
		$res = new response();
		if($id = $this->getModel()->save(req::get('post'))) {
			$res->addData(array('id' => $id));
			$res->addMessage(lang::_('Country was saved'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function remove() {
		$res = new response();
		if($this->getModel()->remove(req::get('post'))) {
			$res->addMessage(lang::_('Country was removed'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function restore() {
		$res = new response();
		if($this->getModel()->restore(req::get('post'))) {
			$res->addData(fieldAdapter::getCachedCountries(true));
			$res->addMessage(lang::_('Countries was restored to default'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			S_USERLEVELS => array(
				S_ADMIN => array('save', 'remove', 'restore')
			),
		);
	}
}

