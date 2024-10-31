<?php
class countriesView extends view {
	public function getTabContent() {
		$allCountries = fieldAdapter::getCachedCountries();
		$this->assign('allCountries', $allCountries);
		return parent::getContent('countriesTab');
	}
}
?>
