<div class="toeSingleProductShell">
	<form action="" method="post" class="toeAddToCartForm" id="toeAddToCartForm<?php echo $this->post->ID?>" onsubmit="toeAddToCart(this,'<?php lang::_e('Product Added!'); ?>',false); return false;">
		<div id="single_product">
			 <?php if(isset($this->variationsSelect) && !empty($this->variationsSelect)) 
				echo $this->variationsSelect;?>
			<?php if ($this->viewOptions['title']) :?>
			<div class="product_title">
				<h2><?php echo get_the_title();?>
					<?php if(frame::_()->getModule('products')->markAsSale($this->post->ID) == true):?>
						<span style="color: red; font-weight: bold; text-decoration: blink;"><?php lang::_e('SALE')?></span>
					<?php endif;?>
				</h2>
			</div>
			<?php endif;?>
			<?php if($this->pData['mark_as_new']->value) { ?>
				<div style="font-weight: bold; color: blue; margin-bottom: -49px;"><?php lang::_e('New')?>!</div>
			<?php }?>
			<div class="product_wrap">
				<?php if ($this->viewOptions['full_image'] or $this->viewOptions['preview_images']) :?>
				<div class="product_main <?php if($this->viewOptions['gallery_position'] == 'left') {echo 'gallery_to_left';} elseif ($this->viewOptions['gallery_position'] == 'right') {echo 'gallery_to_right';} ?>">
					<?php if ($this->viewOptions['full_image']) :?>
						<!--toeImage-->
						<div class="full_image_wrapper">
							<div class="product_full_image">
								<a href="<?php echo $this->image['big'][0]?>" title="<?php lang::_e('Click to enlarge');?>" rel="lightbox[product]">
									<img src="<?php echo $this->image['big'][0]; ?>" 
										 alt="<?php echo get_the_title();?>" class="back-img" />
								</a>
							</div>
						</div>
						<!--/toeImage-->
					<?php endif;?>
					<?php if ($this->viewOptions['preview_images']) :?>
						<!--toeImages-->
						<!-- All product images here -->
						<?php if (!empty($this->images)):?>
						<div class="product_slider">
							<?php
								$image_count = count($this->images);
								if ($image_count > 1):
									if ($image_count > 3):
							?>
							<div class="slider_prev"></div>
							<div class="slider_next"></div>
									<?php endif; ?>
							<div class="slider-line">
								<ul class="content-slider">
								<?php foreach($this->images as $image) :?>
									<li>
										<div class="slider_thumb_wrapper">
											<a href="<?php echo $image['big'][0]?>" class="product-thumb-image" alt="<?php echo $image['big'][0]; ?>">
											<img src="<?php echo $image['thumb'][0]; ?>" 
												 width="<?php echo $image['thumb'][1]; ?>"
												 height="<?php echo $image['thumb'][2]; ?>"
												 alt="<?php echo get_the_title();?>" />
											</a>
										</div>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>
							<div id="all-prod-images">
								<?php foreach($this->images as $image) :?>
									<?php
										$imgsrc = $image['big'][0];
									?>
									<a href="<?php echo $imgsrc; ?>" rel="lightbox[product]"></a>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
						</div>
						<?php endif;?>
						<!--/toeImages-->
					<?php endif;?>
				</div>
				<?php endif;?>

				<div class="product_info <?php if (!$this->viewOptions['full_image'] and !$this->viewOptions['preview_images']) echo 'full_width'; ?>">
					<div id="product_main_info">
						<div class="product_block_wrapper">
							<?php if(!$this->viewOptions['show_twitter'] and !$this->viewOptions['show_gplus'] and !$this->viewOptions['show_facebook']): ?>
							<?php else: ?>
							<div class="social">
								<?php if($this->viewOptions['show_twitter']): ?>
								<div class="twitter"><a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
								</div>
								<?php endif; ?>

								<?php if($this->viewOptions['show_gplus']): ?>
								<div class="gplus">
									<div class="g-plusone" data-size="medium"></div>
									<script type="text/javascript">
									  (function() {
										var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
										po.src = 'https://apis.google.com/js/plusone.js';
										var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
									  })();
									</script>
								</div>
								<?php endif; ?>

								<?php if($this->viewOptions['show_facebook']): ?>
								<?php  
									$lang_arr = array ("ca_ES ", "cs_CZ", "da_DK", "eu_ES", "en_UD", "en_US", "es_CL", "es_ES", "es_VE", "fi_FI", "gl_ES", "cy_GB", "de_DE", "ck_US", "es_CO", "fb_FI", "hu_HU", "ja_JP", "nb_NO", "nl_NL", "pt_BR", "ro_RO", "en_PI", "es_LA", "fr_FR", "ko_KR", "pl_PL", "ru_RU", "sl_SI", "th_TH", "ku_TR", "zh_HK", "fb_LT", "es_MX", "it_IT", "pt_PT", "sv_SE", "zh_CN", "af_ZA", "hy_AM", "be_BY", "bs_BA ", "hr_HR", "en_GB", "nn_NO", "sk_SK", "zh_TW", "az_AZ", "bg_BG", "eo_EO", "fo_FO", "ka_GE", "gu_IN", "is_IS", "ga_IE", "tr_TR", "sq_AL", "nl_BE", "fr_CA", "hi_IN", "jv_ID", "kk_KZ", "lv_LV", "lt_LT", "mg_MG", "mt_MT", "bn_IN", "et_EE", "id_ID", "la_VA", "mk_MK ", "mr_IN", "ne_NP ", "rm_CH", "sr_RS", "sw_KE", "ta_IN", "el_GR", "kn_IN", "ms_MY", "pa_IN", "so_SO", "tt_RU", "ml_IN", "uz_UZ", "xh_ZA", "km_KH", "ar_AR", "li_NL", "mn_MN", "tl_PH", "uk_UA", "zu_ZA", "he_IL", "fa_IR", "yi_DE", "qu_PE", "se_NO", "tl_ST", "sa_IN", "te_IN", "tg_TJ", "sy_SY", "ay_BO", "vi_VN", "ur_PK", "gn_PY", "ps_AF");
									$curr_lang = S_WPLANG;
									if (in_array($curr_lang, $lang_arr) ) { $fb_lang = $curr_lang; }
									else {$fb_lang = "en_US";}
								?>
								<div class="facebook">
									<div id="fb-root"></div>
									<script>(function(d, s, id) {
									  var js, fjs = d.getElementsByTagName(s)[0];
									  if (d.getElementById(id)) return;
									  js = d.createElement(s); js.id = id;
									  js.src = "//connect.facebook.net/<?php echo $fb_lang; ?>/all.js#xfbml=1&appId=374511692596628";
									  fjs.parentNode.insertBefore(js, fjs);
									}(document, 'script', 'facebook-jssdk'));</script>
									<div class="fb-like" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
								</div>
								<?php endif; ?>
							</div>
							<?php endif; ?>

							<div class="clear"></div>
							<?php if ($this->viewOptions['sku']) :?>
								<!--toesku-->
									<div id="product_sku">
										<span><?php lang::_e('Item #:'); echo $this->pData['sku']->value;?></span>
									</div>
								<!--/toesku-->
							<?php endif;?>
							<div class="product_rating"><?php echo $this->ratingBox?></div>
							<div class="clear"></div>
							<?php if ($this->viewOptions['short_descr']) :?>
								<!--toeshort_description-->
									<div id="product_excerpt">
										<div class="product_block_wrapper">
											<?php remove_filter('excerpt_more', 'twentyten_auto_excerpt_more'); echo get_the_excerpt(); ?>
										</div>  
									</div>
								<!--/toeshort_description-->
							<?php endif; ?>
							<div class="productBC">
								<p>
									<?php if(get_the_term_list($this->post->ID, frame::_()->getModule('products')->getConstant('CATEGORIES')) !== false) {?>
										<strong><?php lang::_e('Product category:'); ?></strong> <?php the_terms( $this->post->ID, frame::_()->getModule('products')->getConstant('CATEGORIES') ); ?><br />
									<?php }?>
									<?php if(get_the_term_list($this->post->ID, frame::_()->getModule('products')->getConstant('BRANDS')) !== false) {?>
										<strong><?php lang::_e('Brands:'); ?></strong> <?php the_terms( $this->post->ID, frame::_()->getModule('products')->getConstant('BRANDS') ); ?>
									<?php }?>
								</p>
							</div>

							<?php if(!empty($this->extraFields) and $this->viewOptions['show_extra_fields']) {?>
							<!--show_extra_fields-->
							<div class="product_extra_fields">
								<table>
									<?php foreach($this->extraFields as $d) { ?>
									<tr>
										<td><?php lang::_e($d->label)?></td>
										<td><?php $d->display()?></td>
									</tr>
									<?php }?>
								</table>
							</div>
							<!--/show_extra_fields-->
							<?php }?>

							<?php if ($this->viewOptions['price']) :?>
								<!--toeprice-->
								<div id="product_price" class="product_price">
									<span><?php echo $this->priceHtml?></span>
								</div>
								<!--/toeprice-->
							<?php endif; ?>

						</div>
						<?php if ($this->viewOptions['add_to_cart']) :?>
							<!--toeadd_to_cart-->
								<div class="product_to_cart">
									  <?php echo $this->actionButtons?>
								</div>
							<!--/toeadd_to_cart-->
						<?php endif; ?>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			<?php if ($this->viewOptions['full_descr']) :?>
				<!--toefull_description-->
					<div id="product_description">
						<div class="product_block_wrapper">
							<?php echo $this->fullDescr; ?>
						</div>  
					</div>
				<!--/toefull_description-->
			<?php endif; ?>
		</div>
	</form>
</div>