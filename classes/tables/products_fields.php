<?php
class tableProducts_fields extends table {
    public function __construct() {
        $this->_table = '@__products_fields';
        $this->_id = 'id';
        $this->_alias = 'toe_p_fields';
        $this->_addField('id', 'hidden', 'int', 0, lang::_('field id'))
				->_addField('label', 'text', 'varchar', 0, lang::_('label'))
				->_addField('htmltype_id', 'text', 'int', 0, lang::_('htmltype_id'))
                ->_addField('default_value', 'text', 'text', 0, lang::_('default_value'))
				->_addField('mandatory', 'text', 'int', 0, lang::_('mandatory'))
				->_addField('active', 'text', 'int', 0, lang::_('active'))
				->_addField('sort_order', 'text', 'int', 0, lang::_('sort_order'))
				->_addField('original_id', 'text', 'int', 0, lang::_('original_id'));
    }
}
