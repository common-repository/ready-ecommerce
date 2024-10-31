<script type="text/javascript">
// <!--
    jQuery(document).ready(function(){
        toeMarkSelectedTpl('<?php echo $this->default_theme?>');
        jQuery('.toeTplActivateButt').click(function(){
			if(jQuery(this).hasClass('toeTplDeactivateButt')) {
				toeUpdateTemplate('');
			} else {
				toeUpdateTemplate( jQuery(this).attr('href') );
			}
            return false;
        });
		jQuery('.toeTplPromoButt').click(function(){
			window.open( jQuery(this).attr('href') );
			return false;
		});
    });
    function toeUpdateTemplate(newValue) {
        jQuery(this).sendForm({
            data: {
                reqType: 'ajax', action: 'putOption', page: 'options', code: 'default_theme', value: newValue
            },
            msgElID: 'toeTplMsg',
            onSuccess: function(res) {
                if(!res.error) {
                    jQuery('#toeTplMsg').html('<?php lang::_e('Template Updated. Reload page to see results.')?>');
                    //if(res.data.value) {
                        toeMarkSelectedTpl(res.data.value);
                    //}
                    if(jQuery('#opt_general_form').exists()) {  //Update option value on General options tab
                        jQuery('#opt_general_form p.toe_opt_values13').html(res.data.value);
                    }
                }
            }
        });
    }
    function toeMarkSelectedTpl(tpl) {
        jQuery('.toeTplOption').removeClass('toeTplOptionSelected').find('.toeTplDeactivateButt').removeClass('toeTplDeactivateButt').val(toeLang('Activate'));
        var selectedBox = jQuery('.toeTplActivateButt[href="'+ tpl+ '"]').addClass('toeTplDeactivateButt').val(toeLang('Deactivate')).parents('div.toeTplOption:first');
        jQuery(selectedBox).addClass('toeTplOptionSelected');
    }
// -->
</script>
<?php foreach($this->templates as $code => $t) { ?>
	<div class="toeTplOption">
		<center><b><?php echo $t->name?></b></center><br />
		<?php $ingLinkProps = isset($t->isPromo) ? 'href="'. $t->href. '" target="_blank"' : 'href="#" onclick="toeUpdateTemplate(\''. $code. '\'); return false;"';?>
		<a class="toeTplImgPrev" <?php echo $ingLinkProps?>>
			<img class="toeTplPicturePrev" src="<?php echo $t->prevImg?>" />
		</a>
		<br />
		<div style="height: 50px;"><?php echo $t->description?></div>
		<div>
			<?php if(isset($t->isPromo)) { ?>
				<input type="button" href="<?php echo $t->href?>" class="button button-primary button-large toeTplPromoButt" value="<?php echo $t->buttVal?>" />
			<?php } else { ?>
				<input type="button" href="<?php echo $code?>" class="button button-primary button-large toeTplActivateButt" value="<?php lang::_e('Activate')?>" />
			<?php }?>
		</div>
	</div>
<?php } ?>
<div style="clear: both;"></div>
<div id="toeTplMsg"></div>