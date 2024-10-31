<div class="popup_content"><?php lang::_e('The item has been added to your')?> <a href="<?php echo frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getShoppingCart'))?>"><?php lang::_e('shopping cart!'); ?></a></div>
<div class="popup_control_btns">
    <a href="#" onclick="subScreen.clearAndHide(); return false;" class="grey_btn"><?php lang::_e('Continue')?></a>
    <a href="<?php echo frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml'))?>" class="pink_btn"><?php lang::_e('Checkout')?></a>
</div>
