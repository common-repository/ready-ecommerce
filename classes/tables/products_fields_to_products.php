<?php
class tableProducts_fields_to_products extends table {
    public function __construct() {
        $this->_table = '@__products_fields_to_products';
        $this->_id = 'products_field_id';
        $this->_alias = 'toe_p_fields_to_products';
        $this->_addField('products_field_id', 'hidden', 'int', 0, lang::_('products_field_id'))
				->_addField('product_id', 'hidden', 'int', 0, lang::_('product_id'));
    }
}