<?php
class shippingView extends view {
    public function getTabContent() {
       $allShippingModules = frame::_()->getModule('shipping')->getModel()->get();
       $fields = frame::_()->getTable('shipping')->getFields();
       $shippigEngines = frame::_()->getModules(array('type' => 'shipping'));
       if(!empty($shippigEngines)) {
           $defaultShippingOptions = frame::_()->getModule('shipping')->getDefaultOptions();
           $codes = array();
           $configInputs = array();
           foreach($shippigEngines as $code => $eng) {
               $codes[$code] = is_object($eng) ? $eng->getLabel() : $code;
               $configInputs[$code] = toeCreateObj('field', array($code));
               $paramsConfigPatterns[$code] = $configInputs[$code]->drawConfig( $code, $defaultShippingOptions );
           }
           $fields['code']->addHtmlParam('options', $codes);

           foreach($allShippingModules as $i => $mData) {
			   if(!isset($configInputs[ $mData['code'] ]) || !is_object($configInputs[ $mData['code'] ])) continue;
               $confInput = clone($configInputs[ $mData['code'] ]);
               $confInput->setValue( $mData['params'] );
               $allShippingModules[$i]['params'] = $confInput->drawConfig( $mData['code'], $defaultShippingOptions );
           }

           $this->assign('fields', $fields);
           $this->assign('allShippingModules', $allShippingModules);
           $this->assign('paramsConfigPatterns', $paramsConfigPatterns);
           return parent::getContent('shippingAdminTab');
       } else
           return parent::getContent('noShippingModulesInstalled');
   }
}

