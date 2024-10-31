<?php
class tableModules_type extends table {
    public function __construct() {
        $this->_table = '@__modules_type';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_m_t';
        $this->_addField($this->_id, 'text', 'int', '', lang::_('ID'))->
                _addField('label', 'text', 'varchar', '', lang::_('Label'), 128);
    }
}
?>
