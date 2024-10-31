<?php
class dummy_dataView extends view {
	public function getTabContent(){
			return parent::getContent('dummyDataTab');		
	}
   /**
    * Will show notice about availability of installation of dummy products
    */
   public function adminInstallNotice() {
	   parent::display('dummyAdminInstallNotice');
   }
}
