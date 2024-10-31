<?php
class tableUser_files extends table {
    public function __construct() {
        $this->_table = '@__user_files';
        $this->_id = 'id';
        $this->_alias = 'toe_uf';
        $this->_addField('id', 'hidden', 'int', '', lang::_('ID'))
				->_addField('uid', 'hidden', 'int', '', lang::_('User ID'))
                ->_addField('fid', 'hidden', 'int', '', lang::_('File ID'))
                ->_addField('order_id', 'text', 'int', '', lang::_('Order ID'))
                ->_addField('downloads', 'text', 'int', '', lang::_('Number of Downloads'))
                ->_addField('expires', 'text', 'datetime', '', lang::_('Expiration Date'))
                ->_addField('token', 'hidden', 'text', '', lang::_('Token'));
    }
}
?>
