<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    
});
function toeReplaceProductFileComplete(filename, res) {
    toeProcessAjaxResponse(res, 'toeProductFileMsg_'+ res.data.fid);
    jQuery('#toeProductDownloadFileDesc_'+ res.data.fid).html( res.data.newDescription );
}
// -->
</script>
<div class="product_downloads_wrapper">
    <div class="product_downloads">
        <?php 
		$i=0;
		if(!empty($this->downloads)): ?>
        <ul>
        <?php foreach ($this->downloads as $download): ?>
            <li> 
                <a id="toeProductDownloadFileDesc_<?php echo $download['id']?>" href="<?php echo admin_url('admin-ajax.php?action=digital_download&id='. $download['id'])?>"
                   title="<?php lang::_e('Click to download file')?>"> 
                    <?php echo $download['description']?>
                </a>
				<a class='toeEditProductFile' id='<?php echo $download['id'];?>' title='Edit'></a>
				<div class='toeProductFileOptions file_item_<?php echo $download['id'];?>'>
					<p>
						<label><?php lang::_e('Period For Download');?> : </label>
						<input type="text" name="exists[<?php echo $i;?>][period_limit]" class="product_numeric" value="<?php echo $download['period_limit'];?>" />
					</p>
					<input type='hidden' name="exists[<?php echo $i;?>][id]" value="<?php echo $download['id']?>" />
					<p>
						<label><?php lang::_e('Download Limit');?> : </label>
						<input type="text" name="exists[<?php echo $i;?>][download_limit]" class="product_numeric" value="<?php echo $download['download_limit'];?>" />
					</p>
					<p>
						<label><?php lang::_e('File Description')?></label><br />
					    <textarea cols="30" rows="3" name="exists[<?php echo $i;?>][description]"><?php echo $download['description']?></textarea>
					</p>
				</div>
                <a href="javascript:void(0);" class="delete_product_file" 
                   rel="<?php echo $download['id']?>"
                   title="<?php lang::_e('Delete Product File');?>"></a><br />
                <?php echo html::ajaxfile('replaceProdFile_'. $download['id'], array(
                    'url' => uri::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'digital_product', 'action' => 'replaceFile', 'fid' => $download['id'], 'reqType' => 'ajax')),
                    'responseType' => 'json',
                    'buttonName' => 'Replace File',
                    'onComplete' => 'toeReplaceProductFileComplete',
                    'onSubmit' => 'function(){ jQuery("#toeProductFileMsg_'. $download['id']. '").showLoader(); }',
                ))?>
               <div id="toeProductFileMsg_<?php echo $download['id']?>"></div>
            </li>
        <?php 
		$i++;
		endforeach;?>
        </ul>
        <?php endif;?>
    </div>
    <div id="upload_file">     
        <div class="upload_file" id="first_raw">
            <p>
                <label><?php lang::_e('Choose File');?> : </label> <br />
                <input type="file" class="uploader" name="product_files" />
            </p>
			<label><?php lang::_e('Set URL');?> : </label> <br />
            <textarea cols="30" rows="1" name="product_ftp_urls[]"></textarea>
            <p>
                <label>
					<?php lang::_e('Set Download Limit');?>
					&nbsp;&nbsp;
					<a href="#" class="toeOptTip" tip="<?php lang::_e('Number of downloads, if you want to set it to infinite - just set big value, for example 99999')?>"></a>
				</label>:
                <input type="text" name="download_limit[]" class="product_numeric" value="<?php echo $this->params->download_limit;?>" />
            </p>
            <p>
                <label>
					<?php lang::_e('Set Period For Download');?>
					&nbsp;&nbsp;
					<a href="#" class="toeOptTip" tip="<?php lang::_e('Number of days, that download will be available for customer after purchase')?>"></a>
				</label>:
                <input type="text" name="period_limit[]" class="product_numeric" value="<?php echo $this->params->period_limit;?>" />
            </p>
            <label><?php lang::_e('File Description')?></label><br />
            <textarea cols="30" rows="3" name="description[]"></textarea>
            <span class="remove_product_file"></span>
        </div>
     </div>
    <h4>
        <a href="javascript:void(0);" id="add_product_download">
            <?php lang::_e('+ Add File');?>
        </a>
    </h4>
    <input type="submit" value="<?php lang::_e('Send');?>" name="save" class="button button-primary" />
</div>       
