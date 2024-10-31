<div class="clear"></div>
<script type="text/javascript">
// <!--
function toeApplyCoupon() {
    jQuery(this).sendForm({
        msgElID: 'toeCouponsCheckoutMsg',
        data: {coupon: jQuery('#toeCoupon').val(), mod: 'coupons', action: 'applyCoupon', reqType: 'ajax'},
        onSuccess: function(res) {
            if(res.data.totalHtml) {
                updateCart(new Array(), res.data.totalHtml);
            }
            if(!res.error) {
                jQuery('#toeCoupon').val('');
            }
        }
    });
}
function toeShouCouponsDescription(link) {
    var linkPos = jQuery(link).position();
    subScreen.show(<?php echo $this->couponsDescription?>, linkPos.left, linkPos.top);
}
// -->
</script>
<span><?php lang::_e('Enter Coupon Code (only one coupon per order, more than one coupon will void your order):')?></span>
<?php echo html::text('coupon', array('attrs' => 'id="toeCoupon"'))?>
<?php echo html::button(array('value' => lang::_('OK'), 'attrs' => 'onclick="toeApplyCoupon(); return false;" class="grey_btn"'))?>
<div id="toeCouponsCheckoutMsg"></div>
<!-- <a href="#" onclick="toeShouCouponsDescription(this); return false;"><?php lang::_e('Where I can get It?')?></a> -->