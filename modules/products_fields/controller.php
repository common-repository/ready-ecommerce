<?php
class products_fieldsController extends controller {
	public function saveProductField() {
		$res = new response();
		$post = req::get('post');
		if(($id = $this->getModel()->saveProductField($post))) {
			$res->addData('product_field', $this->getModel()->getById($id));
			if(isset($post['pid']) && (int) $post['pid']) {
				$res->addData('product_fields_list', $this->getModel()->getForProduct(array('pid' => $post['pid'])));
			}
			$res->addMessage(lang::_('Product Field was Saved'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function removeProductField() {
		$res = new response();
		if($this->getModel()->removeProductField(req::get('post'))) {
			$res->addMessage(lang::_('Product Field was Removed'));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function getTabContent() {
		$res = new response();
		if(($tabHtml = $this->getView()->getTabContent(req::get('post')))) {
			$res->setHtml($tabHtml);
		} else {
			$res->pushError($this->getView()->getErrors());
		}
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			S_USERLEVELS => array(
				S_ADMIN => array('saveProductField', 'removeProductField', 'getTabContent')
			),
		);
	}
}
