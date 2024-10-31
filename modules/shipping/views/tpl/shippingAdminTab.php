<script type="text/javascript">
// <!--
var paramsConfigPatterns = <?php echo utils::jsonEncode($this->paramsConfigPatterns)?>;
var paramsConfigPatternsSelected = {};

jQuery(document).ready(function(){
    toeTables['toe_shipping_modules_list'] = new toeListTable('toe_shipping_modules_list', <?php echo utils::jsonEncode($this->allShippingModules)?>);
    toeTables['toe_shipping_modules_list'].draw();
    
    jQuery('#toeShippingModulesForm').submit(function(){
        jQuery(this).sendForm({
            msgElID: 'toeShippingMsg',
            onSuccess: function(res) {
                if(!res.error) {
                    if(!toeTables['toe_shipping_modules_list'].redrawRow('id', res.data[0].id, res.data[0])) {
                        toeTables['toe_shipping_modules_list'].draw(res.data);
                        toeClearShippingForm();
                    }
                    paramsConfigPatternsSelected[ res.data[0].code ] = res.data[0].params;
                    toeSetShippingModuleParams();
                }
            }
        });
        return false;
    });
    jQuery('#toeShippingModulesForm').find('select[name=code]').change(function(){
        toeSetShippingModuleParams();
    });
    toeSetShippingModuleParams();
    jQuery('#addShippingModule').click(function(){
        toeClearShippingForm();
    });
});
function toeRemoveShipping(link) {
    notOnRowClicked = true;
    var id = getIdFromTable(link);
    if(id) {
        jQuery(this).sendForm({
            data: {id: id, 
                action: 'deleteModule', 
                page: 'shipping', 
                reqType: 'ajax'},
            msgElID: 'toeShippingMsg',
            onSuccess: function(res) {
                if(!res.error) {
                    toeTables['toe_shipping_modules_list'].deleteRow('id', res.data.id);
                }
            }
        });
    }
}
function toeEditShipping(row) {
    if(notOnRowClicked) {
        notOnRowClicked = false;
        return;
    }
    var id = parseInt(jQuery(row).find('.id:first').html());
    if(!id) return;
    //And finaly - load data without new ajax request
    var module = toeTables['toe_shipping_modules_list'].getData('id', id);
    if(module) {
        toeClearShippingForm();
        var form = jQuery('#toeShippingModulesForm');
        jQuery(form).find('input[name=label]').val(module.label);
        jQuery(form).find('textarea[name=description]').val(module.description);
        jQuery(form).find('select[name=code]').val(module.code);
        jQuery(form).find('input[type=hidden][name=id]').val(module.id);
        jQuery(form).find('input[type=hidden][name=active]').val(module.active);
        paramsConfigPatternsSelected = {};
        jQuery.extend(paramsConfigPatternsSelected, paramsConfigPatterns);
        paramsConfigPatternsSelected[ module.code ] = module.params;
        toeSetShippingModuleParams();
    }
}
function toeClearShippingForm() {
    jQuery('#toeShippingModulesForm').clearForm();
    jQuery('#toeShippingModulesForm').find('input[type=hidden][name=id]').val('');
    jQuery('#toeShippingModulesForm').find('input[type=hidden][name=active]').val('1'); //Let it be active by default
    paramsConfigPatternsSelected = {};
	toeSetShippingModuleParams();
}
function toeSetShippingModuleParams() {
    var code = jQuery('#toeShippingModulesForm').find('select[name=code]').val();
    var htmlParamsBlock = '';
    if(paramsConfigPatternsSelected != null && paramsConfigPatternsSelected[code]) {
        htmlParamsBlock = paramsConfigPatternsSelected[code];
    } else if(code && (paramsConfigPatterns[code])) {
        htmlParamsBlock = paramsConfigPatterns[code];
    }
    jQuery('#toeShippingModulesForm').find('.toe_params:first').html(htmlParamsBlock);
    if(toeStatesObjects['params0state_to_ship']) {
        toeStatesObjects['params0state_to_ship'].init( jQuery('#toeShippingModulesForm').find('select[type=country]:first') );
    } else {
        toeStatesObjects['params0state_to_ship'] = new toeStatesSelect('params0state_to_ship', {linkToElement: jQuery('#toeShippingModulesForm').find('select[type=country]:first')});
    }
}
function toeSwitchShippingModuleStatus(link) {
    notOnRowClicked = true;
    if(jQuery(link).parents('tr').find('td.type').html() == 'system') return;
    var id = getIdFromTable(link);
    if(id) {
        jQuery(this).sendForm({
            data: {id: id, action: 'storeModule', page: 'shipping', reqType: 'ajax', active: jQuery(link).hasClass('toeOptDisabled')},
            msgElID: 'toeShippingMsg',
            onSuccess: function(res) {
                if(toeTables['toe_shipping_modules_list']) {
                    toeTables['toe_shipping_modules_list'].redrawRow('id', res.data[0].id, res.data[0]);
                }
            }
        });
    }
    return ;
}
// -->
</script>
<h1><?php lang::_e('Shipping Modules')?></h1>
<div class="tab_form">
    <form id="toeShippingModulesForm">
        <table wdith="100%">
            <?php foreach($this->fields as $fKey => $f) { if(in_array($f->getHtml(), array('hidden'))) continue;?>
            <tr>
                <td><?php lang::_e($f->label)?>: </td>
                <td>
					<?php $f->display()?>
					<?php if($fKey == 'code') { // Small promo - ?>
						<a target="_blank" href="http://readyshoppingcart.com/products_categories/shipping/?ref=cust" id="" class="button button-primary button-large toeGreenButton"><?php lang::_e('Get more Shipping Methods!')?></a>
					<?php }?>
				</td>
            </tr>
            <?php }?>
            <tr>
                <td>
                    <div id="toeShippingMsg"></div>
                </td>
                <td>
                    <?php echo html::hidden('id', array('value' => ''))?>
                    <?php echo html::hidden('active', array('value' => '1'))?>
                    <?php echo html::hidden('page', array('value' => 'shipping'))?>
                    <?php echo html::hidden('action', array('value' => 'storeModule'))?>
                    <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
                    <?php echo html::submit('save', array('value' => lang::_('Save'), 'attrs' => 'class="button button-primary button-large"'))?>
                </td>
            </tr>
        </table>
    </form>
</div>
<div style="clear: both;"></div>
<div class="options_list">
    <table id="toe_shipping_modules_list" width="100%">
        <tr class="toe_admin_row_header">
            <td><?php lang::_e('ID')?></td>
            <td><?php lang::_e('Label')?></td>
            <td><?php lang::_e('Status')?></td>
            <td><?php lang::_e('Action')?></td>
        </tr>
        <tr class="toe_admin_row toeShippingRow toeRowExample" onclick="toeEditShipping(this);">
            <td class="id"></td>
            <td class="label"></td>
            <td align="center"><a href="#" class="active" onclick="toeSwitchShippingModuleStatus(this); return false;"></a></div></td>
            <td>
                <a href="#" onclick="toeRemoveShipping(this); return false;">
                    <?php echo html::img('cross.gif')?>
                </a>
            </td>
        </tr>
    </table>
</div>
<div><?php echo html::button(array('attrs' => 'id="addShippingModule" class="button button-primary button-large"', 'value' => lang::_('Add New Shipping module')))?></div>