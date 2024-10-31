<?php
class shipping extends module {
    /**
     * Engine Instances
     */
    protected $_engInstances = array();
    /**
     * Default options, that will be used for all shipping modules
     */
    protected $_defaultOptions = array(
        'country_to_ship' => array('type' => 'countryListMultiple', 'label' => 'Country to ship', 'htmlParams' => array('notSelected' => 'All Countries')),
        'state_to_ship' => array('type' => 'statesListMultiple', 'label' => 'State to ship', 'htmlParams' => array('doNotAddJs' => true, 'notSelected' => 'All States')),
        'order_price_boundary' => array('type' => 'text', 'label' => 'If order price is more than:', 'default' => '0'),
        'order_price_decrease' => array('type' => 'text', 'label' => 'price for shipping will decrease on:', 'default' => '0'),
        'order_price_free_shipping' => array('type' => 'text', 'label' => 'If order price is more than - shipping is free', 'default' => '0'),
    );
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('Shipping'), $this->getCode());
        $tab->setView('shipping');
        $tabs[] = $tab;
        return $tabs;
    }
    public function moduleActive($d = 0) {
        $moduleData = $this->getController()->getModel()->get($d);
        if(empty($moduleData) || empty($moduleData['code']) || !frame::_()->getModule($moduleData['code']))
            return false;
        return (bool) $moduleData['active'];
    }
    /*public function getRate($d = array()) {
        
    }*/
    /*public function allowShipping() {
        
    }*/
    public function getEngineFor($id = 0) {
        if(!isset($this->_engInstances[$id])) {
            $moduleData = $this->getController()->getModel()->get($id);
            if(empty($moduleData))
                return false;
            if(!frame::_()->getModule($moduleData['code']))
                return false;
            $this->_engInstances[$id] = clone(frame::_()->getModule($moduleData['code']));
            $this->_engInstances[$id]->setParams($moduleData['params']);
            $this->_engInstances[$id]->setRate(0);
            $this->_engInstances[$id]->init();
        }
        return $this->_engInstances[$id];
    }
    public function getDefaultOptions() {
        return $this->_defaultOptions;
    }
	/**
	 * Additional shipping options that need to be entered by user
	 */
	public function getAdditionalOptionsHtml($id = 0) {
		if(!empty($id) && $this->moduleActive($id)) {
			if($this->getEngineFor($id)->haveAdditionalOptions())
				return $this->getEngineFor($id)->getAdditionalOptionsHtml();
		}
		return false;
	}
}

