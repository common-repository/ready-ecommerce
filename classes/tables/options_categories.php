<?php
class tableOptions_categories extends table {
    public function __construct() {
        $this->_table = '@__options_categories';
        $this->_id = 'id';     
        $this->_alias = 'toe_opt_cats';
        $this->_addField('id', 'hidden', 'int', 0, lang::_('ID'))
            ->_addField('label', 'text', 'varchar', 0, lang::_('Method'), 128);
    }
}
?>
