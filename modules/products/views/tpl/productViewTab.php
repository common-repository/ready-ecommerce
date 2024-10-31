<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	jQuery('#single_product_view_form').submit(function(){
		jQuery(this).sendForm({
			msgElID: 'mod_msg_spv',
			url: '<?php echo get_admin_url(0, 'options.php')?>',
			dataType: 'text',
			onSuccess: function(res) {
				jQuery('#mod_msg_spv').html(toeLang('Done'));
			}
		});
		return false;
	});
	jQuery('#category_product_view_form').submit(function(){
		jQuery(this).sendForm({
			msgElID: 'mod_msg_cpv',
			url: '<?php echo get_admin_url(0, 'options.php')?>',
			dataType: 'text',
			onSuccess: function(res) {
				jQuery('#mod_msg_cpv').html(toeLang('Done'));
			}
		});
		return false;
	});
});
// -->
</script>
<div id="single_product_view" class="tab_form">
    <h1><?php lang::_e('Single Product View')?></h1>
    <form method="post" action="options.php" id="single_product_view_form">
        <?php settings_fields( 're_product_single' ); ?>
        <?php do_settings_sections(__FILE__); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><h3><?php lang::_e('Product info settings'); ?></h3></th>
                <td></td>
                <th scope="row"><h3><?php lang::_e('Product gallery settings'); ?></h3></th>
                <td></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Product Title'); ?></th>
                <td>
                    <input id="re_product_single[title]" name="re_product_single[title]" type="checkbox" value="1" <?php checked( '1', $this->options['title'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product title or not'); ?>"></a>
                </td>
                
                <th scope="row"><?php lang::_e('Show Full Image'); ?></th>
                <td>
                    <input id="re_product_single[full_image]" name="re_product_single[full_image]" type="checkbox" value="1" <?php checked( '1', $this->options['full_image'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'If not checked the full image will not be on the page'); ?>"></a>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Short Description'); ?></th>
                <td>
                        <input id="re_product_single[short_descr]" name="re_product_single[short_descr]" type="checkbox" value="1" <?php checked( '1', $this->options['short_descr'] ); ?> />
                        <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product short description or not'); ?>"></a>
                </td>
                
                <th scope="row"><?php lang::_e('Show Image Previews'); ?></th>
                <td>
                    <input id="re_product_single[preview_images]" name="re_product_single[preview_images]" type="checkbox" value="1" <?php checked( '1', $this->options['preview_images'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'If not checked the image previews will not be on the page'); ?>"></a>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Full Description'); ?></th>
                <td>
                    <input id="re_product_single[full_descr]" name="re_product_single[full_descr]" type="checkbox" value="1" <?php checked( '1', $this->options['full_descr'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product full description or not'); ?>"></a>
                </td>
                
                <th scope="row"><?php lang::_e('Product Gallery Position'); ?></th>
                <td>
                    <select id="re_product_single[gallery_position]" name="re_product_single[gallery_position]">
                        <option value="left"<?php if ($this->options['gallery_position'] == 'left') echo ' selected="selected"';?>><?php lang::_e('Left')?></option>
                        <option value="right"<?php if ($this->options['gallery_position'] == 'right') echo ' selected="selected"';?>><?php lang::_e('Right')?></option>
                    </select>
                    <a class="toeOptTip" tip="<?php lang::_e( 'Choose product gallery position'); ?>"></a>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Product SKU'); ?></th>
                <td>
                    <input id="re_product_single[sku]" name="re_product_single[sku]" type="checkbox" value="1" <?php checked( '1', $this->options['sku'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product sku or not (Example: Item: #1184)'); ?>"></a>
                </td>
                <th scope="row"></th>
                <td></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Product Details'); ?></th>
                <td>
                    <input id="re_product_single[details]" name="re_product_single[details]" type="checkbox" value="1" <?php checked( '1', $this->options['details'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product details or not'); ?>"></a>
                </td>
                <th scope="row"></th>
                <td></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Product Quantity'); ?></th>
                <td>
                    <input id="re_product_single[quantity]" name="re_product_single[quantity]" type="checkbox" value="1" <?php checked( '1', $this->options['quantity'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product quantity or not'); ?>"></a>
                </td>
                <th scope="row"></th>
                <td></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Product Price'); ?></th>
                <td>
                    <input id="re_product_single[price]" name="re_product_single[price]" type="checkbox" value="1" <?php checked( '1', $this->options['price'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product price or not'); ?>"></a>
                </td>
                <th scope="row"><strong><?php lang::_e('Social Share settings'); ?></strong></th>
                <td></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Product Extra fields'); ?></th>
                <td>
                    <input id="re_product_single[show_extra_fields]" name="re_product_single[show_extra_fields]" type="checkbox" value="1" <?php checked( '1', $this->options['show_extra_fields'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product extra fields or not'); ?>"></a>
                </td>
                <th scope="row"><?php lang::_e('Show Twitter Share button'); ?></th>
                <td>
                    <input id="re_product_single[show_twitter]" name="re_product_single[show_twitter]" type="checkbox" value="1" <?php checked( '1', $this->options['show_twitter'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show twitter share button or not'); ?>"></a>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Show Add To Cart Buttons'); ?></th>
                <td>
                    <input id="re_product_single[add_to_cart]" name="re_product_single[add_to_cart]" type="checkbox" value="1" <?php checked( '1', $this->options['add_to_cart'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show add to cart buttons or not'); ?>"></a>
                </td>
                <th scope="row"><?php lang::_e('Show Google+ Share button'); ?></th>
                <td>
                    <input id="re_product_single[show_gplus]" name="re_product_single[show_gplus]" type="checkbox" value="1" <?php checked( '1', $this->options['show_gplus'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show Google+ button or not'); ?>"></a>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php lang::_e('Add To Cart Button Text Color'); ?></th>
                <td>
					<?php echo html::colorpicker('re_product_single[add_to_cart_text]', array('value' => $this->options['add_to_cart_text']))?>
                    <a class="toeOptTip" tip="<?php lang::_e( 'Add to cart button Text Color'); ?>"></a>
                </td>
                <th scope="row"><?php lang::_e('Show Facebook Share button'); ?></th>
                <td>
                    <input id="re_product_single[show_facebook]" name="re_product_single[show_facebook]" type="checkbox" value="1" <?php checked( '1', $this->options['show_facebook'] ); ?> />
                    <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show Facebook button or not'); ?>"></a>
                </td>
            </tr>
        </table>
        <input type="submit" value="<?php lang::_e('Save')?>" class="button button-primary" />
		<input type="hidden" name="reqType" value="ajax" />
        <span id="mod_msg_spv" class="mod_msg"></span>
    </form>
</div>

<div id="category_product_view" class="tab_form">
    <h1><?php lang::_e('Category Product View')?></h1>
        <form method="post" action="options.php" id="category_product_view_form">
            <?php settings_fields( 're_product_category' ); ?>
            <?php do_settings_sections(__FILE__); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Catalog Items view'); ?></th>
                        <td>
                            <select id="re_product_category[catalog_view]" name="re_product_category[catalog_view]">
                                <option value="grid" <?php if ($this->category_options['catalog_view'] == 'grid') echo ' selected="selected"';?>><?php lang::_e('Grid View')?></option>
                                <option value="list" <?php if ($this->category_options['catalog_view'] == 'list') echo ' selected="selected"';?>><?php lang::_e('List View')?></option>
                            </select>
                            <a class="toeOptTip" tip="<?php lang::_e( 'Choose catalog view mode: Grid or List.'); ?>"></a>
                        </td>
                        <th scope="row"></th>
                        <td></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><h3><?php lang::_e('Product Grid View Settings'); ?></h3></th>
                        <td></td>
                        <th scope="row"><h3><?php lang::_e('Product List View Settings'); ?></h3></th>
                        <td></td>
                    </tr>
                
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Preview size (grid)'); ?></th>
                        <td>
                            <input id="re_product_category[grid_preview_size]" name="re_product_category[grid_preview_size]" type="text" value="<?php echo $this->category_options['grid_preview_size'] ; ?>" />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Size of preview image'); ?>"></a>
                        </td>
                        <th scope="row"><?php lang::_e('Preview size (list)'); ?></th>
                        <td>
                            <input id="re_product_category[list_preview_size]" name="re_product_category[list_preview_size]" type="text" value="<?php echo $this->category_options['list_preview_size'] ; ?>" />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Size of preview image'); ?>"></a>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Vertical distance (grid)'); ?></th>
                        <td>
                            <input id="re_product_category[grid_vert_distance]" name="re_product_category[grid_vert_distance]" type="text" value="<?php echo $this->category_options['grid_vert_distance'] ; ?>" />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Vertical distance of item block'); ?>"></a>
                        </td>
                        <th scope="row"><?php lang::_e('Vertical distance (list)'); ?></th>
                        <td>
                            <input id="re_product_category[list_vert_distance]" name="re_product_category[list_vert_distance]" type="text" value="<?php echo $this->category_options['list_vert_distance'] ; ?>" />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Vertical distance of item block'); ?>"></a>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Horizontal distance (grid)'); ?></th>
                        <td>
                            <input id="re_product_category[grid_hor_distance]" name="re_product_category[grid_hor_distance]" type="text" value="<?php echo $this->category_options['grid_hor_distance'] ; ?>" />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Horizontal distance for item block'); ?>"></a>
                        </td>
                        <th scope="row"></th>
                        <td></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Shadow border on product item hover'); ?></th>
                        <td>
                            <input id="re_product_category[shadow_border]" name="re_product_category[shadow_border]" type="checkbox" value="1" <?php checked( '1', $this->category_options['shadow_border'] ); ?> />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Show shadow border on product item hover or not'); ?>"></a>
                        </td>
                        <th scope="row"></th>
                        <td></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Short description size (in lines)'); ?></th>
                        <td>
                            <input id="re_product_category[short_descr_size]" name="re_product_category[short_descr_size]" type="text" value="<?php echo $this->category_options['short_descr_size'] ; ?>" />
                            <a class="toeOptTip" tip="<?php lang::_e( 'How many lines of short description will shown'); ?>"></a>
                        </td>
                        <th scope="row"></th>
                        <td></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><h3><?php lang::_e('Catalog Items settings'); ?></h3></th>
                        <td></td>
                        <th scope="row"><h3><?php lang::_e('Catalog Items color'); ?></h3></th>
                        <td></td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Show Product Image'); ?></th>
                        <td>
                            <input id="re_product_category[catalog_image]" name="re_product_category[catalog_image]" type="checkbox" value="1" <?php checked( '1', $this->category_options['catalog_image'] ); ?> />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product title or not'); ?>"></a>
                        </td>
                        <th scope="row"><?php lang::_e('Background when Hovering on product item (grid)'); ?></th>
                        <td>
							<?php echo html::colorpicker('re_product_category[hover_item_bg]', array('value' => $this->category_options['hover_item_bg']))?>
                            <a class="toeOptTip" tip="<?php lang::_e( 'Background when Hovering on product item'); ?>"></a>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Show Product Title'); ?></th>
                        <td>
                            <input id="re_product_category[title]" name="re_product_category[title]" type="checkbox" value="1" <?php checked( '1', $this->category_options['title'] ); ?> />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product title or not'); ?>"></a>
                        </td>
                        <th scope="row"><?php lang::_e('Short Description text color'); ?></th>
                        <td>
							<?php echo html::colorpicker('re_product_category[short_descr_color]', array('value' => $this->category_options['short_descr_color']))?>
                            <a class="toeOptTip" tip="<?php lang::_e( 'Short Description text color'); ?>"></a>
                        </td>
                    </tr>
					
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Show Product Price'); ?></th>
                        <td>
                            <input id="re_product_category[price]" name="re_product_category[price]" type="checkbox" value="1" <?php checked( '1', $this->category_options['price'] ); ?> />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product price or not'); ?>"></a>
                        </td>
                        <th scope="row"><?php lang::_e('Price color'); ?></th>
                        <td>
							<?php echo html::colorpicker('re_product_category[price_color]', array('value' => $this->category_options['price_color']))?>
                            <a class="toeOptTip" tip="<?php lang::_e( 'Price color'); ?>"></a>
                        </td>
                    </tr>
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Show Read More'); ?></th>
                        <td>
                            <input id="re_product_category[more]" name="re_product_category[more]" type="checkbox" value="1" <?php checked( '1', $this->category_options['more'] ); ?> />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show read more link or not'); ?>"></a>
                        </td>
                        <th scope="row"><?php lang::_e('Product image border color'); ?></th>
                        <td>
							<?php echo html::colorpicker('re_product_category[image_border_color]', array('value' => $this->category_options['image_border_color']))?>
                            <a class="toeOptTip" tip="<?php lang::_e( 'Product image border color'); ?>"></a>
                        </td>
                    </tr> 
                    
                    <tr valign="top">
                        <th scope="row"><?php lang::_e('Show Add to Cart'); ?></th>
                        <td>
                            <input id="re_product_category[add_to_cart]" name="re_product_category[add_to_cart]" type="checkbox" value="1" <?php checked( '1', $this->category_options['add_to_cart'] ); ?> />
                            <a class="toeOptTip" tip="<?php lang::_e( 'Whether to show add to cart or not'); ?>"></a>
                        </td>
                        <th scope="row"><?php lang::_e('Product title color'); ?></th>
                        <td>
							<?php echo html::colorpicker('re_product_category[title_color]', array('value' => $this->category_options['title_color']))?>
                            <a class="toeOptTip" tip="<?php lang::_e( 'Product title color'); ?>"></a>
                        </td>
                    </tr>
					<tr valign="top">
						<th scope="row"><?php lang::_e('Show Short Description'); ?></th>
						<td>
								<input id="re_product_category[short_descr]" name="re_product_category[short_descr]" type="checkbox" value="1" <?php checked( '1', $this->category_options['short_descr'] ); ?> />
								<a class="toeOptTip" tip="<?php lang::_e( 'Whether to show product short description or not'); ?>"></a>
						</td>
						<th scope="row"></th>
						<td></td>
					</tr>
                                        
                 </table>
            <input type="submit" value="<?php lang::_e('Save')?>" class="button button-primary" />
			<input type="hidden" name="reqType" value="ajax" />
            <span id="mod_msg_cpv" class="mod_msg"></span>
        </form>
</div>