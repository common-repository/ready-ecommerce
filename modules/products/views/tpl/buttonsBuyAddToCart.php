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
        <div class="toeAddToCartMsg"></div>
        <?php if(!empty($this->extraFields)) {?>
        <div>
            <table>
                <?php foreach($this->extraFields as $d) { ?>
                <tr>
                    <td><?php lang::_e($d->label)?></td>
                    <td><?php $d->display()?></td>
                </tr>
                <?php }?>
            </table>
        </div>
        <?php }?>
        <?php echo html::text('qty', array('value' => 1))?>
        <?php echo html::hidden('goto')?>
        <?php echo html::hidden('addQty', array('value' => 1))?>
        <?php echo html::hidden('mod', array('value' => 'user'))?>
        <?php echo html::hidden('action', array('value' => 'addToCart'))?>
        <?php echo html::hidden('pid', array('value' => $this->post->ID))?>
        <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
        <?php echo html::submit('add', array('value' => lang::_('Add to Cart')))?>
        <?php echo html::submit('buynow', array('value' => lang::_('Buy Now')))?>
    <?php if($this->useFormOnButtonsTpl) { ?>
        </form>
    <?php }?>
    <?php if($this->stockCheck && !$this->availableQty) { ?>
        <div class="toeErrorMsg"><?php lang::_e('Out of stock')?></div>
    <?php }?>
</div>