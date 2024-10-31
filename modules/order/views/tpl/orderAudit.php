<div class="toeOrderAuditHeader"><h1><?php lang::_e('Order Changes Audit')?></h1></div>
<?php if(!empty($this->messages)) { ?>
	<?php foreach($this->messages as $m) { ?>
		<div class="toeOrderAuditMessage">
			<b>
			<?php 
				echo empty($m['user_nicename']) ? 
					lang::_('Guest') : 
					$m['user_nicename'];
				echo ' ';
				echo date(S_DATE_FORMAT_HIS, $m['date_created']);
			?>
			</b>:
			<?php echo $m['data']?>
		</div>
	<?php }?>
<?php }?>