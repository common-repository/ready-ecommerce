<tr class="form-field">
	<th scope="row" valign="top"><label for=""><?php lang::_e('Category ID');?></label></th>
	<td>
		<?php echo $this->tag->term_id?>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="toe_sort_order"><?php lang::_e('Sort order');?></label></th>
	<td>
		<?php echo html::text('sort_order', array('value' => $this->sort_order, 'attrs' => 'id="toe_sort_order" style="width: 60px;"'))?>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="product_category_menu"><?php lang::_e('Create menu item from that category');?></label></th>
	<td>
		<?php if (!empty($this->menus)) {?>
			<?php if ($this->category_menu == '' 
					|| (is_array($this->category_menu) 
						&& isset($this->category_menu['menuItemId']) 
						&& !empty($this->menuItemsIds) 
						&& !in_array($this->category_menu['menuItemId'], $this->menuItemsIds))
				) {?>
			<select id="product_category_menu" name="product_category_menu">
				<option value=""><?php lang::_e('No');?></option>
				<option value="1"><?php lang::_e('Include sub categories');?></option>
				<option value="0"><?php lang::_e('Do not include sub categories');?></option>
			</select>
			<br />
			<?php  echo '<label for="product_category_menu">'.lang::_('Create menu item in this menu').'</label><br />';
				   echo '<select name="choose_menu">';
				   foreach ($this->menus as $menu) {
					  $truncated_name = wp_html_excerpt( $menu->name, 40 );
					  echo '<option value="'.$menu->term_id.'">'.$truncated_name.'</option>';
				   }
				   echo '</select>';
			?>
			<p class="description"><?php lang::_e('You can create menu item from category'); ?></p>
			<?php } else {?>
			<p class="description"><?php lang::_e('This category corresponds to ');?><strong><?php echo (is_array($this->category_menu) ? $this->category_menu['menuName'] : $this->category_menu);?></strong></p>
			<?php }?>
	   <?php } else { ?>
			<p class="description"><?php lang::_e("You can't create menu item because there are no active menus in your theme or your theme doesn't support menus"); ?></p>
	   <?php }?>
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="product_category_thumb_url"><?php lang::_e('Current Image');?></label></th>
	<td>
		<input type="hidden" name="product_category_thumb_url" id="product_category_thumb_url" value="<?php echo $this->category_thumb; ?>"/><br />
		<img id="image" src="<?php echo $this->category_thumb;?>" alt="<?php echo $tag->name;?>" /> <br />
		<p class="description"><?php lang::_e('You can add the image to category')?></p>
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