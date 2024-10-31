<table width="100%">
<?php foreach($this->brands as $b) { ?>
	<tr>
		<td style="vertical-align: top;">
			<?php if($imgSrc = frame::_()->getModule('products')->getBrandImage($b)) { ?>
				<a href="<?php echo frame::_()->getModule('products')->getLinkToBrand($b, $b->slug)?>"><?php echo html::img($imgSrc, false);?></a>
			<?php } else {
				echo '&nbsp;';
			}?> 
		</td>
		<td style="vertical-align: top;">
			<h1 class="toeCategoryListTitle">
				<a href="<?php echo frame::_()->getModule('products')->getLinkToBrand($b, $b->slug)?>"><?php echo $b->name?></a>
			</h1>
			<div class="toeCategoryListDescription"><?php echo nl2br($b->description)?></div>
		</td>
	</tr>
<?php }?>
</table>
