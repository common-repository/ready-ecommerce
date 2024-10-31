<?php
class statesView extends view {
	public function getTabContent() {
		$allStates = fieldAdapter::getCachedStates();
		/*foreach($allStates as $i => $s) {
			$allStates[$i]['c_iso_code_3'] = fieldAdapter::displayCountry($s['country_id'], 'iso_code_3');
		}*/
		$this->assign('allStates', $allStates);
		return parent::getContent('statesTab');
	}
}
?>
