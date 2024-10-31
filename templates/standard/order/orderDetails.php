<h1><?php lang::_e('Order Details')?></h1>
<?php if($this->canEdit) {
    echo html::formStart('order', array('method' => 'POST',  'attrs' => 'onsubmit="toeUpdateOrder(this); return false;"'));
}?>
<table width="100%" id="order_details">
    <tr class="toe_admin_row">
        <td><?php lang::_e('ID')?>:</td>
        <td><?php echo dispatcher::applyFilters('printOrderId', $this->order['id'])?></td>
    </tr>
    <?php if($this->order['shipping_address']) {?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Shipping Address')?>:</td>
        <td>
            <table width="100%">
                <?php foreach($this->order['shipping_address'] as $k => $v) { ?>
                        <tr>
                            <td>
                            <?php 
                                if(isset($this->userMeta[$k])) 
                                    lang::_e($this->userMeta[$k]->label);
                                else
                                    echo $k;
                            ?>
                            </td>
                            <td>
                            <?php 
                                if(!empty($this->userMeta[$k])) {
                                    $this->userMeta[$k]->setValue($v);
									$this->userMeta[$k]->setName('shipping_address['. $k. ']');
									$displayMethod = $this->canEdit ? 'display' : 'displayValue';
                                    echo $this->userMeta[$k]->$displayMethod();
                                }
                            ?>
                            </td>
                        </tr>
                <?php }?>
            </table>
        </td>
    </tr>
    <?php }?>
    <?php if($this->order['shipping_module']) { ?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Shipping')?>:</td>
        <td>
            <?php 
                if(is_array($this->order['shipping_module'])) //New multiple shipping system
                    echo $this->order['shipping_module']['label'];
                elseif(frame::_()->getModule($this->order['shipping_module']))  //For old shipping system
                    lang::_e(frame::_()->getModule($this->order['shipping_module'])->getLabel());
                else
                    lang::_e($this->order['shipping_module']);
            ?>
        </td>
    </tr>
    <?php } ?>
    <?php if($this->order['billing_address']) {?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Billing Address')?>:</td>
        <td>
            <table width="100%">
                <?php foreach($this->order['billing_address'] as $k => $v) { ?>
                        <tr>
                            <td>
                            <?php 
                                if(isset($this->userMeta[$k])) 
                                    lang::_e($this->userMeta[$k]->label);
                                else
                                    echo $k;
                            ?>
                            </td>
                            <td>
                            <?php 
                                if(!empty($this->userMeta[$k])) {
                                    $this->userMeta[$k]->setValue($v);
									$this->userMeta[$k]->setName('billing_address['. $k. ']');
									$displayMethod = $this->canEdit ? 'display' : 'displayValue';
                                    echo $this->userMeta[$k]->$displayMethod();
                                }
                            ?>
                            </td>
                        </tr>
                <?php }?>
            </table>
        </td>
    </tr>
    <?php }?>
    <?php if($this->order['payment_module']) { ?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Payment')?>:</td>
        <td>
            <?php 
                if(frame::_()->getModule($this->order['payment_module'])) 
                    echo lang::_(frame::_()->getModule($this->order['payment_module'])->getLabel());
                else
                    echo $this->order['payment_module'];
            ?>
        </td>
    </tr>
    <?php } ?>
	<?php if($this->canEdit) { ?>
	<tr class="toe_admin_row">
		<td colspan="2"><b style="color: red;"><?php lang::_e('Changes values below will not affect to all order details - now it is just data in database, so be careful with its changing!')?></b></td>
	</tr>	
	<?php }?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Sub total')?>:</td>
        <td>
		<?php 
			if($this->canEdit) {
				echo html::text('sub_total', array('value' => $this->order['sub_total'])). lang::_(' in store units, '). 
						frame::_()->getModule('currency')->display($this->order['sub_total'], $this->order['currency']).
						'<a href="#" class="toeOptTip description" htmlTo="tip" tip="'. lang::_('This value will be refreshed after this view reload.').'"></a>';
			} else {
				echo frame::_()->getModule('currency')->display($this->order['sub_total'], $this->order['currency']);
			}
		?>
		</td>
    </tr>
    <?php if(!empty($this->additionalSubtotalData)) echo $this->additionalSubtotalData?>
    <?php if(!empty($this->order['shipping_module']['cost'])) { ?>
        <tr class="toe_admin_row">
            <td><?php lang::_e('Shipping')?>:</td>
            <td>
			<?php 
				if($this->canEdit) {
					echo html::text('shipping_module[cost]', array('value' => $this->order['shipping_module']['cost'])). lang::_(' in store units, '). 
							frame::_()->getModule('currency')->display($this->order['shipping_module']['cost'], $this->order['currency']).
						'<a href="#" class="toeOptTip description" htmlTo="tip" tip="'. lang::_('This value will be refreshed after this view reload.').'"></a>';
				} else {
					echo frame::_()->getModule('currency')->display($this->order['shipping_module']['cost'], $this->order['currency']);
				}
			?>
			</td>
        </tr>
    <?php }?>
    <?php if($this->order['tax_rate']) { ?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Tax Rate')?>:</td>
        <td>
		<?php 
			if($this->canEdit) {
				echo html::text('tax_rate', array('value' => $this->order['tax_rate'])). lang::_(' in store units, '). 
						frame::_()->getModule('currency')->display($this->order['tax_rate'], $this->order['currency']).
						'<a href="#" class="toeOptTip description" htmlTo="tip" tip="'. lang::_('This value will be refreshed after this view reload.').'"></a>';
			} else {
				echo frame::_()->getModule('currency')->display($this->order['tax_rate'], $this->order['currency']);
			}
		?>
		</td>
    </tr>
    <?php }?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Total')?>:</td>
        <td>
		<?php 
			if($this->canEdit) {
				echo html::text('total', array('value' => $this->order['total'])). lang::_(' in store units, '). 
						frame::_()->getModule('currency')->display($this->order['total'], $this->order['currency']).
						'<a href="#" class="toeOptTip description" htmlTo="tip" tip="'. lang::_('This value will be refreshed after this view reload.').'"></a>';
			} else {
				echo frame::_()->getModule('currency')->display($this->order['total'], $this->order['currency']);
			}
		?>
		</td>
    </tr>
    <?php if($this->order['comments']) {?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Comments')?>:</td>
        <td>
		<?php 
			if($this->canEdit) {
				echo html::textarea('comments', array('value' => $this->order['comments']));
			} else {
				echo nl2br($this->order['comments']);
			}
		?>
        </td>
    </tr>
    <?php }?>
    <tr class="toe_admin_row">
        <td><?php lang::_e('Status')?></td>
        <td>
		<?php 
            if($this->canEdit) {
                $field = clone(frame::_()->getTable('orders')->getField('status'));
                $field->setValue($this->order['status']);
                $field->display();
            } else {
                echo $this->order['status'];
						
			}
		?>
		</td>
    </tr>
	<?php if($this->order['date_created']) { ?>
		<tr class="toe_admin_row">
			<td><?php lang::_e('Order Date')?></td>
			<td><?php echo date(S_DATE_FORMAT_HIS, $this->order['date_created'])?></td>
		</tr>
	<?php }?>
