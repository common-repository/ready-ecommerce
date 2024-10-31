<div class="toeSingleProductShell">
	<form action="" method="post" class="toeAddToCartForm" id="toeAddToCartForm<?php echo $this->post->ID?>" onsubmit="toeAddToCart(this); return false;">
		<div id="single_product">
			<?php if(isset($this->variationsSelect) && !empty($this->variationsSelect)) 
				echo $this->variationsSelect;?>

			<?php if ($this->viewOptions['title']) :?>
			<div class="product_title">
				<h2><?php echo get_the_title();?></h2>
			</div>
			<?php endif;?>
			<div class="product_wrap">
				<div class="product_main">
					<?php if ($this->viewOptions['full_image']) :?>
						<!--toeImage-->
						<div class="product_full_image">
							<a href="<?php echo $this->image['big'][0]?>" title="<?php lang::_e('Click to enlarge');?>" rel="prettyPhoto">
								<img src="<?php echo $this->image['thumb'][0]; ?>" 
									 width="<?php echo $this->image['thumb'][1]; ?>"
									 height="<?php echo $this->image['thumb'][2]; ?>"
									 alt="<?php echo get_the_title();?>" />
							</a>
						</div>
						<!--/toeImage-->
					<?php endif;?>
					<?php if ($this->viewOptions['preview_images']) :?>
						<!--toeImages-->
						<!-- All product images here -->
						<?php if (!empty($this->images)):?>
						<div class="product_slider">
							<ul>
							<?php foreach($this->images as $image) :?>
								<li>
									<a href="<?php echo $image['big'][0]?>" title="<?php lang::_e('Click to enlarge');?>" rel="lightbox">
									<img src="<?php echo $image['thumb'][0]; ?>" 
										 width="<?php echo $image['thumb'][1]; ?>"
										 height="<?php echo $image['thumb'][2]; ?>"
										 alt="<?php echo get_the_title();?>" />
									</a>
								</li>
							<?php endforeach; ?>
							</ul>
						</div>
						<?php endif;?>
						<!--/toeImages-->
					<?php endif;?>
				</div>
				<div class="product_info">
					<div id="product_main_info">
						<div class="product_block_wrapper">
							<?php if ($this->viewOptions['title']) :?>
								<!--toetitle-->
									<h1><?php echo get_the_title()?></h1>
								<!--/toetitle-->
							<?php endif;?>
							<?php if ($this->viewOptions['price']) :?>
								<!--toeprice-->
								<div id="product_price" class="product_price">
									<span><?php echo $this->priceHtml?></span>
								</div>
								<!--/toeprice-->
							<?php endif; ?>
							<?php if ($this->viewOptions['sku']) :?>
								<!--toesku-->
									<div id="product_sku">
										<span><?php echo $this->pData['sku']->label. ' : '. $this->pData['sku']->value;?></span>
									</div>
								<!--/toesku-->
							<?php endif;?>
							<?php if ($this->viewOptions['quantity']) :?>
								<!--toequantity-->
									<div id="product_quantity">
										<span><?php echo $this->pData['quantity']->label. ' : '. $this->pData['quantity']->value;?></span>
									</div>
								<!--/toequantity-->
							<?php endif; ?>
						</div>
					</div>
					<div class="product_tabs">
						<ul>
							<!--toeshort_description-->
							<?php if ($this->viewOptions['short_descr']) :?>
								<li><a href="#product_excerpt"><?php lang::_e('Short Description')?></a></li>
							<?php endif; ?>
							<!--/toeshort_description-->
							<!--toefull_description-->
							<?php if ($this->viewOptions['full_descr']) :?>
								<li><a href="#product_description"><?php lang::_e('Full Description')?></a></li>
							<?php endif;?>
							<!--/toefull_description-->
							<!--toeproperties-->
							<?php if ($this->viewOptions['details']) :?>
								<li><a href="#product_properties"><?php lang::_e('Details')?></a></li>
							<?php endif;?>
							<!--/toeproperties-->
							<?php if(!empty($this->additionalTabs)) { ?>
								<?php foreach($this->additionalTabs as $id => $tab) { ?>
								<li><a href="#toeProductAdditionalTab<?php echo $id?>"><?php echo $tab['label']?></a></li>
								<?php }?>
							<?php }?>
						</ul>
						<?php if ($this->viewOptions['short_descr']) :?>
							<!--toeshort_description-->
								<div id="product_excerpt">
									<div class="product_block_wrapper">
										<?php echo get_the_excerpt(); ?>
									</div>  
								</div>
							<!--/toeshort_description-->
						<?php endif; ?>
						<?php if ($this->viewOptions['full_descr']) :?>
							<!--toefull_description-->
								<div id="product_description">
									<div class="product_block_wrapper">
										<?php echo $this->fullDescr; ?>
									</div>  
								</div>
							<!--/toefull_description-->
						<?php endif; ?>
						<?php if ($this->viewOptions['details']) :?>
							<!--toeproperties-->
								<div id="product_properties">
									<div class="product_block_wrapper">
										<table width="100%">
											<?php foreach($this->pData as $d) {
												if (in_array($d->name, array('cost','price','sku','featured','views')) || (isset($d->hide) && $d->hide)) continue;
												$d->valToType();
												$value = $d->getValue();
												if(empty($value)) continue;
												?>
											<tr>
												<td><?php lang::_e($d->label)?></td>
												<td>
													<?php 
														echo $d->displayValue();
														$showUnits = '';
														if(in_array($d->name, array('weight'))) {
															$showUnits = frame::_()->getModule('options')->get('weight_units');
														} elseif(in_array($d->name, array('width', 'height', 'length'))) {
															$showUnits = frame::_()->getModule('options')->get('size_units');
														}
														if(!empty($showUnits)) {
															echo ' ('. $showUnits. ')';
														}
													?>
												</td>
											</tr>
											<?php }?>
											<?php foreach ($this->pExtra as $d) {?>
											<tr>
												<td><?php lang::_e($d->label)?></td>
												<td> <?php echo $d->displayValue();?></td>
											</tr>
											<?php }?>
										</table>
								   </div>
							   </div>
							<!--/toeproperties-->
						<?php endif; ?>
						<?php if(!empty($this->additionalTabs)) { ?>
							<?php foreach($this->additionalTabs as $id => $tab) { ?>
							<div id="toeProductAdditionalTab<?php echo $id?>"><?php echo $tab['content']?></div>
							<?php }?>
						<?php }?>
				   </div>
					<?php if ($this->viewOptions['add_to_cart']) :?>
						<!--toeadd_to_cart-->
							<div class="product_to_cart">
								  <?php echo $this->actionButtons?>
							</div>
						<!--/toeadd_to_cart-->
					<?php endif; ?>
					<div><?php echo $this->ratingBox?></div>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
	</form>
</div>