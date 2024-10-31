<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    jQuery('#toeLoginFormSubmit').click(function(){
        var data = jQuery('#toeLoginFormFields').serializeAnything({
            reqType: 'ajax',
            mod: 'user',
            action: 'postLogin'
        });
        jQuery(this).sendForm({
            msgElID: 'toeLoginMsg',
            data: data,
            inputsWraper: 'toeLoginFormFields',
            onSuccess: function(res) {
                if(!res.error && res.data.redirect) 
                   document.location.href = res.data.redirect;
            }
        });
        return false;
    });
});
// -->
</script>
<?php if(!$this->fieldsOnly) {
    echo html::formStart('userLogin', array('method' => 'POST', 'attrs' => 'id="loginForm" onsubmit="return false;"'));
}?>
    <table id="toeLoginFormFields">
        <tr>
            <td>
                <div class="forminput">
                    <?php echo html::text('user_login', array('attrs' => 'placeholder="'.__('Username').'"'))?>
                    <div class="toeErrorForField toe_<?php echo html::nameToClassId('user_login')?>"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="forminput">
                    <?php echo html::password('user_password', array('attrs' => 'placeholder="'.__('Password').'"'))?>
                    <div class="toeErrorForField toe_<?php echo html::nameToClassId('user_password')?>"></div>
                </div>
            </td>
        </tr>
		<tr>
			<td><?php lang::_e('Remember Me')?>
				<div class="forminput">
					<?php echo html::checkbox('remember', array('value' => 1))?>
				</div>
			</td>
		</tr>
        <tr>
            <td>
                <?php if(!empty($this->toeReturn)) {
                    echo html::hidden('toeReturn', array('value' => $this->toeReturn));
                }?>
                <div class="toePasswordForgotLinkBox">
                    <a href="#" class="toeForgotPasswordLink"><?php lang::_e('Forgot password?')?></a>
                </div>
                <?php echo html::submit('login', array('value' => lang::_('Login'), 'attrs' => 'class="tcf_submit" id="toeLoginFormSubmit"'))?>
                <div id="toeLoginMsg"></div>
            </td>
        </tr>
		
    </table>
<?php if(!$this->fieldsOnly) {
    echo html::formEnd();
}?>
<?php if(!empty($this->registrationLink)) {?>
<div>
    <?php lang::_e('If you have no account - you can')?>
    <a href="<?php echo $this->registrationLink?>"><?php lang::_e('Register')?></a>
    <?php lang::_e('here')?>
</div>
<?php }?>