<div class="archive tax-products_categories">
	<?php if(class_exists('frame') && frame::_()->getModule('pagination')) {
		frame::_()->getModule('pagination')->getView()->display(array('nav_id' => 'pagination', 'show' => array('navigation', 'perPage', 'ordering')));
	}?>
	<?php foreach($this->productsContentParts as $pHtml) { ?>
	<div class="product">
		<?php echo $pHtml ?>
	</div>
	<?php }?>
	<?php if(class_exists('frame') && frame::_()->getModule('pagination')) {
		frame::_()->getModule('pagination')->getView()->display(array('nav_id' => 'pagination', 'show' => array('navigation', 'perPage', 'ordering')));
	}?>
</div>
