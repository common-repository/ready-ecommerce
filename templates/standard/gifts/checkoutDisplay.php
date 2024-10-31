<?php if(!empty($this->gifts['data']) && is_array($this->gifts['data'])) {?>
<h3><?php lang::_e('Choose your gift')?></h3>
<table>
<?php foreach($this->gifts['data'] as $g) { ?>
    <tr>
        <td style="width: 250px;">
            <b><?php lang::_e($g['label'])?></b>
            <?php if(!empty($g['img'])) { ?>
                <?php echo html::img($g['img'], 0)?>
            <?php }?>
            <?php if(!empty($g['description'])) {?>
                :<br /><i><?php lang::_e(nl2br($g['description']))?></i>
            <?php }?>
        </td>
        <td>
        <?php 
            $giftContent = '';
            switch($g['type']) {
                case 'product':
                    echo $g['freeProductLink'];
                    break;
            }
            echo $giftContent;
        ?>
        </td>
    </tr>
<?php }?>
</table>
<?php }?>
<?php /*if(!empty($this->gifts['total'])) { ?>
<tr>
    <td><?php lang::_e('Total gift bonus')?></td>
    <td><?php echo frame::_()->getModule('currency')->display($this->gifts['total'])?></td>
</tr>
<?php }*/?>
