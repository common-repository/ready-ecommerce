<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    jQuery(".add_rel").click(function(){
       var link = jQuery(this); 
           var id = link.attr('rel');
           var data = {
               id : jQuery("#post_ID").val(),
			   rel_to : jQuery("#rel_to").val(),
               action: 'related_add'
           };
           jQuery.post(ajaxurl, data, function(res){
               //link.parent().remove();
			   jQuery(".add_rel").off('click');
			   jQuery(".product_related_wrapper").html(res);
           });
    });
    jQuery(".del_rel").click(function(){
       var link = jQuery(this); 
           var id = link.attr('rel');
           var data = {
               id : jQuery("#post_ID").val(),
			   rel_from : jQuery("#rel_from").val(),
               action: 'related_delete'
           };
           jQuery.post(ajaxurl, data, function(res){
               //link.parent().remove();
			   jQuery(".del_rel").off('click');
			   jQuery(".product_related_wrapper").html(res);
           });
    });
});



// -->
</script>
<div style="color:green"><?php echo $this->msg;?></div>
<?php //var_dump($this->related_products);?>
<?php //var_dump($this->not_related_products);?>
<?php if(count($this->not_related_products)!=0) :?>
<table>
<tr>
<td>Add related product</td>
<td>
<select id="rel_to" name="rel_to">
<?php foreach ($this->not_related_products as $rp): ?>
<option value="<?=$rp['post_id'];?>"><?=$rp['post_title'];?></option>
<?php endforeach;?>
</select>
</td>
<td><input type="button" value="<?php lang::_e('Add');?>" name="add_rel" class="add_rel" /></td>
</tr>
</table>
<?php endif;?>


<?php if(count($this->related_products)!=0) :?>
<table>
<tr>
<td>Delete related product</td>
<td>
<select id="rel_from" name="rel_from">
<?php foreach ($this->related_products as $rp): ?>
<option value="<?=$rp['id'];?>"><?=$rp['post_title'];?></option>
<?php endforeach;?>
</select>
</td>
<td><input type="button" value="<?php lang::_e('Delete');?>" name="del_rel" class="del_rel"  /></td>
</tr>
</table>
<?php endif;?>
