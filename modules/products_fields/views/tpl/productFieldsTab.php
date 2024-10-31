<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	toeTables['products_fields_list'] = new toeListTable('toeProductsFieldsList', <?php echo utils::jsonEncode($this->fieldsList)?>);
    toeTables['products_fields_list'].draw();
});
// -->
</script>
<h1><?php lang::_e('Product Fields')?></h1>
<style type="text/css">
	.toeProductFieldsOptionShell{
		position: relative;
		float: left;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box; 
		box-sizing: border-box;
		margin: 3px;
		border: solid 1px #ccc;
		-webkit-border-radius: 13px;
		-moz-border-radius: 13px;
		border-radius: 13px;
		background: #f9f9f9;
		padding: 10px;
		cursor: move;
	}
	.toeProductFieldsOptionShell:hover{
		-webkit-box-shadow: 0 0 17px rgba(50, 50, 50, 1);
		-moz-box-shadow:    0 0 17px rgba(50, 50, 50, 1);
		box-shadow:         0 0 17px rgba(50, 50, 50, 1);
	}
</style>
<div id="toeProductFieldsEditShell">
	<?php echo html::formStart('productFieldsForm', array('attrs' => 'id="toeProductFieldsEditForm"'))?>
		<table width="700px">
			<tr>
				<td valign="top" width="400px">
					<table width="100%">
						<tr>
							<td><?php lang::_e('Label')?>:</td>
							<td><?php echo html::text('label')?></td>
						</tr>
						<tr>
							<td><?php lang::_e('Type')?>:</td>
							<td><?php echo html::selectbox('htmltype_id', array('options' => $this->availableHtmlTypes, 'attrs' => 'id="toeProductFieldsHtmlTypeId"'))?></td>
						</tr>
						<tr>
							<td valign="top"><?php lang::_e('Default Value')?>:</td>
							<td id="toeProductFieldsDefaultValueShell"><?php /*Empty - it will be filled in JS after load, same method as on change html type*/?></td>
						</tr>
						<tr id="toeProductFieldsOptionsRow" style="display: none;">
							<td valign="top" width="90px"><?php lang::_e('Options')?>:</td>
							<td>
								<div id="toeProductFieldsOptionsShell"></div>
								<?php echo html::button(array('value' => lang::_('Add Option'), 'attrs' => 'id="toeProductFieldsAddOption" class="button"'))?>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top">
					<table width="100%">
						<?php if(!$this->pid) {?>
						<tr>
							<td colspan="2">
								<table width="100%">
									<tr>
										<td><?php lang::_e('Categories')?></td>
										<td><?php lang::_e('Products')?></td>
										
									</tr>
									<tr>
										<td><?php echo html::categorySelectlist('categories', array('select_all' => 'true', 'size' => 15))?></td>
										<td><?php echo html::productsSelectlist('products', array('select_all' => 'true', 'size' => 15))?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?php }?>
						<tr>
							<td width="30%"><?php lang::_e('Mandatory')?>:</td>
							<td><?php echo html::checkbox('mandatory', array('value' => 1, 'checked' => 0))?></td>
						</tr>
						<tr>
							<td width="30%"><?php lang::_e('Active')?>:</td>
							<td><?php echo html::checkbox('active', array('value' => 1, 'checked' => 1))?></td>
						</tr>
						<tr>
							<td width="30%"><?php lang::_e('Order')?>:</td>
							<td><?php echo html::text('sort_order')?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo html::hidden('page', array('value' => 'products_fields'))?>
					<?php echo html::hidden('action', array('value' => 'saveProductField'))?>
					<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
					<?php echo html::hidden('pid', array('value' => $this->pid))?>
					<?php echo html::hidden('id')?>
					<?php echo html::hidden('original_id')?>
					<?php echo html::submit('save', array('value' => lang::_('Save'), 'attrs' => 'class="button-primary"'))?>
					<span id="toeProductFieldsEditFormMsg"></span>
				</td>
			</tr>
		</table>
	<?php echo html::formEnd()?>
	<div id="toeProductFieldsOptionExample" style="display: none;" class="toeProductFieldsOptionShell">
		<?php echo html::text('options[][label]', array('attrs' => 'placeholder="'. lang::_('Label'). '" onchange="toeRebuildProdOptionsDefaultValueSelect();" class="toeProductFieldsOptionLabel"'))?>
		<span class="toeDeleteButt"></span>
		<br />
		<?php echo html::text('options[][price]', array('attrs' => 'placeholder="'. lang::_('Price'). '"'))?><br />
		<?php lang::_e('Absolute')?>: <?php echo html::checkbox('options[][absolute]', array('value' => 1))?>, 
		<?php lang::_e('Order')?>: <?php echo html::text('options[][sort_order]', array('attrs' => 'size="2" class="toeProductFieldsOptionSortOrder"'))?>
		<?php echo html::hidden('options[][id]')?>
	</div>
</div>
<table width="100%" id="toeProductsFieldsList" class="options_list">
<tr class="toe_admin_row_header sdHeader">
    <td style="display: none;"><?php lang::_e('ID')?></td>
    <td><?php lang::_e('Label')?></td>
    <td><?php lang::_e('Type')?></td>
    <td><?php lang::_e('Action')?></td>
</tr>
<tr class="toe_admin_row toeRowExample">
	<td class="id" onclick="toeEditProdField(this);" style="display: none;"></td>
	<td class="label" onclick="toeEditProdField(this);"></td>
	<td class="htmltype" onclick="toeEditProdField(this);"></td>
	<td class="delete"><a href="" onclick="toeRemoveProdField(this); return false;"><?php echo html::img('cross.gif')?></a></td>
</tr>
</table>
<div><?php echo html::button(array('attrs' => 'id="toeAddNewProductField" class="button-primary"', 'value' => lang::_('Add New Product Field')))?></div>
<script type="text/javascript">
// <!--
jQuery(function(){
	toeChangeProdFieldType();
	jQuery('#toeProductFieldsOptionsShell').sortable({
		stop: function() {
			toeFillOptionsSortOrder();
		}
	});
});
// -->
</script>