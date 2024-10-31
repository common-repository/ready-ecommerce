<?php if(empty($this->errors)) { ?>
	<div class="toeSuccessMsg"><?php lang::_e(array('Your password was reset.', 
		'New password was sent to your email.', 
		'Now you can', 
		'<a href="'. frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getLoginForm')). '">', 
		'login', 
		'</a>', 
		'to your account using new password.'))?></div>
<?php } else { ?>
	<div class="toeErrorMsg"><?php echo implode('<br />', $this->errors)?></div>
<?php }?>