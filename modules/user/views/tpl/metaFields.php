<table>
    <tr><td colspan="2"><h1><?php lang::_e('Store User Data')?></h1></td></tr>
	<?php if(!empty($this->metaFields) && is_array($this->metaFields)) {?>
		<?php foreach($this->metaFields as $key => $f) { ?>
		<?php if(in_array($key, array('password', 're_password'))) continue;?>
		<tr>
			<td><?php lang::_e($f->label)?></td>
			<td><div class="forminput">
			<?php 
				 echo $f->display();
			 ?></div>
			</td>
		</tr>
		<?php }?>
	<?php }?>
    <?php if($this->showPassword && is_object($this->metaFields['password'])) { ?>
    <tr>
        <td><?php lang::_e($this->metaFields['password']->label)?></td>
        <td>
            <?php echo html::text('toePassword', array('value' => $this->metaFields['password']->getValue(), 'attrs' => 'readonly="true"'))?>
        </td>
    </tr>
    <?php }?>
    <tr>
        <td align="left">
            <a href="" id="toeResetPasswordLink" onclick="return false;"><?php lang::_e('Reset password')?></a>
            <script type="text/javascript">
            // <!--
            jQuery(document).ready(function(){
                jQuery('#toeResetPasswordLink').click(function(){
                    jQuery(this).sendForm({
                        msgElID: 'toeResetPasswordMsg',
                        data: {mod: 'user', action: 'resetPassword', reqType: 'ajax', uid: <?php echo (string)$this->uid?>}
                    });
                    return false;
                });
            });
            // -->
            </script>
        </td>
        <td id="toeResetPasswordMsg"></td>
    </tr>
	<?php if($this->haveOrders) { ?>
		<tr><td colspan="2"><a href="<?php uri::_e(array('baseUrl' => admin_url('admin.php'), 'page' => 'orders', 'uid' => $this->uid))?>"><?php lang::_e('View user Orders')?></a></td></tr>
	<?php }?>
</table>