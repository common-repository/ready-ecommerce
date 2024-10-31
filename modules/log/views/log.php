<?php
class logView extends view {
    public function getList() {
		// Save tab usage
	    frame::_()->getModule('promo_ready')->saveUsageStat('log.getList');
		
        $this->assign('logs', frame::_()->getModule('log')->getModel()->getSorted());
        $this->assign('logTypes', frame::_()->getModule('log')->getModel()->getTypes());
        parent::display('logList');
    }
}