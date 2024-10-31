<?php
class tableCart extends table {
    public function __construct() {
        $this->_table = '@__cart';
        $this->_id = 'user_id';
        $this->_alias = 'toe_cart';
        $this->_addField('user_id', 'text', 'int', 0, lang::_('User ID'))
            ->_addField('content', 'text', 'text', '', lang::_('Content'), 128)
            ->_addField('updated', 'text', 'int', 0, lang::_('Content'));
    }
}