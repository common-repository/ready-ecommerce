<?php
class toectaxes extends module {
    public function get() {
        return $this->getController()->getModel()->get();
    }
    public function getForOrder() {
        
    }
   /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('Taxes'), $this->getCode());
        $tab->setView('taxTab');
        $tabs[] = $tab;
        return $tabs;
    }
}