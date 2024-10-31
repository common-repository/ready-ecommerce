<?php
class tableTaxes extends table {
    public function __construct() {
        $this->_table = '@__taxes';
        $this->_id = 'id';
        $this->_alias = 'toe_tax';
        $this->_addField('id', 'text', 'int', '', lang::_('Tax ID'))
				->_addField('label', 'text', 'varchar', '', lang::_('Tax Name'), 128, '', '', lang::_('Tax name'))
                ->_addField('type', 'selectbox', 'enum', '', lang::_('Apply To'), '','','', lang::_('You can apply tax to country(ies), category(ies) or to the total sum of the order'))
                ->_addField('data', 'textarea', 'text', '', lang::_('Details'), 0, array('dbFrom' => 'userFieldDestFromDB', 'dbTo' => 'userFieldDestToDB'), '', lang::_('Set countries, categories and the tax rate'));
        
        $this->_fields['type']->addHtmlParam('options', array(
            'total' => 'total',
            'address' => 'address',
            'category' => 'category'
        ));
    }
}
?>
