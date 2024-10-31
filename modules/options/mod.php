<?php
class toecoptions extends module {
    /**
     * Method to trigger the database update
     */
    public function init(){
        parent::init();
        
        $add_option = array(
            'add_checkbox' => lang::_('Add Checkbox'),
            'add_radiobutton' => lang::_('Add Radio Button'),
            'add_item' => lang::_('Add Item'),
        );
        frame::_()->addJSVar('adminOptions', 'TOE_LANG', $add_option);
    }
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('General'), $this->getCode());
        $tab->setView('optionTab');
        $tab->setSortOrder(-99);
        $tabs[] = $tab;
        return $tabs;
    }
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($d = array()) {
        return $this->getController()->getModel()->get($d);
    }
	
	public function isEmpty($d = array()) {
		$optionValue = $this->get($d);
		return empty($optionValue);
	}
	public function getAllowedPublicOptions() {
		$res = array();
		$alowedForPublic = array('store_name', 'dialog_after_prod_add', 'shipp_same_as_bill');
		$allOptions = $this->getModel()->getByCode();
		foreach($alowedForPublic as $code) {
			if(isset($allOptions[ $code ]))
				$res[ $code ] = $allOptions[ $code ];
		}
		return $res;
	}
}

