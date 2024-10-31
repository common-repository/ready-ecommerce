<?php
if($this->wp_query->max_num_pages > 1) {    //If this is not first page?>
    <?php //Navigation?>
    <div id="<?php echo $this->nav_id; ?>">
        <?php if($this->paged != 1) {?>
            <div class="nav-first toeNavFirst"><a href="<?php echo get_pagenum_link(1)?>"><?php lang::_e('<<')?></a></div>
            <div class="nav-previous toeNavPrev"><?php previous_posts_link(lang::_('<')); ?></div>
        <?php }?>
<?php for($i = 1; $i <= $this->wp_query->max_num_pages; $i++) { ?>
		<div class="toeNavPageNum">
			<?php if($this->paged != $i) {?>
			<a href="<?php echo get_pagenum_link($i)?>">
			<?php }?>
			<?php echo $i?>
			<?php if($this->paged != $i) {?>
			</a>
			<?php }?>
		</div>
<?php }?>
        <?php if($this->paged != $this->wp_query->max_num_pages) {  //If this is not last page?>
        <div class="nav-next toeNavNext"><?php next_posts_link(lang::_('>')); ?></div>
        <div class="nav-last toeNavLast"><a href="<?php echo get_pagenum_link($this->wp_query->max_num_pages)?>"><?php lang::_e('>>')?></a></div>
        <?php }?>
    </div><!-- #nav-above -->
	<div style="clear: both;"></div>
<?php }