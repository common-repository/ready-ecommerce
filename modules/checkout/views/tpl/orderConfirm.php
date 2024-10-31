<script type="text/javascript">
// <!--
    if(typeof(toeSetNavigationSelected) != 'undefined') {
        toeSetNavigationSelected('confirnation');
        toeSetNavigationPassed(['cart', 'checkout']);
    }
// -->
</script>
<div id="order_confirmation_content">
    <h1><?php lang::_e('Order Info')?></h1>
    <?php foreach($this->blokSteps as $sKey => $sInfo) {
        if(isset($sInfo['blokSteps']) && $sInfo['blokSteps']) continue;
        if(empty($this->$sKey)) continue;
    ?>
    <div class="toe_checkout_part_box toeFLeft">
        <h1><?php lang::_e($sInfo['title'])?></h1>
        <?php echo $this->$sKey?>
    </div>
    <?php }?>
</div>
<div class="toeClear"></div>
<div>
    <div class="payInform" style="float: right;"><?php echo $this->processHtml?></div>
    <div style="float: left;"><a href="<?php echo frame::_()->getModule('pages')->getLink(array('mod' => 'checkout', 'action' => 'getAllHtml'))?>"><?php lang::_e('Back')?></a></div>
</div>