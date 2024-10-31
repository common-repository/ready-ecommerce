<?php
class optionsView extends view {
    public function getAdminPage() {
        $modules = array(
            'all' => array(),
        );
        $modules['all'] = frame::_()->getModule('options')->getModel('modules')->get();
        foreach($modules['all'] as $m) {
            $modules[$m['type']][] = $m;
        }
		$modules = dispatcher::applyFilters('adminOptModulesList', $modules);
        $tabs = frame::_()->getModule('options')->getModel('modules')->getTabs($modules['all']);
		
        $this->assign('modules', $modules);
        $this->assign('tabs', $tabs);
		
        parent::display('adminPage');
    }
}