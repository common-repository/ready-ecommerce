<?php echo html::formStart('singleProdWidg'. $this->post->ID, array('attrs' => 'onsubmit="toeAddToCart(this); return false;"', 'method' => 'POST')) ?>
	<?php if($this->instance['show_qty']) {?>
		<?php echo html::text('qty', array('value' => 1))?>
	<?php } else {?>
		<?php echo html::hidden('qty', array('value' => 1))?>
	<?php }?>
	<?php echo html::hidden('addQty', array('value' => 1))?>
	<?php echo html::hidden('mod', array('value' => 'user'))?>
	<?php echo html::hidden('action', array('value' => 'addToCart'))?>
	<?php echo html::hidden('pid', array('value' => $this->post->ID))?>
	<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo html::submit('add', array('value' => lang::_('Add to Cart')))?>
	<div class="toeAddToCartMsg"></div>
<?php echo html::formEnd()?>
