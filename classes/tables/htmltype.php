<?php
class tableHtmltype extends table {
    public function __construct() {
        $this->_table = '@__htmltype';
        $this->_id = 'id';     
        $this->_alias = 'toe_htmlt';
        $this->_addField('id', 'hidden', 'int', 0, lang::_('ID'))
            ->_addField('label', 'text', 'varchar', 0, lang::_('Method'), 32)
            ->_addField('description', 'text', 'varchar', 0, lang::_('Description'), 255);
    }
}
?>
