<?php
class tableProducts_fields_options extends table {
    public function __construct() {
        $this->_table = '@__products_fields_options';
        $this->_id = 'id';
        $this->_alias = 'toe_p_fields_options';
        $this->_addField('id', 'hidden', 'int', 0, lang::_('field opt id'))
				->_addField('products_field_id', 'hidden', 'int', 0, lang::_('products_field_id'))
				->_addField('label', 'text', 'varchar', 0, lang::_('label'))
                ->_addField('price', 'text', 'decimal', 0, lang::_('price'))
				->_addField('absolute', 'text', 'int', 0, lang::_('absolute'))
				->_addField('sort_order', 'text', 'int', 0, lang::_('sort_order'));
    }
}
