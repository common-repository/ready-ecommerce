<?php
class tableContent_img extends table {
    public function __construct() {
        $this->_table = '@__content_img';
        $this->_id = 'img_id';
        $this->_alias = 'toe_content_img';
        $this->_addField('img_id', 'text', 'int', '', lang::_('Image ID'))
                ->_addField('pid', 'text', 'int', '', lang::_('Product ID'));
    }
}
