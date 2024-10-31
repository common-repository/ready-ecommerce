<?php
class tableCurrency extends table {
    public function __construct() {
        $this->_table = '@__currency';
        $this->_id = 'id';
        $this->_alias = 'toe_curr';
        $this->_addField('id', 'text', 'int', '', lang::_('id'))
			->_addField('code', 'text', 'varchar', '', lang::_('Code'), 32, '', '', lang::_('Currency identifier'))
            ->_addField('label', 'text', 'varchar', '', lang::_('Label'), 128, '', '', lang::_('Currency name'))
            ->_addField('value', 'text', 'float', 0, lang::_('Value'),'','','', lang::_('Currency rate in comparison to default currency'))
            ->_addField('symbol', 'text', 'varchar', '', lang::_('Symbol'), 16, '','', lang::_('Currency Symbol'))
            
            ->_addField('symbol_left', 'hidden', 'varchar', '', lang::_('Symbol Left'), 32, '','', lang::_('Symbol left to price'))
            ->_addField('symbol_right', 'hidden', 'varchar', '', lang::_('Symbol Right'), 32,'','',lang::_('Symbol right to price'))
            ->_addField('symbol_point', 'hidden', 'varchar', '', lang::_('Symbol Point'), 2, '', '', lang::_('Decimal point'))
            ->_addField('symbol_thousand', 'hidden', 'varchar', '', lang::_('Symbol Thousand'), 2, '', '', lang::_('Thousand symbol'))
            ->_addField('decimal_places', 'hidden', 'int', 2, lang::_('Decimal Places'),'','','',lang::_('The number of digits after the point'))
            
            ->_addField('use_as_default', 'checkbox', 'bool', 0, lang::_('Default'),'','','', lang::_('If checked the currency is the default currency and all prices should be set in current currency'))
            ->_addField('currency_view', 'selectbox', 'varchar', '', lang::_('Currency View'), 32,'','', lang::_(''))
            ->_addField('price_view', 'selectbox', 'varchar', '', lang::_('Price View'),32,'','', lang::_(''));
    }
}
?>
