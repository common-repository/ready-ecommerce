<div class="toeWidget">
    <div class="toeWidgetTitle">
        <p>
            <a href="<?php echo frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getShoppingCart'))?>">
                <?php lang::_e('Cart')?>
            </a>
        </p>
        <span class="cart_items">
            <?php  
            $count_items = 0;
    
            foreach($this->cart as $inCartId => $c) { 
                $count_items += $c['qty'];
            }
            
            if ($count_items > 1) {
                $txt = 'items';
            } else {$txt = 'item';}
            ?>
            
            <?php echo $count_items.' '.lang::_("$txt").' - '.frame::_()->getModule('currency')->display( $this->total ); ?> 
        </span>
    </div>
    <div class="shopping_cart_list">
        <div class="blue_button"><?php echo $this->checkoutLink; ?></div>
        <br clear="all" />
    </div>
    <div>
        <input type="button" value="<?php lang::_e('Clear Cart')?>" onclick="toeClearCart({reload: false}); return false;" />
        <div class="toeCartMsg"></div>
    </div>
</div>