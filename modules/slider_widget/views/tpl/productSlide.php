<?php echo $this->product->post_title?><br />
<img src="<?php echo $this->prodImage['thumb'][0]?>" /><br />
<?php echo $this->product->post_excerpt?><br />
<?php if($this->specialData['is_special']) {
    if($this->specialData['mark_as_sale']) { ?>
        <b style="color: red; text-decoration: blink;"><?php lang::_e('SALE')?></b><br />
<?php }
    if($this->specialData['show_old_prices']) { ?>
        <?php lang::_e('Old Price')?>: 
        <i style="text-decoration: line-through;">
            <?php echo  frame::_()->getModule('currency')->display($this->specialData['oldPrice']);?>
        </i><br />
<?php }
    echo  frame::_()->getModule('currency')->display($this->specialData['newPrice']);
} else {
    echo  frame::_()->getModule('currency')->display($this->product->price);
}?>
<br />
<a href="<?php the_permalink(); ?>"><?php lang::_e('More Info')?></a>