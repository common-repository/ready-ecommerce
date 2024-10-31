<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    toeTables['user_fields_list'] = new toeUserFieldsListTabe('user_fields_list', <?php echo utils::jsonEncode($this->userfieldsList)?>);
    toeTables['user_fields_list'].draw();
});
// -->
</script>
<table width="100%" id="user_fields_list" class="options_list">
<tr class="toe_admin_row_header">
    <td><?php lang::_e('ID')?></td>
    <td><?php lang::_e('Label')?></td>
    <td><?php lang::_e('Code (Meta Key)')?></td>
    <td><?php lang::_e('Type')?></td>
    <td><?php lang::_e('Status')?></td>
    <td><?php lang::_e('Action')?></td>
</tr>
<tr class="toe_admin_row toe_opt_userfield toeRowExample">
    <td class="id"></td>
    <td class="label"></td>
    <td class="code"></td>
    <td class="type"></td>
    <td><a href="#" class="active" onclick="toeSwitchUserfieldStatus(this); return false;"></a></td>
    <td class="system">
        <a href="" class="toe_opt_remove_userfield" onclick="removeUserfield(this); return false;">
            <?php echo html::img('cross.gif')?>
        </a>
    </td>
</tr>
</table>
<div><?php echo html::button(array('attrs' => 'id="add_opt_userfield" class="button"', 'value' => lang::_('Add')))?></div>
