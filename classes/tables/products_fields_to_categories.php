<?php
class tableProducts_fields_to_categories extends table {
    public function __construct() {
        $this->_table = '@__products_fields_to_categories';
        $this->_id = 'products_field_id';
        $this->_alias = 'toe_p_fields_to_categories';
        $this->_addField('products_field_id', 'hidden', 'int', 0, lang::_('products_field_id'))
				->_addField('category_id', 'hidden', 'int', 0, lang::_('category_id'));
    }
}
