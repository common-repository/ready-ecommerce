<div>
    <?php lang::_e('Per page')?>:
    <?php foreach($this->postsPerPageValues as $i) { ?>
        <?php if($i != $this->posts_per_page) { ?>
        <a href="<?php uri::_e(array('baseUrl' => $this->baseHrefForPerPage, 'posts_per_page' => $i))?>">    
        <?php }?>
            <?php lang::_e($i)?>
        <?php if($i != $this->posts_per_page) { ?>
        </a>
        <?php }?>
    <?php }?>
</div>
