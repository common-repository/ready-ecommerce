<form action="" method="post" id="opt_general_form">
	<?php foreach($this->options as $catOpts) {?>
		<?php //if(empty($catOpts['opts'])) continue; //this will not work as array can caonsists of values with htmltyp hidden - array will be not empty, but there will be no value to display?>
		<div class="">
			<div><h2><?php lang::_e($catOpts['cat_label'])?></h2></div>
			<div>
				<table width="100%" id="options_list" class="options_list">
				   <tr class="toe_admin_row_header">
					   <td><?php lang::_e('ID')?></td>
					   <td><?php lang::_e('Label')?></td>
					   <td><?php lang::_e('Value')?></td>
				   </tr>
				   <?php foreach($catOpts['opts'] as $opt) {
					   if(in_array($opt['code'], array('checkout_steps', 'cart_columns', 'checkout_success_text')) || in_array($opt['htmltype'], array('hidden'))) continue;?>
				   <tr class="toe_admin_row">
					   <td><?php echo $opt['id']?></td>
					   <td>
							<?php lang::_e($opt['label'])?>
							<a href="#" class="toeOptTip" tip="<?php lang::_e($opt['description'])?>"></a>
					   </td>
					   <td width="60%" class="field_input">
					   <?php
							$htmltype = $opt['htmltype'];
							$htmlOptions = array('value' => $opt['value'], 'attrs' => 'class="toe_general_opt_input"');
							switch($htmltype) {
								case 'checkbox':
									$htmlOptions['checked'] = (bool)$opt['value'];
									break;
								case 'selectbox':
									switch($opt['code']) {
										case 'default_theme':
											$templates = frame::_()->getModule('templates')->getModel()->get();
											$htmlOptions['options'] = array();
											foreach($templates as $code => $tpl) {
												$htmlOptions['options'][$code] = $code;
											}
											break;
										default:
											break;
									}
									break;
							}
							if(!empty($opt['params']) && is_array($opt['params'])) {
								$htmlOptions = array_merge($htmlOptions, $opt['params']);
							}
							echo html::$htmltype('opt_values['. $opt['id']. ']', $htmlOptions);
							if($htmltype != 'block')
								echo html::inputButton(array('value' => lang::_('Save'), 'attrs' => 'class="opt_general_save_butt button"'));
						?>
							<div class="toeMainOptsMsg"></div>
					   </td>
				   </tr>
				   <?php }?>
				</table>
			</div>
		</div>
	<?php }?>
    <div id="msg"></div>
    <?php echo html::hidden('reqType', array('value' => 'ajax'))?>
    <?php echo html::hidden('page', array('value' => 'options'))?>
    <?php echo html::hidden('action', array('value' => 'putOption'))?>
    <?php echo html::hidden('id', array('value' => ''))?>
    <?php echo html::hidden('value', array('value' => ''))?>
</form>
