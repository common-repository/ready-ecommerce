<table width="100%" id="currencies_list" class="options_list">
    <tr class="toe_admin_row_header">
        <td><?php lang::_e('ID')?></td>
        <td><?php lang::_e('Label')?></td>
        <td><?php lang::_e('Code')?></td>
        <td><?php lang::_e('Action')?></td>
    </tr>
    <?php foreach($this->currency as $c) { ?>
    <tr class="toe_admin_row toe_opt_currency">
        <td><?php echo $c['id']?></td>
        <td><?php echo $c['label']?></td>
        <td><?php echo $c['code']?></td>
        <td><a href="" class="toe_opt_remove_currency" onclick="removeCurrency(this); return false;"><?php echo html::img('cross.gif')?></a></td>
    </tr>
    <?php }?>
</table>
<div><?php echo html::button(array('attrs' => 'id="add_opt_currency" class="button"', 'value' => lang::_('Add new currency')))?></div>
