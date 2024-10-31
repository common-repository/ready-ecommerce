<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#toeAddToCartForm<?php echo $this->post->ID?>').find('input[name=buynow]').click(function(){
        jQuery('#toeAddToCartForm<?php echo $this->post->ID?>').find('input[name=goto]').val('checkout');
    });
});
</script>
<div class="actionButtons">
    <?php if($this->useFormOnButtonsTpl) { ?>
        <form action="" method="post" class="toeAddToCartForm" id="toeAddToCartForm<?php echo $this->post->ID?>" onsubmit="toeAddToCart(this); return false;">
    <?php }?>
        <?php $textColor = $this->viewOptions['add_to_cart_text']; ?>
		<div class="product_qty">
            <?php lang::_e('Qty:'); ?>
            <?php echo html::text('qty', array('value' => 1))?>
        </div>
        <?php echo html::hidden('goto')?>
		<?php echo html::hidden('addQty', array('value' => 1))?>
		<?php echo html::hidden('mod', array('value' => 'user'))?>
		<?php echo html::hidden('action', array('value' => 'addToCart'))?>
		<?php echo html::hidden('pid', array('value' => $this->post->ID))?>
		<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo html::hidden('gotoOnError', array('value' => 'product'))?>
		
		<?php echo html::submit('add', array('value' => lang::_('Add to Cart'), 'attrs' => 'class="pink_btn"'))?>
		<div class="toeAddToCartMsg"></div>
    <?php if($this->useFormOnButtonsTpl) { ?>
        </form>
    <?php }?>
    <?php if($this->stockCheck && !$this->availableQty) { ?>
        <div class="toeErrorMsg"><?php lang::_e('Out of stock')?></div>
    <?php }?>
</div>