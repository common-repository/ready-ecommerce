<table>
<?php
if($this->registrationUserMeta) {
    foreach($this->registrationUserMeta as $f) { ?>
    <tr>
        <td><?php lang::_e($f->label)?></td>
        <td><?php $f->{ $this->displayMethod }()?></td>
    </tr>
    <?php }
}
?>
</table>
