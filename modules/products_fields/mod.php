<?php
/**
 * Products Fields Module Class
 */
class products_fields extends module {
    public function getTabs(){
        $tabs = array();
		if(frame::_()->getModule('options')->get('use_new_products_fields')) {
			$tab = new tab(lang::_('Product Fields'), $this->getCode());
			$tab->setView('products_fields');
			$tab->setSortOrder(4);
			$tab->setParent('templates');
			$tab->setNestingLevel(1);
			$tabs[] = $tab;
			frame::_()->addScript('productFieldsAdmin', S_JS_PATH. 'productFieldsAdmin.js');
		}
        return $tabs;
    }
}