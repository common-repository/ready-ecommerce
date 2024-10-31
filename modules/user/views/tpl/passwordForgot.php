<h1><?php lang::_e('Password Recovery')?></h1>
<form action="" method="post" onsubmit="toePasswordRecover(this); return false;">
	<?php lang::_e('Type down your email address and we\'ll send you recovery letter.')?><br />
	<?php echo html::text('email')?><br />
	<?php echo html::capcha()?><br />
	<?php echo html::hidden('mod', array('value' => 'user'))?>
	<?php echo html::hidden('action', array('value' => 'passwordRecoverSendLink'))?>
	<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo html::inputButton(array('value' => lang::_('Go Back'), 'attrs' => 'onclick="subScreen.clearAndHide(); return false;"'))?>
	<?php echo html::submit('recover', array('value' => lang::_('Recover')))?>
</form>
<div id="toePasswordForgotMsg"></div>