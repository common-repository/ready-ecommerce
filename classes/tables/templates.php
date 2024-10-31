<?php
class tableTemplates extends table {
    public function __construct() {
        $this->_table = '@__templates';
        $this->_id = 'id';
        $this->_alias = 'toe_tpl';
        $this->_addField('id', 'text', 'int', '', lang::_('id'), 128)
				->_addField('label', 'text', 'varchar', '', lang::_('Label'), 128)
                ->_addField('code', 'text', 'varchar', '', lang::_('Code'), 64)
                ->_addField('use_as_default', 'checkbox', 'tinyint', 0, lang::_('Use as default'));
    }
}
?>
