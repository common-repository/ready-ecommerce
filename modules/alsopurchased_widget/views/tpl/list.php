<div class="toeWidget">
    <?php if(!empty($this->params['title'])) { ?>
        <div class="toeWidgetTitle"><?php lang::_e($this->params['title'])?></div>
    <?php }?>
    <div id="toeAlsoPurchasedWidgetContent<?php echo $this->uniqID?>">
        <div class="toeAlsoPurchased">
            <?php if (!empty($this->products)):
                    foreach ($this->products as $product) :?>
            <div class="toeAlsoPurchasedProduct">
                <div class="toeAlsoPurchasedProductWrapper">
                    <div class="product_image">
                        <a href="<?php echo $product['guid']?>" title="<?php echo $product['title'];?>">
                            <img class="productPict" src="<?php echo $product['image']['thumb'][0]; ?>" 
                                 width="<?php echo $product['image']['thumb'][1]; ?>"
                                 height="<?php echo $product['image']['thumb'][2]; ?>"
                                 alt="<?php echo $product['title'];?>" />
                        </a>
                    </div>
                    <h3 class="product_title">
                        <a href="<?php echo $product['guid']?>"><?php echo $product['title'];?></a>
                    </h3>
                    <div class="product_short">
                        <?php echo $product['description'];?>
                    </div>
                     <div class="product_short">
                        <?php echo $product['productContentAfter'];?>
                    </div>
                    <div class="buy_product">
                        <?php if ($this->params['show_price']) :?>
                        <div class="product_price"><?php echo $product['price'];?></div>
                        <?php endif;?>
                        <?php if ($this->params['show_add_to_cart']) :?>
                        <div class="product_to_cart">
                            <?php echo $product['actionButtons'];?>
                        </div>
                        <?php endif;?>
                        <div class="clr"></div>
                    </div>
                    <?php if(!empty($product['ratingBox'])) { ?>
                    <div>
                        <?php echo $product['ratingBox']?>
                    </div>
                    <?php }?> 
                </div>
            </div>
            <?php   endforeach;
                  endif;  ?>
            <br clear="all" />
        </div>
    </div>
</div>