<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	jQuery('#toePagesRecreate').click(function(){
		jQuery(this).sendForm({
			msgElID: 'toePagesOptionsMsg',
			data: {page: 'pages', action: 'recreatePages', reqType: 'ajax'}
		});
		return false;
	});
});
// -->
</script>
<h1><?php lang::_e('Pages Options')?></h1>
<table>
	<tr>
		<td><?php echo html::inputButton(array('value' => lang::_('Recreate Pages'), 'attrs' => 'id="toePagesRecreate" class="button button-primary"'))?></td>
		<td><a href="#" class="toeOptTip" tip="<?php lang::_e('If you accidently deleted one of plugin page - Login, or Checkout, or Shopping cart for example, just click on this button - and pages, that you deleted, will be created again. Do not use it without emergency.')?>"></a></td>
	</tr>
</table>
<div id="toePagesOptionsMsg"></div>