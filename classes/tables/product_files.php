<?php
class tableProduct_files extends table {
    public function __construct() {
        $this->_table = '@__product_files';
        $this->_id = 'id';
        $this->_alias = 'toe_f';
        $this->_addField('id', 'hidden', 'int', '', lang::_('ID'))
				->_addField('pid', 'hidden', 'int', '', lang::_('Product ID'))
                ->_addField('name', 'text', 'varchar', '255', lang::_('File name'))
                ->_addField('path', 'hidden', 'text', '', lang::_('Real Path To File'))
                ->_addField('mime_type', 'text', 'varchar', '32', lang::_('Mime Type'))
                ->_addField('size', 'text', 'int', 0, lang::_('File Size'))
                ->_addField('active', 'checkbox', 'tinyint', 0, lang::_('Active Download'))
                ->_addField('date','text','datetime','',lang::_('Upload Date'))
                ->_addField('download_limit','text','int','',lang::_('Download Limit'))
                ->_addField('period_limit','text','int','',lang::_('Period Limit'))
                ->_addField('description', 'textarea', 'text', 0, lang::_('Descritpion'))
                ->_addField('type_id','text','int','',lang::_('Type ID'));
    }
}
?>
