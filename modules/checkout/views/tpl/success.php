<script type="text/javascript">
// <!--
    if(typeof(toeSetNavigationSelected) != 'undefined') {
        toeSetNavigationSelected('success');
        toeSetNavigationPassed(['cart', 'checkout', 'confirnation']);
    }
// -->
</script>
<h2>
<?php 
    if(empty($this->checkout_success_text)) {
        lang::_e('Order Created. Thank You for buying in Our online Store.');
    } else {
        lang::_e(nl2br($this->checkout_success_text));
    }
?>
</h2>
<?php if(!empty($this->paymentSuccessHtml)) echo $this->paymentSuccessHtml?>
