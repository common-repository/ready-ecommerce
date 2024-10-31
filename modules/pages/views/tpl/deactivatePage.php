<html>
    <head>
        <title><?php lang::_e('Ready! Ecommerce - plugin deactivation')?></title>
    </head>
    <body>
<div style="position: fixed; margin-left: 40%; margin-right: auto; text-align: center; background-color: #fdf5ce; padding: 10px; margin-top: 10%;">
    <div><?php lang::_e('Ready! Ecommerce - plugin deactivation')?></div>
    <?php echo html::formStart('deactivatePlugin', array('action' => $this->REQUEST_URI, 'method' => $this->REQUEST_METHOD))?>
    <?php
        $formData = array();
        switch($this->REQUEST_METHOD) {
            case 'GET':
                $formData = $this->GET;
                break;
            case 'POST':
                $formData = $this->POST;
                break;
        }
        foreach($formData as $key => $val) {
            if(is_array($val)) {
                foreach($val as $subKey => $subVal) {
                    echo html::hidden($key. '['. $subKey. ']', array('value' => $subVal));
                }
            } else
                echo html::hidden($key, array('value' => $val));
        }
    ?>
        <table width="100%">
            <tr>
                <td><?php lang::_e('Delete Products Data')?>:</td>
                <td><?php echo html::radiobuttons('deleteProducts', array('options' => array('No', 'Yes')))?></td>
            </tr>
            <tr>
                <td><?php lang::_e('Delete Orders Data')?>:</td>
                <td><?php echo html::radiobuttons('deleteOrders', array('options' => array('No', 'Yes')))?></td>
            </tr>
            <tr>
                <td><?php lang::_e('Delete Other Data (options, modules setup, currencies, taxes, etc.)')?>:</td>
                <td><?php echo html::radiobuttons('deleteOptions', array('options' => array('No', 'Yes')))?></td>
            </tr>
        </table>
    <?php echo html::submit('toeGo', array('value' => lang::_('Done')))?>
    <?php echo html::formEnd()?>
    </div>
</body>
</html>