<?php
    echo html::formStart('toeSliderWidgetProductSelect', array('attrs' => 'onsubmit="toeSliderSelectProducts(this); return false;"'));
    lang::_e('Use Products:');
    echo '<br />';
    echo html::selectlist('products', array('options' => frame::_()->getModule('products')->getHelper()->getProducts()));
    echo '<br />';
    echo html::hidden('uniqBoxId', array('value' => $this->uniqBoxId));
    echo html::submit('select', array('value' => lang::_('Select')));
    echo html::formEnd();
?>