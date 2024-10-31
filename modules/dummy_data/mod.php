<?php
class dummy_data extends module {
	protected $_dummyFileName = 'toe_dummy_data.xml';
	public function init() {
		if(!$this->getModel()->wasDummyProductsInstalled() && !$this->getModel()->hideDummyDataInstallNotice()) {
			add_action('admin_notices',array($this->getView(), 'adminInstallNotice'));
		}
		add_action('admin_menu', array($this, 'setup_theme_admin_menus'));
		if(is_admin()) {
			frame::_()->addScript('toeDummyData', $this->getModPath(). 'js/dummyData.js');
			frame::_()->addStyle('toeDummyData', $this->getModPath(). 'css/dummyData.css');
		}
	}
	public function setup_theme_admin_menus() {
		add_theme_page('', lang::_('Install Products Dummy Data'), 'manage_options', 'toe_install_prod_dummy_data', '');
	}
	/**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('Dummy Data'), $this->getCode());
        $tab->setView('dummy_data');
        $tab->setSortOrder(20);
        $tabs[] = $tab;
        return $tabs;
    }
	public function getDummyFileName() {
		return $this->_dummyFileName;
	}
	/**
	 * Retrive location of dummy file in file system
	 * @return string location of dummy file
	 */
	 public function getDummyDataTemplatePath(){
		return get_template_directory() . DS . 'toe/theme_modules/dummy_data/data_file/';
	}
	public function getDefaultDummyDataFile(){
		return $this->getModDir(). 'data_file'. DS. $this->_dummyFileName; 
	}
	public function getDummyFilePath() {

		return $this->getDummyDataTemplatePath() . $this->getDummyFileName();
	}
	/**
	 * Retrive location of dummy images directory in file system
	 * @return string location of dummy images directory
	 */
	public function getDummyImgDir() {
		return $this->getModDir(). 'data_file'. DS. 'img'. DS;
	}
}
?>
