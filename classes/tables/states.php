<?php
class tableStates extends table {
    public function __construct() {
        $this->_table = '@__states';
        $this->_id = 'id';
        $this->_alias = 'toe_states';
        $this->_addField('id', 'text', 'int', 0, lang::_('State ID'))
            ->_addField('name', 'text', 'varchar', '', lang::_('Name'), 128)
            ->_addField('code', 'text', 'varchar', 0, lang::_('Code'), 6)
            ->_addField('country_id', 'text', 'int', 0, lang::_('Country ID'));
    }
}
?>