</table>
<h1><?php lang::_e('Ordered Products')?></h1> 
<?php if( $this->canEdit ){
	?>
		<a class='button' id='toeaddNewProductToOrder' order_id='<?php echo $this->order['id'];?>'><?php lang::_e("Add new product");?></a>
	<?php
}
?>
<table width="100%" class='toeProdsContainerTable'>
    <?php foreach($this->order['products'] as $pid => $p) { ?>
    <tr class="toe_admin_row toe_admin_row_header prod_item_<?php echo $p['product_id']?>">
        <td colspan="2"><?php echo $p['product_name']?></td>
		<?php
			if( $this->canEdit ){
				?>
					<td>
						<a class="toeRemoveOrderedProductCsp" id="<?php echo $p['product_id']?>" order_key='<?php echo $this->order['id']?>'>Remove
						</a>
					</td>				
				<?php
			}
		?>

    </tr>
    <?php 
        $allOrderItemsFields = frame::_()->getTable('orders_items')->getFields();
		
        $showOnlyItemsFields = array('product_name', 'product_sku', 'product_price', 'product_qty', 'product_params');
    ?>
        <?php foreach($showOnlyItemsFields as $k) {?>
        <tr class="toe_admin_row prod_item_<?php echo $p['product_id']?>">
            <td><?php lang::_e($allOrderItemsFields[$k]->label)?></td>

            <td><?php 
                switch($k) {
                    case 'product_price':
                        echo frame::_()->getModule('currency')->display($p[$k], $this->order['currency']);
                        break;
                    case 'product_params':
                        if(!empty($p[$k]['options'])) {
                            $strOptions = lang::_(array('Options', '<br />'));
                            
                            foreach($p[$k]['options'] as $opt) {
                                $strOptions .= $opt['label']. ': ';
                                if(is_array($opt['displayValue']))
                                    $strOptions .= implode(', ', $opt['displayValue']);
                                else
                                    $strOptions .= $opt['displayValue'];
                                $strOptions .= '<br />';
                            }
                            echo $strOptions;
                        }
                        break;
                    default:
                        echo $p[$k];
                        break;
                }
                ?>
            </td>
        </tr>
        <?php }?>
		<tr class="toe_admin_row prod_item_<?php echo $p['product_id']?>">
			<td><?php lang::_e('Product Total')?></td>
			<td><?php echo frame::_()->getModule('currency')->displayTotal($p['product_price'], $p['product_qty'], $p['product_id'], $p['product_params']);?></td>
		</tr>
        <?php if(!empty($p['addDisplayData'])) 
                echo $p['addDisplayData']?>
    <?php }?>
</table>

<?php if(!empty($this->downloadsHtml)) { ?>
<h1><?php lang::_e('Download Files')?></h1>
<div><?php echo $this->downloadsHtml?></div>
<?php }?>
<?php if($this->canEdit) { ?>
    <div id="msg"></div>
	<div class='toeAdditionalOptionsEditOrderCsp'>
		
	</div>
<?php
    echo html::hidden('reqType', array('value' => 'ajax'));
    echo html::hidden('mod', array('value' => 'order'));
    echo html::hidden('action', array('value' => 'updateOrder'));
    echo html::hidden('id', array('value' => $this->order['id']));
	echo html::checkbox('notifyCustomer', array('value' => '1'));
	echo '&nbsp;';
	lang::_e('Notify customer');
	echo '<br />';
    echo html::submit('update', array('value' => lang::_('Update')));
    echo html::formEnd();
}?>
<?php if(!empty($this->otherOrderDataBottom)) {
    echo $this->otherOrderDataBottom;
}?>