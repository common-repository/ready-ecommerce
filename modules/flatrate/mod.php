<?php
class toecFlatrate extends shippingModule {
    public function init() {
        parent::init();
        $this->_rate = $this->_params->rate;
    }
}