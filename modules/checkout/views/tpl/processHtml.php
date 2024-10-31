<?php
    echo html::formStart('toeProcessOrder', array('method' => 'post', 'action' => '', 'attrs' => 'id="finishCheckoutForm"')). 
        html::hidden('action', array('value' => 'addSuccess')). 
        html::hidden('mod', array('value' => 'order')). 
        html::hidden('reqType', array('value' => 'ajax')). 
        html::submit('next', array('value' => lang::_('Finish'))).
        html::formEnd();
?>
<script type="text/javascript">
// <!--
jQuery(function() {
    jQuery('#finishCheckoutForm').submit(function(){
        jQuery(this).sendForm({
            onSuccess: function(res){
                if(res.html) {
                    jQuery('#toe_checkout_content').slideToggle('slow', function(){
                       jQuery(this).html(res.html);
                       jQuery(this).slideToggle('slow');
                    });
                }
            }
        });
        return false;
    });
});
// -->
</script>