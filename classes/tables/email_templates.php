<?php
class tableEmail_templates extends table {
    public function __construct() {
        $this->_table = '@__email_templates';
        $this->_id = 'id';
        $this->_alias = 'toe_etpl';
        $this->_addField('id', 'text', 'int', '', lang::_('ID'))
				->_addField('label', 'text', 'varchar', '', lang::_('Label'), 128, '','',lang::_('Template label'))
               ->_addField('subject', 'textarea', 'varchar','', lang::_('Subject'),255,'','',lang::_('E-mail Subject'))
               ->_addField('body', 'textarea', 'text','', lang::_('Body'),'','','',lang::_('E-mail Body'))
               ->_addField('variables', 'block', 'text','', lang::_('Variables'),'','','',lang::_('Template variables. They can be used in the body and subject'))
               ->_addField('active', 'checkbox', 'tinyint',0, lang::_('Active'),'','','',lang::_('If checked the notifications will be sent to receiver'))
               ->_addField('name', 'hidden', 'varchar','','',128)
               ->_addField('module', 'hidden', 'varchar','','', 128);
    }
}
?>
