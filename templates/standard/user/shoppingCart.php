<?php if($this->canEdit) {?>
<script type="text/javascript">
// <!--
var toeQtyPrev = '';
jQuery(document).ready(function(){
    jQuery('.prod_qty').keyup(function(){
        var val = jQuery(this).val();
        if(val == '')
            val = 0;
        if(!isNumber(toeQtyPrev)) {
            var intMatches = /\d+/.exec(toeQtyPrev);
            if(intMatches)
                toeQtyPrev = intMatches[0];
        }
        if(!isNumber(val)) 
            val = toeQtyPrev;
        jQuery(this).val(val);
    });
    jQuery('.prod_qty').keydown(function(){
        if(jQuery(this).val() == '0')
            jQuery(this).val('');
        toeQtyPrev = jQuery(this).val();
    });
    jQuery('.cartQtyUpdate').submit(function(){
        jQuery(this).sendForm({
            msgElID: 'qty_update_msg_'+ jQuery(this).find('input[type=hidden][name=inCartId]').val(),
            onSuccess: function(res) {
                if(res.data)
                    updateCart( (res.data.update_cart ? res.data.update_cart : [res.data.cart]), res.data.totalHtml, res.data.newCartData );
            }
        });
        return false;
    });
    jQuery('.remove_from_cart').click(function(){
        var inCartId = jQuery(this).parents('tr:first').find('input[type=hidden][name=inCartId]').val();
        inCartId = parseInt(inCartId);
        if(isNumber(inCartId)) {
            jQuery(this).sendForm({
                msgElID: 'qty_update_msg_'+ inCartId,
                data: {inCartId: inCartId, reqType: 'ajax', action: 'updateCart', mod: 'user', qty: 0},
                onSuccess: function(res) {
                    if(res.data)
                        updateCart( (res.data.update_cart ? res.data.update_cart : [res.data.cart]), res.data.totalHtml, res.data.newCartData );
                }
            });
        }
        return false;
    });
});
// -->
</script>
<?php }?>
<?php if($this->canEdit) {?>
<div class="sp_todo">
    <a class="pink_btn" href="<?php echo frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml'))?>"><?php lang::_e('Check Out')?></a>
    <a href="<?php bloginfo('url'); ?>" class="arrow_grey_btn"><?php lang::_e('Continue shopping'); ?></a>
    <div class="clear"></div>
</div>

<div class="toe_coupons">
    <?php echo dispatcher::applyFilters('afterCartPrint', ''); ?>
</div>
<?php }?>
<table class="shopping_cart">
    <thead>
	<tr>
        <?php foreach($this->columns as $cKey => $cInfo) { ?>
            <?php if($cInfo['disable']) continue;?>
            <?php  if(!($cKey == 'action' && !$this->canEdit)):?>
            <td class="shopping_cart_<?php echo $cKey?>"><?php lang::_e($cInfo['title'])?></td>
            <?php endif; ?>
        <?php }?>
    </tr>
    </thead>
