<tr class="form-field">
	<th scope="row" valign="top"><label for=""><?php lang::_e('Brand ID');?></label></th>
	<td>
		<?php echo $this->tag->term_id?>
	</td>
</tr>
<tr class="form-field">
	<th><label for="brand_url"><?php lang::_e('Brand\'s URL');?></label></th>
	<td>
		<input id="brand_url" type="text" value="<?php echo $this->brand_url;?>" name="brand_url" />
		<p class="description"><?php lang::_e('Enter the URL to brand website');?></p>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="product_brand_thumb_url"><?php lang::_e('Current Image');?></label></th>
	<td>
		<input type="hidden" name="product_brand_thumb_url" id="product_category_thumb_url" value="<?php echo $this->brand_thumb; ?>"/><br />
		<img id="image" src="<?php echo $this->brand_thumb;?>" alt="<?php echo $this->tag->name;?>" /> <br />
		<p class="description">You can add the image to category</p>
	</td>
</tr>
<tr class="form-field">
	<th>&nbsp;</th>
	<td>
		<input class="button" id="btn-upload" type="button" value="<?php lang::_e('Select an image');?>" />
		<input class="button" id="btn-delete" type="button" value="<?php lang::_e('Delete image');?>" />
		<p class="description"><?php lang::_e('You can upload PNG, JPG or GIF image');?></p>
	</td>
</tr>
