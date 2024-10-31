<?php
class tableModules extends table {
    public function __construct() {
        $this->_table = '@__modules';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_m';
        $this->_addField('id', 'text', 'int', 0, lang::_('ID'))
				->_addField('label', 'text', 'varchar', 0, lang::_('Label'), 128)
                ->_addField('type_id', 'selectbox', 'smallint', 0, lang::_('Type'))
                ->_addField('active', 'checkbox', 'tinyint', 0, lang::_('Active'))
                ->_addField('params', 'textarea', 'text', 0, lang::_('Params'))
                ->_addField('has_tab', 'checkbox', 'tinyint', 0, lang::_('Has Tab'))
                ->_addField('description', 'textarea', 'text', 0, lang::_('Description'), 128)
                ->_addField('code', 'hidden', 'varchar', '', lang::_('Code'), 64)
                ->_addField('ex_plug_dir', 'hidden', 'varchar', '', lang::_('External plugin directory'), 255);
    }
}
?>
