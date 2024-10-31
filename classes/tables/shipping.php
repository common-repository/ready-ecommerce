<?php
class tableShipping extends table {
    public function __construct() {
        $this->_table = '@__shipping';
        $this->_id = 'id';
        $this->_alias = 'toe_rate';
        $this->_addField('id', 'hidden', 'int', 0, lang::_('ID'))
                ->_addField('label', 'text', 'varchar', '', lang::_('Label'), 255)
                ->_addField('description', 'textarea', 'text', '', lang::_('Description'))
                ->_addField('code', 'selectbox', 'varchar', '', lang::_('Shipping Method'), 128)
                ->_addField('params', 'block', 'text', '', lang::_('Params'))
                ->_addField('active', 'hidden', 'tinyint', 0, lang::_('Active'));
    }
}
?>
