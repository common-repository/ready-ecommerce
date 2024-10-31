<span class="toeProdPrice">
	<?php if($this->showFromPriceLabel) { ?>
		<?php lang::_e('From')?>
	<?php }?>
    <?php echo frame::_()->getModule('currency')->display($this->price)?>
</span>
<?php if(isset($this->oldPrice)) { ?>
    <span class="toeOldProdPrice"><s><?php echo frame::_()->getModule('currency')->display($this->oldPrice)?></s></span>
<?php }?>
<?php if (is_single()): ?>
    <?php if(!empty($this->specials)) { ?>
    <span class="toeProdPriceSpecials">
    <?php foreach($this->specials as $s) {
            lang::_e($s['label']). '<br />';
        }?>
    </span>
    <?php }?>
<?php endif; ?>