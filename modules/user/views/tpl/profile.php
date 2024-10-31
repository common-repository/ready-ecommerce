<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    jQuery('#toeUserData').submit(function(){
        jQuery(this).sendForm();
        return false;
    });
});
// -->
</script>
<?php echo html::formStart('userData', array('attrs' => 'id="toeUserData"'))?>
<?php frame::_()->getModule('user')->getView('user')->displayAllMeta();?>

<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
<?php echo html::hidden('mod', array('value' => 'user'))?>
<?php echo html::hidden('action', array('value' => 'putProfile'))?>
<?php echo html::submit('save', array('value' => lang::_('Update'), 'attrs'=>'class="tcf_submit"'))?>
<div id="msg"></div>
<?php echo html::formEnd()?>