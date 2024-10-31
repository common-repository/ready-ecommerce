<div class="product_downloads_wrapper">
    <div class="product_downloads">
        <?php if(!empty($this->user_files)): ?>
        <ol>
        <?php foreach ($this->user_files as $user_file): ?>
            <li> 
                <a href="<?php echo $user_file['link'];?>"
                   title="<?php lang::_e('Click to download file')?>"> 
                    <?php echo $user_file['title']?>
                </a>
                <br />
                <span class="file_size"><?php echo lang::_('Size').' : '.$user_file['size'];?></span><br />
                <span class="file_size"><?php echo lang::_('Downloads Left').' : '.$user_file['downloads'];?></span><br />
                <?php if($user_file['expires']) {?>
                <span class="file_size"><?php echo lang::_('Expires').' : '.$user_file['expires'];?></span><br />
                <?php }?>
                <span class="file_size"><?php echo lang::_('Product').' : '.$user_file['product']?></span><br />
            </li>
        <?php endforeach;?>
        </ol>
        <?php else:?>
            <?php lang::_e("You haven't ordered any digital product yet");?>
        <?php endif;?>
    </div>
</div>       
