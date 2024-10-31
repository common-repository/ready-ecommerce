<div class="toeWidget">
    <?php if(!empty($this->params['title'])) { ?>
        <div class="toeWidgetTitle"><?php lang::_e($this->params['title'])?></div>
    <?php }?>
    <div id="toeRecentProductsWidgetContent<?php echo $this->uniqID?>">
        <?php echo html::formStart('toeSearch', array('method' => 'get'))?>
            <?php if($this->params['by_price']) { ?>
                <?php lang::_e('Price from')?>: <?php $this->fields['priceFrom']->display()?>
                <?php lang::_e('Price to')?>: <?php $this->fields['priceTo']->display()?>
                <br />
            <?php }?>
            <?php if($this->params['by_title_desc']) { ?>
                <?php lang::_e('By title and description')?>: <?php $this->fields['s']->display()?>
                <br />
            <?php }?>
            <?php if($this->params['by_options'] && !empty($this->fields['exOptions'])) { ?>
                <?php foreach($this->fields['exOptions'] as $o) { ?>
                    <?php lang::_e($o->getLabel())?>: <?php $o->display()?>
                    <br />
                <?php }?>
                <?php if(!empty($this->fields['taxonomy'])) { 
                    $this->fields['taxonomy']->display();
                }?>
            <?php }?>
            <?php echo html::hidden('post_type', array('value' => S_PRODUCT))?>
            <?php echo html::submit('search', array('value' => lang::_('Search')))?>
        <?php echo html::formEnd()?>
    </div>
</div>