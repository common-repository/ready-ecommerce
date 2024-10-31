<?php
class tableOrders_items extends table {
    public function __construct() {
    $this->_table = '@__orders_items';
        $this->_id = 'id';
        $this->_alias = 'toe_o_i';
		$this->_escape = true;
        $this->_addField('id', 'text', 'int', 0, lang::_('ID'))
				->_addField('order_id', 'text', 'int', 0, lang::_('Order ID'))
                ->_addField('order_id', 'text', 'int', 0, lang::_('Order ID'))
                ->_addField('product_id', 'text', 'int', 0, lang::_('Product ID'))
                ->_addField('product_name', 'text', 'varchar', '', lang::_('Product Name'), 128)
                ->_addField('product_sku', 'text', 'varchar', '', lang::_('Product SKU'), 64)
                ->_addField('product_price', 'text', 'float', 0, lang::_('Product Price'))
                ->_addField('product_qty', 'text', 'int', 0, lang::_('Product Quantity'))
                ->_addField('product_params', 'text', 'text', 0, lang::_('Product Params'));
    }
}
?>
