<?php
class tableVariations extends table {
    public function __construct() {
        $this->_table = '@__variations';
        $this->_id = 'product_id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_v';
        $this->_addField('variation_id', 'text', 'decimal', 0, lang::_('Product variation id'));
        $this->_addField('var_name', 'text', 'varchar', 0, lang::_('Variation name'), 128);
		$this->_addField('var_sort_order', 'text', 'int', 0, lang::_('Variation Sort Order'));
    }
}

