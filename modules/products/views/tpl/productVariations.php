<?php if(!empty($this->variations)) { ?>
<div class="toeVariationSelect">
	<?php if(!empty($this->parent_id)) { ?>
	<input type="hidden" name="variation_parent_id" class="variation_parent_id" value="<?php echo $this->parent_id; ?>"/>
	<input type="hidden" name="variation_current_id" class="variation_current_id" value="<?php echo $this->current_id; ?>"/>
	<?php }?>
	<h2><?php lang::_e('Select variation'); ?></h2>
	<select class="toeVariationsSelectList">
		<?php foreach ($this->variations as $var) : ?>
			<option value="<?php echo $var[0]['post_id'];?>" <?php if(isset($var[0]['selected'])) echo 'selected'; ?>><?php echo $var[0]['var_name'];?></option>
		<?php endforeach;?>
	</select>
</div>
<?php }?>