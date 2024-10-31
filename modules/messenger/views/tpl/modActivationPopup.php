<div id="toeModActivationPopupShell" style="display: none;">
	<center>
		<form id="toeModActivationPopupForm">
			<label>
				<?php lang::_e('Enter your activation key here')?>:
				<?php echo html::text('activation_key')?>
			</label>
			<?php echo html::hidden('page', array('value' => 'options'))?>
			<?php echo html::hidden('action', array('value' => 'activatePlugin'))?>
			<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
			<?php echo html::hidden('plugName')?>
			<?php echo html::hidden('goto')?>
			<?php echo html::submit('activate', array('value' => lang::_('Activate')))?>
			<br />
			<div id="toeModActivationPopupMsg"></div>
		</form>
	</center>
</div>