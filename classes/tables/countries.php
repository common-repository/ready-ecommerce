<?php
class tableCountries extends table {
    public function __construct() {
        $this->_table = '@__countries';
        $this->_id = 'id';
        $this->_alias = 'toe_cry';
        $this->_addField('id', 'text', 'int', '', lang::_('id'))
				->_addField('name', 'text', 'varchar', '', lang::_('Name'), 128)
                ->_addField('iso_code_2', 'text', 'varchar', '', lang::_('ISO2 code'), 2)
                ->_addField('iso_code_3', 'text', 'varchar', '', lang::_('ISO3 code'), 3);
    }
}