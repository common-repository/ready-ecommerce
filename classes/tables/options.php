<?php
class tableOptions extends table {
     public function __construct() {
        $this->_table = '@__options';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_opt';
        $this->_addField('id', 'text', 'int', 0, lang::_('ID'))->
                _addField('code', 'text', 'varchar', '', lang::_('Code'), 64)->
                _addField('value', 'text', 'varchar', '', lang::_('Value'), 4194304)->
                _addField('label', 'text', 'varchar', '', lang::_('Label'), 255)->
                _addField('description', 'text', 'text', '', lang::_('Description'))->
                _addField('htmltype_id', 'selectbox', 'text', '', lang::_('Type'))->
				_addField('cat_id', 'hidden', 'int', '', lang::_('Category ID'))->
				_addField('sort_order', 'hidden', 'int', '', lang::_('Sort Order'));
    }
}
?>
