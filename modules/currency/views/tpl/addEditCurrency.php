<script type="text/javascript">
// <!--
    var toeDefaultCurrViewSelect = {'$1': '$1', '$ 1': '$ 1', '1$': '1$', '1 $': '1 $'};
    var toDefaultPriceViewSelect = ['100 000.00', '100000.00', '100 000,00', '100000,00', '100.000,00', '100 000', '100,000.00'];
    jQuery(function(){
        jQuery('select[name=currency_view]').toeRebuildSelect(toeDefaultCurrViewSelect, true, '<?php echo $this->currencyFields['currency_view']->getValue()?>');
        jQuery('select[name=price_view]').toeRebuildSelect(toDefaultPriceViewSelect, false, '<?php echo $this->currencyFields['price_view']->getValue()?>');

        jQuery('input[name=symbol]').keyup(function(){
            toeChangeCurrencyViewSelect();
        });
        jQuery('select[name=currency_view]').change(function(){
            toeCheckCurrencyPriceView('currencyView');
        });
        jQuery('select[name=price_view]').change(function(){
            toeCheckCurrencyPriceView('priceView');
        });
    });
// -->
</script>
<h1><?php lang::_e(array(($this->method == 'post' ? 'Add' : 'Edit'), 'Currency'))?></h1>
<form action="<?php echo uri::mod('currency', '', '', array('id' => $this->id))?>" method="post" id="editCurrencyForm">
    <table width="100%">
    <?php
        foreach($this->currencyFields as $k => $f) { 
            if (in_array($k, array('', '')) || $f->getHtml() == 'hidden') continue;
    ?>
        <tr>
            <td class="field_label">
                <?php echo $f->label.': '?>
                <?php if ($f->description != ''):?>
                <a href="#" class="toeOptTip" tip="<?php echo $f->description;?>"></a>
                <?php endif;?>
            </td>
            <td>
                <?php 
                    if($k == 'use_as_default') {
                        $f->addHtmlParam('checked', (bool)$f->value);
                    }
                    $f->display()
                ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td>
                <?php foreach(array('symbol_left', 'symbol_right', 'symbol_point', 'symbol_thousand', 'decimal_places') as $k) {
                    $this->currencyFields[$k]->display(); 
                }?>
                <?php echo html::hidden('id', array('value' => $this->id))?>
                <?php echo html::hidden('action', array('value' => $this->method. 'Currency'))?>
                <?php echo html::hidden('page', array('value' => 'currency'))?>
                <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
                <?php echo html::button(array('attrs' => 'class="button button-primary"', 'value' => lang::_('Save')))?>
            </td>
            <td id="mod_msg_curr" class="mod_msg" style="width:0 !important"></td>
        </tr>
    </table>
</form>