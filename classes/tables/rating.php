<?php
class tableRating extends table {
    public function __construct() {
        $this->_table = '@__rating';
        $this->_id = 'id';
        $this->_alias = 'toe_rate';
        $this->_addField('id', 'text', 'int', 0, lang::_('ID'))
                ->_addField('pid', 'text', 'int', 0, lang::_('Product ID'))
                ->_addField('ip', 'text', 'varchar', 0, lang::_('IP Address'))
                ->_addField('rate', 'text', 'tinyint', 0, lang::_('Rate'))
                ->_addField('comment_id', 'text', 'int', 0, lang::_('Comment ID'))
                ->_addField('approved', 'text', 'int', 0, lang::_('Approved'));
    }
}
?>
