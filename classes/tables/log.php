<?php
class tableLog extends table {
    public function __construct() {
        $this->_table = '@__log';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_log';
        $this->_addField('id', 'text', 'int', 0, lang::_('ID'), 11)
                ->_addField('type', 'text', 'varchar', '', lang::_('Type'), 64)
                ->_addField('data', 'text', 'text', '', lang::_('Data'))
                ->_addField('date_created', 'text', 'int', '', lang::_('Date created'))
				->_addField('uid', 'text', 'int', 0, lang::_('User ID'))
				->_addField('oid', 'text', 'int', 0, lang::_('Order ID'));
    }
}