<?php
/**
 * Products Fields view class
 */
class products_fieldsView extends view {
	public function getTabContent($d = array()) {
		$this->assign('availableHtmlTypes', array(
			1 => 'Text',
			17 => 'Textarea',
			9 => 'Drop Down',
			5 => 'Checkboxes',
			12 => 'Select List',
			10 => 'Radio Buttons',
		));
		$pid = isset($d['pid']) ? (int) $d['pid'] : 0;
		if($pid)
			$this->assign('fieldsList', $this->getModel()->getForProduct(array('pid' => $pid)));
		else
			$this->assign('fieldsList', $this->getModel()->getList(array('original_id' => 0)));
		$this->assign('pid', $pid);
		return parent::getContent('productFieldsTab');
	}
}
