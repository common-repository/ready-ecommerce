<div class="toeWidget">
    <?php if(!empty($this->title)) { ?>
        <div class="toeWidgetTitle"><?php lang::_e($this->title)?></div>
    <?php }?>
    <div id="toeBCWidgetContent<?php echo $this->uniqID?>">
        <div class="product_categories">   
            <?php if (!empty($this->data)):?>
                <?php foreach ($this->data as $category) :?>
                    <div class="product_category">
                        <div class="category_image">
                            <a href="<?php echo $category->href; ?>" title="<?php echo $category->name; ?>">
                                <?php 
                                    $width = $this->imgWidth;
                                    $imgsrc = $category->image;
                                    $timpath = S_TIM_PATH;
                                    $timsettings = '&w='.$width.'&zc=1';
                                ?>
                                <img width="<?php echo $width; ?>" src="<?php echo $timpath.$imgsrc.$timsettings; ?>" alt="<?php echo $category->name?>" />
                            </a>
                        </div>
                        <div class="category_link">
                            <a href="<?php echo $category->href?>" title="<?php echo $category->name?>"><?php echo $category->name?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif;?>
			<br clear="all" />
        </div>
    </div>
</div>
