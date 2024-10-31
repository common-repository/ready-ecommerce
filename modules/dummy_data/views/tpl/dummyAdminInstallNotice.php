
<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	jQuery('#toeRemoveDummyDataInstallNotice').click(function(){
		jQuery.sendForm({
			msgElID: 'toeRemoveDummyDataInstallNoticeMsg',
			data: {mod: 'dummy_data', action: 'removeDummyDataInstallNotice', reqType: 'ajax'},
			onSuccess: function(res) {
				if(!res.error) {
					jQuery('#toeDummyDataInstallNotice').hide(TOE_DATA.animationSpeed);
				}
			}
		});
		return false;
	});
});
// -->
</script>
<div class="info_box" style="width: 100%; margin: -1px 15px 0 5px;" id="toeDummyDataInstallNotice">
	<?php lang::_e(array('Now You can install dummy data for your store! Just go to Ready! Ecommerce ->  Options -> Dummy Data or click', 
		'<a href="'. uri::_(array('baseUrl' => get_admin_url(0, 'admin.php?page=toeoptions#toe_opt_dummy_data'))).'">',
		'here',
		'</a>',
		'and install dummy data!'))?>
	<a href="#" class="toe_opt_remove_productfield" id="toeRemoveDummyDataInstallNotice"><?php echo html::img('cross.gif')?></a>
	<span id="toeRemoveDummyDataInstallNoticeMsg"></span>
</div>