<?php foreach($this->cart as $inCartId => $c) { ?>
<tbody>
    <tr class="cart_row_<?php echo $inCartId?>">
        <?php foreach($this->columns as $cKey => $cInfo) {
            if($cInfo['disable']) continue;
            switch($cKey) {
                case 'id' :?>
                    <td class="sc_id"><?php echo $c['pid']?></td>
                <?php break;
                case'img':?>
                    <td class="sc_img">
                        <?php
                            $imgsrc = frame::_()->getModule('products')->getView()->getProductImage($c['pid'], false, false, 'big', 'all', 'product-preview');
                        ?>
                        <div class="sc_img_wrapper">
                            <a href="<?php echo get_post_permalink($c['pid']);?>" target="_blank" title="<?php echo $c['name']?>">
                                <img src="<?php echo $imgsrc['big'][0];?>" alt="<?php echo $c['name']?>" />
                            </a>
                        </div>
                    </td>
                <?php break;
                case 'name': ?>
                    <td class="sc_name">
                        <a href="<?php echo get_post_permalink($c['pid']);?>"><?php echo $c['name']?></a><span class="toeProdOutOfStock">***</span>
                        <?php if(!empty($c['options'])) { ?>
                            <br /><br />
                            <div>
                            <?php foreach($c['options'] as $optKey => $opt) { ?>
                                <?php if(empty($opt['displayValue'])) continue;?>
                                <b><?php lang::_e($opt['label'])?></b>: 
                                <?php 
                                    if(is_array($opt['displayValue']))
                                        echo implode(', ', $opt['displayValue']);
                                    else
                                        echo $opt['displayValue']
                                ?><br />
                            <?php } ?>
                            </div>
                        <?php }?>
                    </td>
                <?php break;
                case 'qty':?>
                    <td class="sc_qty">
                        <?php if($this->canEdit && !$c['gift']) {?>
							<?php echo html::formStart('qty_cart', array('action' => '', 'attrs' => 'class="cartQtyUpdate"'))?>
							<?php echo html::hidden('inCartId', array('value' => $inCartId))?>
							<?php echo html::textIncDec('qty', array('value' => $c['qty'], 'attrs' => 'class="prod_qty"', 'id' => 'qty_'. $inCartId. ''))?>
							<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
							<?php echo html::hidden('action', array('value' => 'updateCart'))?>
							<?php echo html::hidden('mod', array('value' => 'user'))?>
							<?php /** @deprecated @see inCartId key, but DO NOT delete this*/?>
							<?php echo html::hidden('pid', array('value' => $c['pid']))?>
							<?php /*****/?>
							<?php echo html::submit('update', array('value' => lang::_('Update'), 'attrs' => 'class="update_qty"'))?>
							<div id="qty_update_msg_<?php echo $inCartId?>"></div>
							<?php echo html::formEnd()?>
                        <?php } else {
							if($this->canEdit && $c['gift']) {	//Show this for gifts
								echo html::formStart('qty_cart', array('action' => '', 'attrs' => 'class="cartQtyUpdate"'));
								echo html::hidden('inCartId', array('value' => $inCartId));
								echo html::formEnd();
							}
                            echo $c['qty'];
                        }?>
                        <div class="clear"></div>
                    </td>
                <?php break;
                case 'price': ?>
                    <td class="sc_price">
                        <?php if($c['gift']) {
                            lang::_e('It\'s a gift');
                        } else {
                            echo frame::_()->getModule('currency')->displayTotal($c['price'], 1 /*Price for one product*/, $c['pid'], array('options' => $c['options']));
                            //echo frame::_()->getModule('currency')->display($c['price']);
                        }?>
                    </td>
                <?php break;
                case 'total': ?>
                    <td class="total_<?php echo $inCartId?> sc_total">
                        <?php if($c['gift']) {
                            lang::_e('It\'s a gift');
                        } else {
                            echo frame::_()->getModule('currency')->displayTotal($c['price'], $c['qty'], $c['pid'], array('options' => $c['options']));
                        }?>
                        <?php /*if($this->canEdit) {?>
                        <a href="#" class="remove_from_cart"><?php echo html::img('delete-item.png')?></a>
                        <?php }*/?>
                    </td>
                <?php break;
                case 'action': ?>
                    <?php if($this->canEdit) {?>
                    <td>
						<?php if(!isset($this->columns['qty']) || $this->columns['qty']['disable']) { ?>
							<div id="qty_update_msg_<?php echo $inCartId?>"></div>
						<?php }?>
						<?php echo html::hidden('inCartId', array('value' => $inCartId)); ?>
						<a href="#" class="remove_from_cart"><?php echo html::img('cross.gif')?></a>
					</td>
                    <?php }?>
                <?php break;
            }
        }?>
    </tr>
	</tbody>
<?php }?>
</table>
<?php if($this->canEdit) {?>
    <div class="cart_footer">
        <div id="toeCartTotalBox"><?php echo $this->totalBox?><div class="toeClear"></div></div>
        <div class="toeClear"></div>
        
        <div class="toeFLeft">
            <input type="button" class="toeCartClearButton" value="<?php lang::_e('Clear Cart')?>" onclick="toeClearCart({reload: true}); return false;" />
            <div class="toeCartMsg"></div>
        </div>
        
        <div class="sp_todo">
            <a class="pink_btn" href="<?php echo frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml'))?>"><?php lang::_e('Check Out')?></a>
            <a href="<?php bloginfo('url'); ?>" onclick="history.go(-1); return false;" class="arrow_grey_btn"><?php lang::_e('Continue shopping'); ?></a>
        </div>
        <div class="toeClear"></div>
    </div>
<?php }?>