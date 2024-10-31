<script type="text/javascript">
// <!--
var toeImgStatuses = <?php echo utils::jsonEncode($this->imgStatuses)?>;
function toeChangeImgStatus(selectbox, imgId) {
    var nextStatus = jQuery(selectbox).find('option:selected').val();
    imgId = parseInt(imgId);
    if(!imgId)
        return;
    jQuery(this).sendForm({
        msgElID: jQuery(selectbox).parents('div:first').find('.toeImgStatusMsg:first'),
        data: {page: 'products', action: 'setImgStatus', parent_id: <?php echo $this->post->ID?>, post_id: imgId, status: nextStatus, reqType: 'ajax'},
    });
}
toePostboxes['prodMedia'] = jQuery.extend(true, {}, toePostbox);
toePostboxes['prodMedia'].classContainer = '.meta-box-sortables-prodMedia';
toePostboxes['prodMedia'].save_order = function(page) {
	var newSortOrder = toeDetectSortOrder('product_media');
	jQuery('<br />' /*Any html element*/).sendForm({
		msgElID: 'toeProdMediaMsg',
		data: {page: 'products', action: 'saveImagesSortOrder', reqType: 'ajax', newSortOrder: newSortOrder.join(',')}
	});
}

jQuery(document).ready(function(){
	jQuery(".wrap h2").append('<a href="post-new.php?post_type=product" class="add-new-h2" id="newcopy">Add New and Copy Data</a>');
	jQuery("#newcopy").click(function(e){
		jQuery.sendForm({
				msgElID: '',
				data: {page: 'products', action: 'addNewCopy', reqType: 'ajax', post_id: <?php echo $this->post->ID;?>},
				onSuccess: function(res){
					if(res.data.copypost_id) {
						var url = "post.php?post="+res.data.copypost_id+"&action=edit";
						toeRedirect(url);
					}
				}
			});
		e.preventDefault();
	});
});
jQuery(function(){
	 toePostboxes['prodMedia'].add_postbox_toggles(pagenow);
});
// -->
</script>
<style type="text/css">
    .meta-box-sortables-prodMedia div {
        float: left;
    }
	.meta-box-sortables-prodMedia div.inside {
		float: none;
	}
</style>
<div class="toeClear"></div>
<div id="product_media" class="metabox-holder" style="width: 100%;">
	<div class="postbox-container">
		<div class="meta-box-sortables-prodMedia ui-sortable">
			<?php
			/**
			* Before WP 3.5 for delete attachment you should use delete-attachment_, after 3.5 (including) - delete-post_, this is WP whim
			*/
			$deleteImgType = version_compare(get_bloginfo('version'), '3.4.9.9', '>') ? 'delete-post_' : 'delete-attachment_';
			?>
			<?php foreach($this->media as $item) { ?>
				<div id="toeProdMedia-<?php echo $item->ID?>" class="postbox product_media" style="display: block; width: 100px;">
					<h3 class="hndle"><?php echo $item->post_title; ?></h3>
						<div class="inside">
							<?php echo "<a href='" . wp_nonce_url( "post.php?action=delete&amp;post=$item->ID", $deleteImgType . $item->ID ) . "'
									id='del[$item->ID]' class='delete_media' title='".lang::_('Delete')."'></a>"; ?>
							<?php if (strpos($item->post_mime_type,'image') === false) { // if attachment is not an image?>
							<a href="<?php echo $this->url['baseurl']?>/<?php echo $this->files[$item->ID]['uploads_dir'].'/'.$this->files[$item->ID]['image']?>" 
								   title="<?php echo lang::_('Download'); ?>"><?php echo $item->post_title; ?></a>
							<?php } else { // if attachment is file ?>
							
							<a href="<?php echo $this->files[$item->ID]['image'][0].'?type=image';?>" class="thickbox">
								<img src="<?php echo $this->files[$item->ID]['thumb'][0]?>"
									 width="<?php echo $this->files[$item->ID]['thumb'][1]?>"
									 height="<?php echo $this->files[$item->ID]['thumb'][2]?>"
									 alt="<?php echo $item->post_title ?>" /> 
							</a><br />
							<div style="left: 10px; bottom: -25px;">
								<?php lang::_e('Status')?>: 
								<?php echo html::selectbox('imgStatus', array(
									'value' => $this->files[$item->ID]['status'], 
									'options' => $this->imgStatusesOptions, 
									'attrs' => 'onchange="toeChangeImgStatus(this, '. (int)$item->ID. ');"'))?>
								<div class="toeImgStatusMsg"></div>
							</div>
							<p><?php echo $item->post_content; ?></p>
							<?php } ?> 
						</div>
				</div>
			<?php }?>
		</div>
	</div>
</div>
<div id="toeProdMediaMsg" class="toeClear"></div>
<h4>
    <a href="<?php echo admin_url('media-upload.php?post_id='.$this->post->ID.'&toeProdGalery=1&TB_iframe=1');?>" class="thickbox toeProdMediaActions">
        <?php lang::_e('+ Upload New Media');?>
    </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="<?php echo admin_url('media-upload.php?post_id='.$this->post->ID.'&toeProdGalery=1&tab=gallery&TB_iframe=1');?>" class="thickbox toeProdMediaActions">
        <?php lang::_e('+ Manage Media Files');?>
    </a>
</h4>
