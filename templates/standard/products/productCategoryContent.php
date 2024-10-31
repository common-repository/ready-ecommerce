<?php if ($this->viewOptions['catalog_view'] == 'grid'): ?>
<style type="text/css">
     .product {
        width:<?php echo ($this->viewOptions['grid_preview_size']+10); ?>px !important;
        height:<?php echo ($this->viewOptions['grid_preview_size']+155+19*$this->viewOptions['short_descr_size']); ?>px;
        float:left;
        margin-right:<?php echo ($this->viewOptions['grid_hor_distance']); ?>px; 
        margin-bottom:<?php echo ($this->viewOptions['grid_vert_distance']); ?>px;
    }
    
     .product .entry-content {
        width:<?php echo ($this->viewOptions['grid_preview_size']+10); ?>px !important;
        float:left;
        margin-right:<?php echo ($this->viewOptions['grid_hor_distance']); ?>px; 
        margin-bottom:<?php echo ($this->viewOptions['grid_vert_distance']); ?>px;
    }
    
    .toe_product_grid_view, .toe_product_grid_view .product_main, .toe_product_grid_view .product_image, .toe_product_grid_view .product_info
    {width:<?php echo ($this->viewOptions['grid_preview_size']+10); ?>px;}
    
    .toe_product_grid_view .product_image {height:<?php echo ($this->viewOptions['grid_preview_size']+10); ?>px;}
    
    .toe_product_grid_view .product_image img {
        max-height:<?php echo ($this->viewOptions['grid_preview_size']); ?>px;
        max-width:<?php echo ($this->viewOptions['grid_preview_size']); ?>px;
    }
    
    <?php if($this->viewOptions['shadow_border']): ?>
     .product .entry-content:hover {
        -webkit-box-shadow: 0px 0px 2px 2px rgba(0, 0, 0, 0.1); 
        box-shadow: 0px 0px 2px 2px rgba(0, 0, 0, 0.1); 
        border:1px solid #cdcdcd;
        margin:-13px 7px 20px -13px !important;
        background:<?php echo $this->viewOptions['hover_item_bg']; ?> !important; position:absolute; z-index:200;
        -webkit-border-radius: 5px; border-radius: 5px;
        padding:12px; 
        z-index:999;
    }
    <?php endif; ?>
     .product .entry-content .product_info .product_excerpt {display:block;}
     .product .entry-content .product_info .product_short {
        height: <?php if ($this->viewOptions['short_descr_size'] > 0) echo (19*$this->viewOptions['short_descr_size']); ?>px;
        overflow:hidden;
    }
</style>
<?php elseif($this->viewOptions['catalog_view'] == 'list'): ?>
<style>
     .product {
        width:100%;
        margin-bottom:<?php echo ($this->viewOptions['list_vert_distance']); ?>px;
    }
    
     .product .entry-content {
        width:100%;
        float:left;
        margin-bottom:<?php echo ($this->viewOptions['list_vert_distance']); ?>px;
    }
    
    .toe_product_list_view .product_main {width:<?php echo ($this->viewOptions['grid_preview_size']+10); ?>px !important;} 
    .toe_product_list_view .product_info
    {width:<?php echo (600 - $this->viewOptions['grid_preview_size']+10); ?>px !important;}
    
    .toe_product_list_view .product_image {width:<?php echo ($this->viewOptions['grid_preview_size']+10); ?>px; height:<?php echo ($this->viewOptions['grid_preview_size']+10); ?>px;}
    
    .toe_product_list_view .product_image img {
        max-height:<?php echo ($this->viewOptions['grid_preview_size']); ?>px;
        max-width:<?php echo ($this->viewOptions['grid_preview_size']); ?>px;
    }
   
     .pink_btn {float:left !important; margin-left:10px;}
    
</style>
<?php endif; ?>

<style type="text/css">
    .toe_product_grid_view .product_info .product_excerpt,
    .toe_product_list_view .product_info .product_excerpt {
        color:<?php echo ($this->viewOptions['short_descr_color']); ?>;
    }
    
    .product_price {
        color:<?php echo ($this->viewOptions['price_color']); ?>;
    }
    
    .toeAddToCartMsg {color:<?php echo ($this->viewOptions['successful_color']); ?>;}
    
    .toe_product_grid_view .product_info h2 a,
    .toe_product_list_view .product_info h2 a {color:<?php echo ($this->viewOptions['title_color']); ?>;}
    
    .toe_product_grid_view .product_image,
    .toe_product_list_view .product_image {border:1px solid <?php echo ($this->viewOptions['image_border_color']); ?>;}
</style>

<form action="" method="post" class="toeAddToCartForm" id="toeAddToCartForm<?php echo $this->post->ID?>" onsubmit="toeAddToCart(this); return false;">
    <div class="product category_product toe_product_<?php echo $this->viewOptions['catalog_view']; ?>_view">
        <div class="product_wrap">
            <?php if ($this->viewOptions['catalog_image']) :?>
            <div class="product_main">
                <!--toeImage-->
                    <div class="product_image">
                        <a href="<?php the_permalink(); ?>" title="<?php echo get_the_title();?>" rel="prettyPhoto[category]">
                            <img src="<?php echo $this->image['big'][0]; ?>" 
                                 alt="<?php echo get_the_title();?>"
                                 class="productPict" />
                        </a>
                    </div>
                <!--/toeImage-->
            </div>
            <?php endif; ?>
			<?php if($this->pData['mark_as_new']->value) { ?>
				<div style="font-weight: bold; color: blue; margin-bottom: -49px;"><?php lang::_e('New')?>!</div>
			<?php }?>
            <div class="product_info" style="float: none;">
                <div class="product_main_info">
                    <div class="product_block_wrapper">
                        <?php if ($this->viewOptions['title']) :?>
                            <!--toetitle-->
                                <h2>
                                    <a href="<?php the_permalink(); ?>" title="<?php lang::_e('View product page')?>">
                                        <?php echo get_the_title()?>
                                    </a>
                                    <?php if(frame::_()->getModule('products')->markAsSale($this->post->ID) == true):?>
                                        <span style="color: red; font-weight: bold; text-decoration: blink;"><?php lang::_e('SALE')?></span>
                                    <?php endif;?>
                                </h2>
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
                            
                        <?php if ($this->viewOptions['price']) :?>
                            <div class="product_price">
                                <span><?php echo $this->priceHtml?></span>
                            </div>
                            <div class="clear"></div>
                        <?php endif;?>
                        
                        <div class="product_to_cart">
                            <?php if ($this->viewOptions['more']) :?>
                            <a href="<?php the_permalink(); ?>" class="grey_btn toeMoreLink"><?php lang::_e('More')?></a>
                            <?php endif;?>
                            
                            <?php if ($this->viewOptions['add_to_cart']) :?>
                            <!--toeadd_to_cart-->
                            <div class="add_to_cart">
                               <?php echo $this->actionButtons?>
                            </div>
                            <!--/toeadd_to_cart-->
                            <?php endif;?>
                        </div>
                        
                        <div class="clr"></div>
                            
                    </div>
                </div>
                <div style="clear: both;"></div>
                <div class="ratingBox"><?php echo $this->ratingBox?></div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</form>