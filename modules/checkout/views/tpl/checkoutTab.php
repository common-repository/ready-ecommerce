<h1><?php lang::_e('Checkout Options')?></h1>
<script type="text/javascript">
// <!--
    toePostboxes['checkout'] = jQuery.extend(true, {}, toePostbox);
    toePostboxes['checkout'].save_order = function(page) {
        detectCheckoutSortOrder('toeCheckoutPostbox', 'checkoutSortOrder');
    }
    toePostboxes['cartCols'] = jQuery.extend(true, {}, toePostbox);
    toePostboxes['cartCols'].classContainer = '.meta-box-sortables-cart';
    toePostboxes['cartCols'].save_order = function(page) {
        detectCheckoutSortOrder('toeCartPostbox', 'cartSortOrder');
    }
    jQuery(document).ready(function(){
        toePostboxes['checkout'].add_postbox_toggles(pagenow);
        toePostboxes['cartCols'].add_postbox_toggles(pagenow);
        jQuery('#toeCheckoutOptionsForm').submit(function(){
		
			var checkoutSortOrder = jQuery('#toeCheckoutOptionsForm').find('input[name=checkoutSortOrder]').val();
			var arr = checkoutSortOrder.split(',');
			var tittles = Array();
			for(var i=0;i<arr.length;i++){
			tittles.push(jQuery("#toeCheckoutStep-"+arr[i]+" .title").text());
			}
			jQuery('#toeCheckoutOptionsForm').find('input[name=checkoutTittles]').val(tittles.join(","));
            jQuery(this).sendForm({
                msgElID: 'toeCheckoutOptionsMsg'
            });
            return false;
        });
        detectCheckoutSortOrder('toeCheckoutPostbox', 'checkoutSortOrder');
        detectCheckoutSortOrder('toeCartPostbox', 'cartSortOrder');
		
		jQuery(".hndle.title").each(function (i) { jQuery(this).dblclick(divClicked);});
    });
	function divClicked() {
		var divHtml = jQuery(this).html();
		jQuery(this).unbind("dblclick");
		var editableText = jQuery("<textarea />");
		editableText.val(divHtml);
		jQuery(this).html(editableText);
		editableText.focus();
		// setup the blur event for this new textarea
		editableText.blur(editableTextBlurred);
	}

	function editableTextBlurred() {
		var html = jQuery(this).val();
		var viewableText = jQuery("<div>");
		viewableText.html(html);
		jQuery(this).replaceWith(viewableText);
		// setup the click event for this new div
		viewableText.dblclick(divClicked);
	}

	
    function detectCheckoutSortOrder(postboxClass, inputName) {
		toeDetectSortOrder(postboxClass, jQuery('#toeCheckoutOptionsForm').find('input[name='+ inputName+ ']'));
    }
// -->
</script>
<style type="text/css">
    .meta-box-sortables-cart div {
        float: left;
    }
    .meta-box-sortables-cart .postbox {
        min-width: 40px !important;
    }
</style>
<form id="toeCheckoutOptionsForm" action="">
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" class="postbox-container" style="width:80%;">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                <?php foreach($this->steps as $sKey => $sDesc) {?>
                    <div id="toeCheckoutStep-<?php echo $sKey?>" class="postbox toeCheckoutPostbox" style="display: block">
                        <div class="handlediv" title="Click to toggle"><br></div>
<h3 class="hndle title"><?php lang::_e(isset($sDesc['title']) ? $sDesc['title'] : '')?></h3>                        <div class="inside">
                            <!--Entire block content start-->
                            <?php echo html::checkbox('checkoutStepsDisable['. $sKey. ']', array('checked' => $sDesc['disable']))?> <?php lang::_e('Disable')?>
                            <?php if($sKey == 'summaryBlock') { ?>
                                <div><?php lang::_e('Shopping Cart product fields setup')?>: </div>
                                <div class="metabox-holder" style="width: 100%;">
                                    <div class="postbox-container">
                                        <div class="meta-box-sortables-cart ui-sortable">
                                            <?php foreach($this->cartColumns as $cKey => $cInfo) { ?>
                                                <div id="toeCartCol-<?php echo $cKey?>" class="postbox toeCartPostbox" style="display: block; width: 100px;">
                                                    <div class="handlediv" title="Click to toggle"><br></div>
                                                        <h3 class="hndle"><?php lang::_e($cInfo['title'])?></h3>
                                                        <div class="inside"><?php echo html::checkbox('cartPartsDisable['. $cKey. ']', array('checked' => $cInfo['disable']))?> <?php lang::_e('Disable')?></div>
                                                </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            <div style="clear: both;"></div>
                            <?php }?>
                            <!--Entire block content end-->
							<!--Terms block content start-->
                            <?php if($sKey == 'terms') { ?>
                                <div class="metabox-holder" style="width: 100%;">
                                    <div class="postbox-container">
									<table>
									<tr>
									<td>URL:</td>
									<td><?php echo html::text('termsURL', array('value' => $this->termsURL, 'attrs' => "size='60'"));?></td>
									</tr>
									<tr>
									<td>Text:</td>
									<td><?php echo html::text('termsText', array('value' => $this->termsText, 'attrs' => "size='60'"));?></td>
									</tr>
                                            
									</table>
                                    </div>
                                </div>
                            <div style="clear: both;"></div>
                            <?php }?>
                            <!--Terms block content end-->
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>
    <div><?php lang::_e('Checkout Success Step Text')?>: </div>
    <div><?php $this->checkoutSuccessText->display()?></div>
    <div><?php lang::_e('Supported Variables')?>: ["<?php echo implode('", "', $this->checkoutSuccessVariables)?>"]</div>
    <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
    <?php echo html::hidden('page', array('value' => 'checkout'))?>
    <?php echo html::hidden('action', array('value' => 'saveStepsOption'))?>
    <?php echo html::hidden('checkoutSortOrder', array('value' => ''))  //This will be set from js, see code above?>
    <?php echo html::hidden('checkoutTittles', array('value' => ''))  //This will be set from js, see code above?>
    <?php echo html::hidden('cartSortOrder', array('value' => ''))      //This will be set from js, see code above?>
    <?php echo html::submit('save', array('value' => lang::_('Save'), 'attrs' => 'class="button button-primary"'))?>
    <div id="toeCheckoutOptionsMsg"></div>
</form>