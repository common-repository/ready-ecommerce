<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	toeTables['countries_list'] = new toeListTable('toeCountriesEditTable', <?php echo utils::jsonEncode($this->allCountries)?>);
    toeTables['countries_list'].draw();
	jQuery('#toeAddCountryButton').click(function(){
		toeShowEditCountryDialog();
		return false;
	});
	jQuery('#toeCountryRestoreButton').click(function(){
		jQuery.sendForm({
			msgElID: 'toeCountriesRemoveRestoreMsg',
			data: {page: 'countries', action: 'restore', reqType: 'ajax'},
			onSuccess: function(res) {
				if(!res.error && res.data) {
					// Clear all countries table and fill it with new data
					toeTables['countries_list']
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
<h1><?php lang::_e('Countries')?></h1>
<div style="width: 200px;">
	<?php echo html::button(array('attrs' => 'id="toeAddCountryButton" class="button"', 'value' => lang::_('Add')))?>
	<a href="#" class="toeOptTip" tip="<?php lang::_e('Add new country to your store')?>"></a>
	<br />
	<?php echo html::button(array('attrs' => 'id="toeCountryRestoreButton" class="button"', 'value' => lang::_('Restore by default')))?>
	<a href="#" class="toeOptTip" tip="<?php lang::_e('Restore default countries list. Be advised - this will remove all changes that you have done with countires!')?>"></a>
	<div id="toeCountriesRemoveRestoreMsg"></div>
</div>

<table width="100%" id="toeCountriesEditTable" class="options_list">
	<tr class="toe_admin_row_header sdHeader">
		<td><?php lang::_e('ID')?></td>
		<td><?php lang::_e('Name')?></td>
		<td><?php lang::_e('Action')?></td>
	</tr>
	<tr class="toe_admin_row toeRowExample">
		<td class="id" onclick="toeEditCountry(this);"></td>
		<td class="name" onclick="toeEditCountry(this);"></td>
		<td class="delete"><a href="" onclick="toeRemoveCountry(this); return false;"><?php echo html::img('cross.gif')?></a></td>
	</tr>
</table>
<?php /* This will be used in popup to add/edit country */?>
<div id="toeCountriesEditFormShell" style="display: none;">
<?php echo html::formStart('toeCountries', array())?>
	<label for="toeCountryEditName"><?php lang::_e('Name')?>: </label><?php echo html::text('name', array('attrs' => 'id="toeCountryEditName"'))?><br />
	<label for="toeCountryEditIso2"><?php lang::_e('ISO2 code')?>: </label><?php echo html::text('iso_code_2', array('attrs' => 'id="toeCountryEditIso2"'))?><br />
	<label for="toeCountryEditIso3"><?php lang::_e('ISO3 code')?>: </label><?php echo html::text('iso_code_3', array('attrs' => 'id="toeCountryEditIso3"'))?><br />
	<?php echo html::hidden('id')?>
	<?php echo html::hidden('page', array('value' => 'countries'))?>
	<?php echo html::hidden('action', array('value' => 'save'))?>
	<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo html::submit('save', array('value' => lang::_('Save'), 'attrs' => 'class="button button-primary"'))?>
	<div class="toeCountriesEditMsg"></div>
<?php echo html::formEnd()?>
</div>