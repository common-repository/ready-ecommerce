<?php
class tableOrders extends table {
    public function __construct() {
    $this->_table = '@__orders';
        $this->_id = 'id';
        $this->_alias = 'toe_o';
        $this->_addField('id', 'text', 'int', 0, lang::_('ID'))
				->_addField('shipping_address', 'taxtarea', 'text', '', lang::_('Shipping Address'))
                ->_addField('billing_address', 'taxtarea', 'text', '', lang::_('Billing Address'))
                ->_addField('shipping_module', 'taxtarea', 'text', '', lang::_('Shipping Module'))
                ->_addField('payment_module', 'taxtarea', 'text', '', lang::_('Payment Module'))
                ->_addField('taxes', 'taxtarea', 'text', 0, lang::_('Taxes'))
                ->_addField('user_id', 'text', 'int', 0, lang::_('User ID'))
                ->_addField('sub_total', 'text', 'float', 0, lang::_('SubTotal'))
                ->_addField('tax_rate', 'text', 'float', 0, lang::_('Tax Rate'))
                ->_addField('total', 'text', 'float', 0, lang::_('Total'))
                ->_addField('status', 'selectbox', 'enum', 'created', lang::_('Status'))
                ->_addField('currency', 'text', 'text', '', lang::_('Currency'))
                ->_addField('comments', 'textarea', 'text', '', lang::_('Comments'))
				->_addField('date_created', 'text', 'int', 0, lang::_('Date Created'));
        $this->_fields['status']->addHtmlParam('options', array(
            'created' => 'created',
            'pending' => 'pending',
            'paid' => 'paid',
            'confirmed' => 'confirmed',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled'));
    }
}
?>