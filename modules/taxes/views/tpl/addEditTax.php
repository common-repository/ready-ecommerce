<?php
    $type = $this->taxFields['type']->value;
    if ($type == '') $type = 'total';
    if(!is_array($this->taxFields['data']->value)) {
        if(empty($this->taxFields['data']->value)) 
            $this->taxFields['data']->value = array();
        else
            $this->taxFields['data']->value = utils::jsonDecode ($this->taxFields['data']->value);
    }
    $cOptions = frame::_()->getModule('products')->getHelper()->getCategoriesList();
    if(!empty($cOptions)) {
        if(!isset($this->taxFields['data']->value['categories']) || !is_array($this->taxFields['data']->value['categories']))
                $this->taxFields['data']->value['categories'] = array();
    }
    $listOfDest = array();
    foreach(fieldAdapter::$userfieldDest as $d) {
        $listOfDest[] = array(
            'id' => $d,
            'text' => $d,
            'checked' => (isset($this->taxFields['data']->value['dest']) 
					&& is_array($this->taxFields['data']->value['dest']) 
					&& in_array($d, $this->taxFields['data']->value['dest'])),
        );
    }
?>
<style type="text/css">
    #editTaxForm input {
        width: auto;
    }
    .toeTaxNonusualUserfieldRow {
        display: none;
    }
</style>
<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    jQuery('input[name=selectAllCountries]').change(function(){
        if(jQuery(this).attr('checked')) {
            jQuery('select[name="data[country][]"]').children('option[value!=0]').attr('selected', 'selected');
        } else {
            jQuery('select[name="data[country][]"]').children('option').removeAttr('selected');
        }
        toeStatesObjects['datastate'].countryChange(jQuery('select[name="data[country][]"]').val());
    });
    jQuery('input[name="data[selectAllCategories]"]').change(function(){
        if(jQuery(this).attr('checked')) {
            jQuery('select[name="data[categories][]"]').children('option[value!=0]').attr('selected', 'selected');
        } else {
            jQuery('select[name="data[categories][]"]').children('option').removeAttr('selected');
        }
    });
    jQuery('input[name="data[showAllUserfields]"]').change(function(){
        if(jQuery(this).attr('checked')) {
            jQuery('.toeTaxNonusualUserfieldRow').show(TOE_DATA.animationSpeed);
        } else {
            jQuery('.toeTaxNonusualUserfieldRow').hide(TOE_DATA.animationSpeed);
        }
    });
});
// -->
</script>
<h1><?php lang::_e(array(($this->method == 'post' ? 'Add' : 'Edit'), 'Tax'))?></h1>
<form action="<?php echo uri::mod('currency', '', '', array('id' => $this->id))?>" method="post" id="editTaxForm">
    <table width="100%">
        <tr>
            <td valign="top">
                <table width="100%">
                    <tr>
                        <td>
                            <?php lang::_e('Tax Name')?>: 
                            <a href="#" class="toeOptTip" tip="<?php lang::_e('Label for Tax')?>"></a>
                        </td>
                        <td><?php $this->taxFields['label']->display()?></td>
                    </tr>
                    <tr>
                        <td>
                            <?php lang::_e('Tax rate')?>: 
                            <a href="#" class="toeOptTip" tip="<?php lang::_e("Taxe Rate. Set from 0 to 100, in percents by default. Or check 'Absolute' checkbox for absolute value.")?>"></a>
                        </td>
                        <td>
                            <?php echo html::text('data[rate]', array('value' => (isset($this->taxFields['data']->value['rate']) ? $this->taxFields['data']->value['rate'] : '')))?><br />
                            <?php echo html::checkbox('data[absolute]', array('value' => 1, 'checked' => (isset($this->taxFields['data']->value['absolute']) && (bool)$this->taxFields['data']->value['absolute'])))?>
                            <?php lang::_e('Absolute')?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php lang::_e('Country for applying')?>: 
                            <a href="#" class="toeOptTip" tip="<?php lang::_e('If you would like to apply tax only for specific countries - select them here. Check "Apply to all countries" to select them all.')?>"></a>
                        </td>
                        <td>
                            <?php echo html::countryListMultiple('data[country][]', array('value' => (isset($this->taxFields['data']->value['country']) ? $this->taxFields['data']->value['country'] : '')))?><br />
                            <?php echo html::checkbox('selectAllCountries')?> <?php lang::_e('Apply to all countries')?><br />
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php lang::_e('States for applying')?>: 
                            <a href="#" class="toeOptTip" tip="<?php lang::_e('If you would like to apply tax only for specific states - select them here. You must select country at first')?>"></a>
                        </td>
                        <td>
                            <?php echo html::statesListMultiple('data[state]', array('value' => (isset($this->taxFields['data']->value['state']) ? $this->taxFields['data']->value['state'] : '')))?><br />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php lang::_e('Categories for apply')?>: 
                            <a href="#" class="toeOptTip" tip="<?php lang::_e('If you would like to apply tax only for products from specific categories - select them here. Check "Apply to all categories" to select them all.')?>"></a>
                        </td>
                        <td>
                            <?php echo (empty($cOptions) ? lang::_('You have no categories') : html::selectlist('data[categories][]', array('options' => $cOptions, 'value'=>$this->taxFields['data']->value['categories'])))?><br />
							<?php echo html::checkbox('data[selectAllCategories]', array('checked' => (isset($this->taxFields['data']->value['selectAllCategories']) ? $this->taxFields['data']->value['selectAllCategories'] : '')))?>
                            <?php lang::_e('Apply to all categories')?>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table width="100%">
                    <tr>
                        <td>
                            <?php lang::_e('Apply Tax')?>: 
                            <a href="#" class="toeOptTip" tip="<?php lang::_e('This will determine for what country - shipping, billing or registration, - tax will be applied')?>"></a>
                        </td>
                        <td><?php echo html::checkboxlist('data[dest][]', array('options' => $listOfDest, 'value'=> (isset($this->taxFields['data']->value['dest']) ? $this->taxFields['data']->value['dest'] : ''), 'delim' => '<br />'))?></td>
                    </tr>
                    <tr>
                        <td>
                            <?php lang::_e('Apply Tax to shipping cost')?>: 
                            <a href="#" class="toeOptTip" tip="<?php lang::_e('If Yes - tax rate will calculate including shipping cost (if it\'s available)')?>"></a>
                        </td>
                        <td><?php echo html::radiobuttons(
                                'data[applyToShipping]', 
                                array(
                                    'value' => (isset($this->taxFields['data']->value['applyToShipping']) ? $this->taxFields['data']->value['applyToShipping'] : ''), 
                                    'options' => array(lang::_('No'), lang::_('Yes')),
                                )
                        )?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo html::hidden('id', array('value' => $this->id))?>
                <?php echo html::hidden('action', array('value' => $this->method. 'Tax'))?>
                <?php echo html::hidden('page', array('value' => 'taxes'))?>
                <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
                <?php echo html::button(array('attrs' => 'class="button button-primary"', 'value' => lang::_('Save')))?>
            </td>
            <td id="mod_msg_tax" class="mod_msg"></td>
        </tr>
    </table>
</form>