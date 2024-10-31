<?php if(isset($this->oldPrice)) { ?>
    <div><s><?php echo frame::_()->getModule('currency')->display($this->oldPrice)?></s></div>
<?php }?>
<div>
	<?php if($this->showFromPriceLabel) { ?>
		<?php lang::_e('From')?>
	<?php }?>
    <?php echo frame::_()->getModule('currency')->display($this->price)?>
</div>
<?php if(!empty($this->specials)) { ?>
<div>
<?php foreach($this->specials as $s) {
        lang::_e($s['label']). '<br />';
    }
    if(!empty($this->saleTpl)) { ?>
        <div><?php echo $this->saleTpl?></div>
<?php }?>
</div>
<?php }?>