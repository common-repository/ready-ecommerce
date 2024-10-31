<?php
class tableImg_status extends table {
    public function __construct() {
        $this->_table = '@__img_status';
        $this->_id = 'post_id';
        $this->_alias = 'toe_img_status';
        $this->_addField('id', 'text', 'int', 0, lang::_('ID'))
            ->_addField('parent_id', 'text', 'int', 0, lang::_('Product ID'))
            ->_addField('post_id', 'text', 'int', 0, lang::_('Img ID'))
            ->_addField('status', 'text', 'enum', 0, lang::_('Status'));
    }
}
?>
