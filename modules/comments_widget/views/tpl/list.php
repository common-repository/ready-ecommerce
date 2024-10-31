<div class="toeWidget">
    <?php if(!empty($this->instance['title'])) { ?>
        <div class="toeWidgetTitle"><?php lang::_e($this->instance['title'])?></div>
    <?php }?>
    <div id="toeCommentsWidgetContent<?php echo $this->uniqID?>">
        <div class="toeCommentsWidget">
            <?php if (!empty($this->comments)) {?>
                <?php foreach ($this->comments as $c) {?>
                <div class="toeWidgetComment">
                    <?php if($this->instance['show_product_link']) { ?>
                    <div class="toeWidgetCommentPostTitle">
                        <a href="<?php echo get_post_permalink($c['comment_post_ID'])?>"><?php echo $c['post_title']?></a>
                    </div>
                    <?php }?>
                    <div class="toeCommentsWidgetComment">
                    <?php 
                        $commentContent = $c['comment_content'];
                        if(!empty($this->instance['comment_len']) && is_numeric($this->instance['comment_len']) && strlen($commentContent) > $this->instance['comment_len']) {
                            $commentContent = substr($commentContent, 0, $this->instance['comment_len']). '...';
                        }
                        echo $commentContent;
                    ?>
                    </div>
                    <?php if(!empty($c['toeRate']) && !empty($this->rateStarsCount)) {?>
                    <div class="toeCommentsWidgetRating" id="toeCommentsWidgetRating<?php echo $c['comment_ID']?>">
                        <ul class="toeRating">
                            <?php for($i = 1; $i <= $this->rateStarsCount; $i++) {?>
                            <li>
                                <a href="#" onclick="return false;" class="toeRatingLink<?php echo $i?> toeStarOff toeRateStarStatic"><?php echo $i?></a>
                            </li>
                            <?php }?>
                        </ul>
                        <script type="text/javascript">
                        // <!--
                        toeSetRating(<?php echo $c['toeRate']?>, 0, jQuery('#toeCommentsWidgetRating<?php echo $c['comment_ID']?>'));
                        // -->
                        </script>
                    </div>
                    <?php }?>
                    <div class="toeClear"></div>
                    <?php if(!empty($c['comment_author'])) {?>
                    <div class="toeCommentsWidgetUsername">by <a href="<?php echo get_comment_link((object)$c)?>"><?php echo $c['comment_author']?></a></div>
                    <div class="toeClear"></div>
                    <?php }?>
                </div>
                <?php }?>
            <?php }?>
            <br clear="all" />
        </div>
    </div>
</div>