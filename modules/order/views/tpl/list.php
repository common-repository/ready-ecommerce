<div class="toeOrderListTableContainer ui-widget-content">
<div class="toeOrderTableCustomFilters">
	<div class="toeOrderTimeFiler toeOrderTableFilterCsp">
		<b>Select period for filtring order</b>
		<a class="toeClearTimefiltering">Clear Time Filter</a>
		<br/>
		<input type="text" id="orderDateFrom"/>
		<input type="text" id="orderDateTo"/>
	</div>
</div>
<table width="100%" class="options_list toeOrderListTableCsp" id="options_list">
<thead>
    <tr class="toe_admin_row_header">
        <th><?php lang::_e('Order ID')?></th>
        <th><?php lang::_e('Cost')?></th>
        <th><?php lang::_e('Status')?></th>
		<th><?php lang::_e('Order Date')?></th>
		<?php if(frame::_()->getModule('user')->isAdmin()) {?>
			<th><?php lang::_e('User Email')?></th>
			<th><?php lang::_e('Remove')?></th>
		<?php }?>
    </tr>
	</thead>
	<tbody>
<?php if(!empty($this->orders)) { ?>
	<?php foreach($this->orders as $o) { ?>
    <tr class="toe_admin_row toe_order_row">
        <td class="toeOrderListCell id"><?php echo $o['order_id']?></td>
        <td class="toeOrderListCell currency"><?php echo frame::_()->getModule('currency')->display($o['total'], $o['currency'])?></td>
        <td class="toeOrderListCell orderStatus" id="status_<?php echo $o['status']?>"><?php echo $o['status']?></td>
		<td class="toeOrderListCell orderDate" id="<?php echo str_replace(array('(',')'),"", ($o['date_created'] ? date(S_DATE_FORMAT_HIS_WB, $o['date_created']) : '&nbsp;'))?>" >
			<?php echo str_replace(array('(',')'),"",($o['date_created'] ? toeDate(S_DATE_FORMAT_HIS_WB, $o['date_created']) : '&nbsp;'))?>
		</td>
		<?php if(frame::_()->getModule('user')->isAdmin()) {?>
			<td>
			<?php if(!empty($o['user_email'])) { ?>
				<a href="<?php uri::_e(array('baseUrl' => admin_url('user-edit.php'), 'user_id' => $o['user_id']))?>" target="_blank"><?php echo $o['user_email']?></a>
			<?php } else { ?>
				&nbsp;
			<?php }?>
			</td>
			<td class="toeOrderListCellRemove"><a href="" onclick="return false;"><?php echo html::img('cross.gif')?></a></td>
		<?php }?>
    </tr>
	<?php } ?>
<?php }?>
</tbody>
</table>
</div>

