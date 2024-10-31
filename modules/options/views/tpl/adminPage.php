<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    toeTables['modules_list_payment'] = new toeListTable('modules_list_payment', <?php echo utils::jsonEncode($this->modules['payment'])?>);
    toeTables['modules_list_payment'].draw();
	
	jQuery(document).scroll(function(event){
		var wHeight = jQuery(window).height();
		var tHeight = jQuery('#toeMainOptsTabsList').height();
		if(wHeight > tHeight) {
			jQuery('#toeMainOptsTabsList')
				.css('position', 'fixed')
				.css('right', '2%')
				.css('bottom', '2%');
		} else {
			jQuery('#toeMainOptsTabsList')
				.css('position', 'inherit')
				.css('right', 'auto')
				.css('bottom', 'auto');
		}
	});
});
function toeGoToExtensionsPromoPage() {
	window.open('https://readyshoppingcart.com/extensions/?ref=cust');
	return false;
}
// -->
</script>
<h1><?php lang::_e('Options Page')?></h1>
<div id="toe_opt_tabs">
    <ul id="toeMainOptsTabsList">
        <?php foreach ($this->tabs as $tab) {?>
		<?php
			$tabModule = $tab->getModule();
			$tabView = $tab->getView();
			if(empty($tabView))
				$tabView = $tabModule;
		?>
        <li style="width: <?php echo $tab->getWidthPercentage()?>%; margin-bottom: 0.1em;">
			<a href="#toe_opt_<?php echo $tabView?>" module="<?php echo $tabModule?>" view="<?php echo $tabView?>"><?php lang::_e($tab->getName())?></a>
		</li>
        <?php }?>
        <li><a href="#toe_opt_modules_payment"><?php lang::_e('Payments')?></a></li>
        <li id="toeModLastTab" onclick="toeGoToExtensionsPromoPage(); return false;"><a href="#"><?php lang::_e('All Modules')?></a></li>
    </ul>
	<?php $i = 0;?>
    <?php foreach ($this->tabs as $tab) {?>
		<?php
			$tabModule = $tab->getModule();
			$tabView = $tab->getView();
			if(empty($tabView))
				$tabView = $tabModule;
		?>
        <div id="toe_opt_<?php echo $tabView?>" class="toeOptTabContent">
            <?php /*Load only first tab content using php, all other will be loaded via ajax, see adminOptions.js*/?>
			<?php if($i == 0) {?>
				<?php echo frame::_()->getModule($tab->getModule())
                                 ->getController($tab->getController())
                                 ->getView($tab->getView())->getTabContent();?>
				<script type="text/javascript">
				// <!--
				jQuery(document).ready(function(){
					toeAdminOptTabLoaded('<?php echo $tab->getModule()?>', '<?php echo $tab->getView()?>');
				});
				// -->
				</script>
			<?php }?>
			<?php $i++;?>
        </div>
    <?php }?>
    <div id="toe_opt_modules_payment" class="toeOptTabContent">
        <table width="100%" id="modules_list_payment" class="options_list">
            <tr class="toe_admin_row_header">
                <td><?php lang::_e('ID')?></td>
                <td><?php lang::_e('Label')?></td>
                <td><?php lang::_e('Code')?></td>
                <td><?php lang::_e('Type')?></td>
                <td><?php lang::_e('Status')?></td>
            </tr>
            <tr class="toe_admin_row toe_opt_module toeRowExample">
                <td class="id"></td>
                <td class="label"></td>
                <td>
                    <span class="code"></span>
                    <a href="#" class="toeOptTip description" htmlTo="tip"></a>
                </td>
                <td class="type"></td>
                <td class="action" align="center"><div><a href="#" class="active" onclick="toeSwitchModuleStatus(this); return false;"></a></div></td>
            </tr>
        </table>
    </div>
	<?php /*?>
    <div id="toe_opt_modules" class="toeOptTabContent">
        <table width="100%" id="modules_list" class="options_list">
            <tr class="toe_admin_row_header">
                <td><?php lang::_e('ID')?></td>
                <td><?php lang::_e('Label')?></td>
                <td><?php lang::_e('Code')?></td>
                <td><?php lang::_e('Type')?></td>
                <td><?php lang::_e('Status')?></td>
            </tr>
            <tr class="toe_admin_row toe_opt_module toeRowExample">
                <td class="id"></td>
                <td class="label"></td>
                <td>
                    <span class="code"></span>
                    <a href="#" class="toeOptTip description" htmlTo="tip"></a>
                </td>
                <td class="type"></td>
                <td align="center"><div><a href="#" class="active" onclick="toeSwitchModuleStatus(this); return false;"></a></div></td>
            </tr>
        </table>
    </div>
	<?php */?>
    <div id="toeOptModulesMsg" style="clear: both;"></div>
</div>
<script type="text/javascript">
// <!--
var toeUnregisteredModules = <?php echo utils::jsonEncode(modInstaller::getActivationModules())?>;
// -->
</script>
<div id="toeOptDescription"></div>