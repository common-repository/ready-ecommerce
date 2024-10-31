<div class="toeWidget">
    <?php if(!empty($this->title)) { ?>
        <div class="toeWidgetTitle"><?php lang::_e($this->title)?></div>
    <?php }?>
    <div id="toeCurrencyWidgetContent<?php echo $this->uniqID?>">
        <?php foreach($this->all as $c) { ?>
        <?php if($this->default['id'] != $c['id']) {?>
            <a href="<?php echo uri::mod('currency', '', 'setCurrency', array('code' => $c['code'], 'redirect' => $this->redirect))?>">
        <?php }?>
            <?php lang::_e($c['code'])?>
        <?php if($this->default['id'] != $c['id']) {?>
            </a>
        <?php }?>
        <?php } ?>
    </div>
</div>
