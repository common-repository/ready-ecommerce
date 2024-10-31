<div>
    <?php lang::_e('Order By')?>: 
    <?php foreach($this->orderByValues as $k => $v) { ?>
    <a href="<?php uri::_e(array('baseUrl' => $this->baseHrefForOrdering, 'orderby' => $v['orderby'], 'order' => 'DESC'))?>"><?php lang::_e($v['label'])?></a>
    <?php foreach($v['desc'] as $order => $orderLabel) { ?>
        <a href="<?php uri::_e(array('baseUrl' => $this->baseHrefForOrdering, 'orderby' => $v['orderby'], 'order' => $order))?>"><?php lang::_e($orderLabel)?></a>
    <?php }?>
    <?php }?>
</div>
