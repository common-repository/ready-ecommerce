<?php
class tableExtrafields extends table {
    public function __construct() {
        $this->_table = '@__extrafields';
        $this->_id = 'id';
        $this->_alias = 'toe_uf';
        $this->_addField('id', 'text', 'int', '', lang::_('ID'))
				->_addField('label', 'text', 'text', '', lang::_('Label'), 128, '','',lang::_('The label of the field'))
                ->_addField('htmltype_id', 'selectbox', 'int', 0, lang::_('Html Type'), '', '', '',lang::_('What html element the field presents'))
                ->_addField('validate', 'selectbox', 'varchar', '', lang::_('Value Type'), 128, '', '', lang::_('What data type is going to be shown by field or validation if needed'))
                ->_addField('default_value', 'text', 'varchar', '', lang::_('Default Value'), 128, '', '', lang::_('The deafault value of the field'))
                ->_addField('params', 'textarea', 'text', '', lang::_('Params'), '', '', '', lang::_('Additional parameters, css selectors, options for list fields'))
                ->_addField('destination', 'checkboxlist', 'text', '', lang::_('Destination'), NULL, array(
                    'dbFrom' => 'userFieldDestFromDB', 
                    'dbTo' => 'userFieldDestToDB',
                    'html' => 'userFieldDestHtml'), '', lang::_('What forms or categories have this field'))
                ->_addField('mandatory', 'checkbox', 'tinyint', '', lang::_('Mandatory'), '', '', '', lang::_('Is this field required?'))
                ->_addField('code', 'hidden', 'varchar', '', lang::_('Code'), 32)                   
                ->_addField('parent', 'hidden', 'varchar','', '', 32)
                ->_addField('ordering', 'hidden', 'int', 0, lang::_('Order'))
                ->_addField('active', 'checkbox', 'tinyint', 1, lang::_('Active'))
                ->_addField('system', 'hidden', 'tinyint', 0, lang::_('System'));
    }
}
?>
