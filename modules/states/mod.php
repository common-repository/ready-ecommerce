<?php
class states extends module {
	/**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('States'), $this->getCode());
        $tab->setView('states');
        $tabs[] = $tab;
        return $tabs;
    }
}
?>
