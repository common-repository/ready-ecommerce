<script>
 jQuery(document).ready(function(){
        jQuery('.toeExtraFieldsSelectList').change(function(){
            jQuery(this).parents('.product_extra:first').find('.toeEfValuesBox:first').show(TOE_DATA.animationSpeed);
            toeFillInEfVal( jQuery(this).parents('.product_extra:first'), jQuery(this).val() );
        });
        jQuery('.toeEfValuesBox input[name=attrs_price]').keyup(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            for(var i = 0; i < selectedOpts.length; i++) {
                jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][price]"]').val( jQuery(this).val() );
            }
            /*var forEfVals = jQuery(this).attr('for').split(',');
            
            alert(jQuery(this).val());*/
        });
        jQuery('.toeEfValuesBox input[name=sku]').keyup(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            for(var i = 0; i < selectedOpts.length; i++) {
                jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][sku]"]').val( jQuery(this).val() );
            }
            /*var forEfVals = jQuery(this).attr('for').split(',');
            
            alert(jQuery(this).val());*/
			//alert('yo');
        });
        jQuery('.toeEfValuesBox input[name=width]').keyup(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            for(var i = 0; i < selectedOpts.length; i++) {
                jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][width]"]').val( jQuery(this).val() );
            }
            /*var forEfVals = jQuery(this).attr('for').split(',');
            
            alert(jQuery(this).val());*/
			//alert('yo');
        });
        jQuery('.toeEfValuesBox input[name=weight]').keyup(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            for(var i = 0; i < selectedOpts.length; i++) {
                jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][weight]"]').val( jQuery(this).val() );
            }
            /*var forEfVals = jQuery(this).attr('for').split(',');
            
            alert(jQuery(this).val());*/
			//alert('yo');
        });
        jQuery('.toeEfValuesBox input[name=height]').keyup(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            for(var i = 0; i < selectedOpts.length; i++) {
                jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][height]"]').val( jQuery(this).val() );
            }
            /*var forEfVals = jQuery(this).attr('for').split(',');
            
            alert(jQuery(this).val());*/
			//alert('yo');
        });
        jQuery('.toeEfValuesBox input[name=length]').keyup(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            for(var i = 0; i < selectedOpts.length; i++) {
                jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][length]"]').val( jQuery(this).val() );
            }
            /*var forEfVals = jQuery(this).attr('for').split(',');
            
            alert(jQuery(this).val());*/
			//alert('yo');
        });
        jQuery('.toeEfValuesBox input[name=sort_order]').keyup(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            for(var i = 0; i < selectedOpts.length; i++) {
                jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ '][sort_order]"]').val( jQuery(this).val() );
            }
            /*var forEfVals = jQuery(this).attr('for').split(',');
            
            alert(jQuery(this).val());*/
			//alert('yo');
        });
        jQuery('.toeEfValuesBox input[name=price_absolute], .toeEfValuesBox input[name=disabled]').change(function(){
            var parentBox = jQuery(this).parents('.toeEfValuesBox:first');
            var selectedOpts = jQuery(parentBox).find('input[name=selectedOpts]').val().split(',');
            var checked = jQuery(this).attr('checked');
            var name = jQuery(this).attr('name');
            for(var i = 0; i < selectedOpts.length; i++) {
                if(checked)
                    jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ ']['+ name+ ']"]').val( 1 );
                else
                    jQuery(parentBox).find('input[name="exVal['+ selectedOpts[i]+ ']['+ name+ ']"]').val( 0 );
            }         
        });
    });
	
    </script>
<fieldset class="toeProdFieldset">
        <legend><?php lang::_e('Extra Product data')?></legend>
        <?php foreach($this->extraFieldsMultiple as $key => $f) {
               /*$field_cat = array_intersect($f->destination['categories'], $this->categories);
               if (empty($field_cat)) continue;*/
            ?>
             <div class="product_extra">
                <label for="<?php echo $f->name?>"><?php echo $f->label?> :</label>
                <div style="">
                    <?php lang::_e('Disable')?>: <?php ?>
                </div>
                <div class="product_field"><?php $f->display()?></div>
                <?php if($f->getHtml() == 'selectlist') {?>
                <br style="clear: both;" />
                <div class="toeEfValuesBox <?php echo $key;?>">
                    <table>
                        <tr><td><?php lang::_e('Price')?>:</td><td><?php echo html::text('attrs_price')?></td></tr>
                        <tr><td><?php lang::_e('Absolute')?>:</td><td><?php echo html::checkbox('price_absolute')?></td></tr>
                        <tr><td><?php lang::_e('Disable')?>:</td><td><?php echo html::checkbox('disabled')?></td></tr>
                        <tr><td><?php lang::_e('SKU')?>:</td><td><?php echo html::text('sku')?></td></tr>
                        <tr><td><?php lang::_e('Sort order')?>:</td><td><?php echo html::text('sort_order')?></td></tr>
                        <tr><td><?php lang::_e('Width')?>:</td><td><?php echo html::text('width')?></td></tr>
                        <tr><td><?php lang::_e('Length')?>:</td><td><?php echo html::text('length')?></td></tr>
                        <tr><td><?php lang::_e('Height')?>:</td><td><?php echo html::text('height')?></td></tr>
                        <tr><td><?php lang::_e('Weight')?>:</td><td><?php echo html::text('weight')?></td></tr>
                    </table>
                    <?php echo html::hidden('selectedOpts')?>
                    <?php foreach($f->getHtmlParam('options') as $id => $value) {
                        echo html::hidden('exValuesToFields['. $id. ']', array('value' => $f->getId()));
                        foreach($this->exFieldsEfVals[$id] as $efVal) {
                            $efVal->display();
                        }
                    }?>
                </div>
                <?php }?>
                <br clear="all" />
            </div>
        <?php }?>   
    </fieldset>
    <br clear="all" />