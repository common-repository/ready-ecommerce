<table width="100%" id="taxes_list" class="options_list">
    <tr class="toe_admin_row_header">
        <td><?php lang::_e('ID')?></td>
        <td><?php lang::_e('Label')?></td>
        <td><?php lang::_e('Rate')?></td>
        <td><?php lang::_e('Action')?></td>
    </tr>
    <?php if($this->taxes) {?>
        <?php foreach($this->taxes as $c) { ?>
        <tr class="toe_admin_row toe_opt_taxes">
            <td><?php echo $c['id']?></td>
            <td><?php echo $c['label']?></td>
            <td><?php echo $c['data']['rate']?></td>
            <td><a href="#" class="toe_opt_remove_tax" onclick="removeTax(this); return false;"><?php echo html::img('cross.gif')?></a></td>
        </tr>
        <?php }?>
    <?php }?>
</table>
<div><?php echo html::button(array('attrs' => 'id="add_opt_taxes" class="button"', 'value' => lang::_('Add New Tax')))?></div>