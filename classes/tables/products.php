<?php
class tableProducts extends table {
    public function __construct() {
        $this->_table = '@__products';
        $this->_id = 'post_id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_p';
        $this->_addField('post_id', 'hidden', 'int', 0, lang::_('post id'))
				->_addField('price', 'text', 'decimal', 0, lang::_('Price'), 10, array('dbTo' => 'floatToDB'))
                ->_addField('weight', 'text', 'float', 0, lang::_('Weight'), 7, array('dbTo' => 'floatToDB'))
                ->_addField('sku', 'text', 'varchar', 0, lang::_('SKU'), 64)
                ->_addField('quantity', 'text', 'int', 0, lang::_('Quantity'), 11, array('dbTo' => 'intToDB'))
                
				->_addField('featured', 'checkbox', 'tinyint', 0, lang::_('Featured'))
				->_addField('mark_as_new', 'checkbox', 'tinyint', 0, lang::_('Mark as New'))
                
                ->_addField('width', 'text', 'decimal', 0, lang::_('Width'))
                ->_addField('height', 'text', 'decimal', 0, lang::_('Height'))
                ->_addField('length', 'text', 'decimal', 0, lang::_('Length'))
			
				->_addField('cost', 'hidden', 'decimal', 0, lang::_('Cost'), 11, array('dbTo' => 'intToDB'))
                ->_addField('views', 'text', 'free', 0, lang::_('Number of Views'), 11)
					
				->_addField('sort_order', 'text', 'int', 0, lang::_('Sort Order'));
    }
}
?>
