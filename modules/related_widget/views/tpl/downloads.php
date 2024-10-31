<div class="product_related_wrapper">
<?php //var_dump($this->related_products);?>
<?php //var_dump($this->not_related_products);?>
<?php if(count($this->not_related_products)!=0) :?>
<table>
<tr>
<td>Add related product</td>
<td>
<select id="rel_to" name="rel_to">
<?php foreach ($this->not_related_products as $rp): ?>
<option value="<?php echo $rp['post_id'];?>"><?php echo $rp['post_title'];?></option>
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
<option value="<?php echo $rp['id'];?>"><?php echo $rp['post_title'];?></option>
<?php endforeach;?>
</select>
</td>
<td><input type="button" value="<?php lang::_e('Delete');?>" name="del_rel" class="del_rel"  /></td>
</tr>
</table>
<?php endif;?>

</div>       
