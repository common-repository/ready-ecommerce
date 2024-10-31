<?php
class countries extends module {
	/**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('Countries'), $this->getCode());
        $tab->setView('countries');
        $tabs[] = $tab;
        return $tabs;
    }
}