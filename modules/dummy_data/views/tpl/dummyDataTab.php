<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	jQuery('#toeDummyDataInstallProductsForm, #toeDummyDataRemoveProductsForm, #toeDummyDataReactivateWidgetsByDefaultForm').submit(function(){
		jQuery(this).sendForm({
			msgElID: 'toeDummyDataMsg',
			success: function(res) {
				
			}
		});
		return false;
	});
});
// -->
</script>
<h1><?php lang::_e('Dummy Data')?></h1>
<table>
	<tr>
		<td>
			<form id="toeDummyDataInstallProductsForm">
				<?php echo html::hidden('mod', array('value' => 'dummy_data'))?>
				<?php echo html::hidden('action', array('value' => 'installProducts'))?>
				<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
				<?php echo html::submit('install', array('value' => lang::_('Install Dummy Products')))?>
			</form>
		</td>
		<td><a href="#" class="toeOptTip" tip="<?php lang::_e('Install Dummy products. This is test data products and with it help you can check your store and theme: how it work, display products, categories, etc. When you will be ready to go live - just delete them using button bellow.')?>"></a></td>
	</tr>
	<tr>
		<td>
			<form id="toeDummyDataRemoveProductsForm">
				<?php echo html::hidden('mod', array('value' => 'dummy_data'))?>
				<?php echo html::hidden('action', array('value' => 'removeProducts'))?>
				<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
				<?php echo html::submit('install', array('value' => lang::_('Remove Dummy Products')))?>
			</form>
		</td>
		<td><a href="#" class="toeOptTip" tip="<?php lang::_e('Remove Dummy products. If you installed them before and don\'t want to use anymore - just click on this button!')?>"></a></td>
	</tr>
	<tr>
		<td>
			<form id="toeDummyDataReactivateWidgetsByDefaultForm">
				<?php echo html::hidden('mod', array('value' => 'dummy_data'))?>
				<?php echo html::hidden('action', array('value' => 'reactivateWidgets'))?>
				<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
				<?php echo html::submit('install', array('value' => lang::_('Reactivate widgets by default')))?>
			</form>
		</td>
		<td><a href="#" class="toeOptTip" tip="<?php lang::_e('Activate default widgets settings for this theme. Click on it - and all widgets will be set in their position as this is on demo site!')?>"></a></td>
	</tr>
</table>
<div id="toeDummyDataMsg"></div>
