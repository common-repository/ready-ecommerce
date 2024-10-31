<?php
class tableSubscribers extends table {
    public function __construct() {
        $this->_table = '@__subscribers';
        $this->_id = 'id';
        $this->_alias = 'toe_subscr';
        $this->_addField('id', 'text', 'int', '', lang::_('Id'), 11)
			->_addField('user_id', 'text', 'int', '', lang::_('User Id'), 11, '', '', lang::_('User Id'))
            ->_addField('email', 'text', 'varchar', '', lang::_('User E-mail'), 255, '', '', lang::_('Subscriber E-mail'))
            ->_addField('name', 'text', 'varchar', 0, lang::_('User Name'),255,'','', lang::_('User Name If User Is Registered'))
            ->_addField('created', 'text', 'datetime', '', lang::_('Subscription Date'), '', '','', lang::_('Date Of Subscription'))
            ->_addField('active', 'checkbox', 'tinyint', '', lang::_('Active Subscription'), 4, '','', lang::_('If Is Not Checked user will not get any newsletters'))
            ->_addField('token', 'hidden', 'varchar', '', lang::_('Token'), 255,'','','');
    }
}
?>