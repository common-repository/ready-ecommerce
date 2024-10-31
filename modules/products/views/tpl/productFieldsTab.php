<table width="100%" id="product_fields_list" class="options_list">
<tr class="toe_admin_row_header">
    <td><?php lang::_e('ID')?></td>
    <td><?php lang::_e('Label')?></td>
    <td><?php lang::_e('Code (Meta Key)')?></td>
    <td><?php lang::_e('Type')?></td>
    <td><?php lang::_e('Action')?></td>
</tr>
<?php foreach($this->productfields as $uf) { ?>
<tr class="toe_admin_row toe_opt_productfield">
    <td><?php echo $uf['id']?></td>
    <td><?php echo $uf['label']?></td>
    <td><?php echo $uf['code']?></td>
    <td><?php echo $uf['type']?></td>
    <td><a href="#" class="toe_opt_remove_productfield" onclick="removeProductfield(this); return false;"><?php echo html::img('cross.gif')?></a></td>
</tr>
<?php }?>
</table>
<div><?php echo html::button(array('attrs' => 'id="add_opt_productfield" class="button button-primary"', 'value' => lang::_('Add')))?></div>
