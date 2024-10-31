<?php
	$isAdmin = frame::_()->getModule('user')->isAdmin();
?>
<div id="toeOrdersContainer">
<?php if($isAdmin) {?>
	<h1>Orders Page</h1>
<?php }?>
	<div class="btn-group">
		<button class="button button-primary button-large toeDropDownStatusesListCsp" data-toggle="dropdown">
			<span class="toeCurrentOrderStatusCsp">Statuses </span><span class="toeCaretCsp"></span>
		</button>
		<ul class="toeDropdownMenu" id="toeOrderStatusesListCsp">
			<li><a data_sort="all"><?php lang::_e('All')?></a></li>
			<?php foreach($this->fields['status']->getHtmlParam('options') as $sKey => $sName) { //for now $sKey and $sName are equal, but who know what will be next?... ?> 
				<li><a data_sort="toe_<?php echo $sKey;?>"><?php echo strFirstUp($sKey)?></a></li>
			<?php }?>
		</ul>
	</div>
    <div id="toe_orders_all">
		<?php echo $this->listsByStatusHtml['all'];?>
    </div>
	 <?php if(!empty($this->ordersListAdminBottom)) { ?>
		<div><?php echo $this->ordersListAdminBottom?></div>
    <?php }?>
</div>
