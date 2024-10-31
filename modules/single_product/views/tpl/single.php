<div class="toeWidget">
    <?php if(!empty($this->params['title'])) { ?>
        <div class="toeWidgetTitle">
        	<h2><?php lang::_e($this->params['title'])?></h2>
        </div>
    <?php }?>
    <div id="toeSPWidgetContent<?php echo $this->uniqID?>">
        <div id="single_product_block">
            <?php if (!empty($this->product)):?>
            <div class="single_product">
                <div class="single_product_wrapper">
                    <div class="single_product_image">
                        <?php switch ($this->params['image_view']) { 
                              case 0:
                            ?>
                            <a href="<?php echo $this->product['image']['big'][0].'?type=image';?>" title="<?php lang::_e('Click to enlarge');?>" rel="prettyPhoto">
                                <img src="<?php echo $this->product['image']['thumb'][0]; ?>" 
                                     width="<?php echo $this->product['image']['thumb'][1]; ?>"
                                     height="<?php echo $this->product['image']['thumb'][2]; ?>"
                                     alt="<?php echo $this->product['title'];?>" />
                            </a>
                        <?php break; 
                              case 1:  
                        ?>
                            <a href="<?php echo $this->product['guid'];?>">
                                <img src="<?php echo $this->product['image']['thumb'][0]; ?>" 
                                     width="<?php echo $this->product['image']['thumb'][1]; ?>"
                                     height="<?php echo $this->product['image']['thumb'][2]; ?>"
                                     alt="<?php echo $this->product['title'];?>" />
                            </a>
                        <?php break;
                            case 2: ?>
                            <div class="product_image_main">
                                <a href="<?php echo $this->product['image']['big'][0].'?type=image';?>" title="<?php lang::_e('Click to enlarge');?>" rel="prettyPhoto">
                                    <img src="<?php echo $this->product['image']['thumb'][0]; ?>" 
                                         width="<?php echo $this->product['image']['thumb'][1]; ?>"
                                         height="<?php echo $this->product['image']['thumb'][2]; ?>"
                                         alt="<?php echo $this->product['title'];?>" />
                                </a>
                            </div>
                            <!-- All product images here -->
                            <?php if (!empty($this->product['images'])):?>
                            <div class="product_slider">
                                <ul>
                                <?php foreach($this->product['images'] as $image) :?>
                                   <a href="<?php echo $image['big'][0].'?type=image';?>" title="<?php lang::_e('Click to enlarge');?>" rel="prettyPhoto">
                                    <img src="<?php echo $image['thumb'][0]; ?>" 
                                         width="<?php echo $image['thumb'][1]; ?>"
                                         height="<?php echo $image['thumb'][2]; ?>"
                                         alt="<?php echo $this->product['title'];?>" />
                                    </a>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif;?>
                        <?php break;
                              case 3:
                        ?>
                            <a href="<?php echo $this->product['guid'];?>">
                                <img src="<?php echo $this->product['image']['thumb'][0]; ?>" 
                                     width="<?php echo $this->imgWidth; ?>"
                                     alt="<?php echo $this->product['title'];?>" />
                            </a>
                        <?php break;
                              default:
                              break;
                        } ?>
                    </div>
                    <div class="single_product_info">
                        <?php if ($this->params['show_title']) :?>
                            <h3 class="product_title">
                                <a href="<?php echo $this->product['guid']?>"><?php echo $this->product['title'];?></a>
                            </h3>
                        <?php endif;?>
                        <?php if ($this->params['show_description']) :?>
                            <div class="product_short_description">
                                <?php echo $this->product['description'];?>
                            </div>
                        <?php endif;?>
                        <?php if ($this->params['show_price']) :?>
                            <p class="product_price"><?php echo $this->product['price'];?></p>
                        <?php endif;?>
                        <?php if ($this->params['show_add_to_cart']) :?>
                            <p class="product_to_cart">
                                <?php echo $this->product['actionButtons'];?>
                            </p>
                        <?php endif;?>
                   </div>
                    <div class="clr"></div>
                </div>
            </div>
           <?php endif;  ?>
        </div>
    </div>
</div>