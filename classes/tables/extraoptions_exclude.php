<?php
class tableExtraoptions_exclude extends table {
    public function __construct() {
        $this->_table = '@__ef_options_exclude';
        $this->_id = 'id';
        $this->_alias = 'toe_ef_o_ex';
        $this->_addField('id', 'text', 'int', '', lang::_('Id'))
				->_addField('oid', 'text', 'int', '', lang::_('Option Id'))
                ->_addField('pid', 'text', 'int','', lang::_('Product (post) Id'));
    }
}
?>
