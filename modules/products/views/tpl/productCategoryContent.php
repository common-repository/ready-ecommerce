<form action="" method="post" class="toeAddToCartForm" id="toeAddToCartForm<?php echo $this->post->ID?>" onsubmit="toeAddToCart(this); return false;">
    <div class="category_product">
        <div class="product_wrap">
            <div class="product_main">
                <!--toeImage-->
                    <div class="product_image">
                        <a href="<?php the_permalink(); ?>" title="<?php echo get_the_title();?>" rel="prettyPhoto[category]">
                            <img src="<?php echo $this->image['thumb'][0]; ?>" 
                                 width="<?php echo $this->image['thumb'][1]; ?>"
                                 height="<?php echo $this->image['thumb'][2]; ?>"
                                 alt="<?php echo get_the_title();?>"
                                 class="productPict" />
                        </a>
                    </div>
                <!--/toeImage-->
            </div>
            <div class="product_info" style="float: none;">
                <div class="product_main_info">
                    <div class="product_block_wrapper">
                        <?php if ($this->viewOptions['title']) :?>
                            <!--toetitle-->
                                 <h1><a href="<?php the_permalink(); ?>" title="<?php lang::_e('View product page')?>"><?php echo get_the_title()?></a></h1>
                            <!--/toetitle-->
                        <?php endif;?>
                        <?php if ($this->viewOptions['short_descr']) :?>
                            <!--toeshort_description-->
                                <div class="product_excerpt">
                                    <div class="product_block_wrapper">
                                        <div class="product_short">
                                            <?php echo get_the_excerpt(); ?>
                                        </div>
                                    </div>  
                                </div>
                            <!--/toeshort_description-->
                        <?php endif;?>
                        <!--toeadd_to_cart-->
                            <?php if ($this->viewOptions['price']) :?>
                                <div class="product_price">
                                    <span><?php echo $this->priceHtml?></span>
                                </div>
                            <?php endif;?>
                            <?php if ($this->viewOptions['add_to_cart']) :?>
                                <div class="product_to_cart">
                                      <?php echo $this->actionButtons?>
                                </div>
                            <?php endif;?>
                            <div class="clr"></div>
                        <!--/toeadd_to_cart-->
                    </div>
                </div>
                <div><?php echo $this->ratingBox?></div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</form>