<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	toeTables['states_list'] = new toeListTable('toeStatesEditTable', <?php echo utils::jsonEncode($this->allStates)?>);
    toeTables['states_list'].draw();
	jQuery('#toeAddStateButton').click(function(){
		toeShowEditStateDialog();
		return false;
	});
	jQuery('#toeStateRestoreButton').click(function(){
		jQuery.sendForm({
			msgElID: 'toeStatesRemoveRestoreMsg',
			data: {page: 'states', action: 'restore', reqType: 'ajax'},
			onSuccess: function(res) {
				if(!res.error && res.data) {
					// Clear all countries table and fill it with new data
					toeTables['states_list']
						.clearTable()
						.setData(res.data)
						.draw();
				}
			}
		});
		return false;
	});
});
// -->
</script>
<h1><?php lang::_e('States')?></h1>
<div style="width: 200px;">
	<?php echo html::button(array('attrs' => 'id="toeAddStateButton" class="button"', 'value' => lang::_('Add')))?>
	<a href="#" class="toeOptTip" tip="<?php lang::_e('Add new state to your store')?>"></a>
	<br />
	<?php echo html::button(array('attrs' => 'id="toeStateRestoreButton" class="button"', 'value' => lang::_('Restore by default')))?>
	<a href="#" class="toeOptTip" tip="<?php lang::_e('Restore default states list. Be advised - this will remove all changes that you have done with states!')?>"></a>
	<div id="toeStatesRemoveRestoreMsg"></div>
</div>
<table width="100%" id="toeStatesEditTable" class="options_list">
	<tr class="toe_admin_row_header sdHeader">
		<td><?php lang::_e('ID')?></td>
		<td><?php lang::_e('Name')?></td>
		<td><?php lang::_e('Country')?></td>
		<td><?php lang::_e('Action')?></td>
	</tr>
	<tr class="toe_admin_row toeRowExample">
		<td class="id" onclick="toeEditState(this);"></td>
		<td class="name" onclick="toeEditState(this);"></td>
		<td class="c_iso_code_3" onclick="toeEditState(this);"></td>
		<td class="delete"><a href="" onclick="toeRemoveState(this); return false;"><?php echo html::img('cross.gif')?></a></td>
	</tr>
</table>
<?php /* This will be used in popup to add/edit state */?>
<div id="toeStatesEditFormShell" style="display: none;">
<?php echo html::formStart('toeStates', array())?>
	<label for="toeStateEditName"><?php lang::_e('Name')?>: </label><?php echo html::text('name', array('attrs' => 'id="toeStateEditName"'))?><br />
	<label for="toeStateEditCode"><?php lang::_e('Code')?>: </label><?php echo html::text('code', array('attrs' => 'id="toeStateEditCode"'))?><br />
	<label for="toeStateEditCountry"><?php lang::_e('Country')?>: </label><?php echo html::countryList('country_id', array('attrs' => 'id="toeStateEditCountry"', 'notSelected' => false))?><br />
	<?php echo html::hidden('id')?>
	<?php echo html::hidden('page', array('value' => 'states'))?>
	<?php echo html::hidden('action', array('value' => 'save'))?>
	<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo html::submit('save', array('value' => lang::_('Save'), 'attrs' => 'class="button button-primary"'))?>
	<div class="toeStatesEditMsg"></div>
<?php echo html::formEnd()?>
</div>