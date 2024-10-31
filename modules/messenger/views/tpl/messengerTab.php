<div class="tab_form" id="messenger_form">
</div>
<table width="100%" id="messenger_list" class="options_list">
<tr class="toe_admin_row_header">
    <td><?php lang::_e('ID')?></td>
    <td><?php lang::_e('Label')?></td>
    <td><?php lang::_e('Module')?></td>
    <td><?php lang::_e('Name')?></td>
    <td><?php lang::_e('Action')?></td>
</tr>
<?php foreach($this->templates as $template) { ?>
<tr class="toe_admin_row toe_opt_messenger_template">
    <td><?php echo $template['id']?></td>
    <td><?php echo $template['label']?></td>
    <td><?php echo $template['module']?></td>
    <td><?php echo $template['name']?></td>
    <td><span><?php echo html::img('edit.png')?></span></td>
</tr>
<?php }?>
</table>

