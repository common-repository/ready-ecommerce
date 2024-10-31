<style>=
.metadiv {
display:none;
}
.curs {
cursor: pointer;
border-right: 1px solid;
border-left: 1px solid;
margin-right: 10px;
padding-left: 10px;
padding-right: 10px;
}
#dopdiv3 .upload_file {
width:260px;
float:left;
margin-right: 15px;
}
#dopdiv3 h4 {
clear:both;
}
</style>
<script type="text/javascript">
// <!--
    var toeSelectedEfVals = new Array();
	var ido = 0;
    jQuery(document).ready(function(){
	
	
	
		jQuery('#variationInfo').live('submit',function(){
			jQuery(this).sendForm({onSuccess: function(res){
			jQuery('#variationInfo').off('submit');
			//console.log(res);
			jQuery("#toe_sub_screen_bg").click();
			
		
        }});
			
			return false;
		});
		jQuery('.add').click(function(){
		jQuery("input[name='pid']").val(0);
		 jQuery('.metadiv:first').hide('slow');//jQuery('.metadiv [name="price"]').attr("type")
		jQuery(".metadiv input").each(function(){ if(jQuery(this).attr("type")!="file") jQuery(this).val(0);} );
		jQuery("#vari input[name='var_name']").val("New variation");
		jQuery("#selvar [id='0']").attr("selected", "selected");
		jQuery("#dopdiv .toeProdFieldset").html("<legend><?php lang::_e('Extra Product data')?></legend>");
		jQuery("#dopdiv3").html("");
		 jQuery('.metadiv:first').show('slow');
		});
		
		
		jQuery('#hidediv').click(function(){
		jQuery("#dopdiv").toggle('slow');
		return false;
		});
		jQuery('#hidediv1').click(function(){
		jQuery("#dopdiv1").toggle('slow');
		return false;
		});
		jQuery('#hidediv2').click(function(){
		jQuery("#dopdiv2").toggle('slow');
		return false;
		});
		
		
		jQuery('#hidediv3').click(function(){
		jQuery("#dopdiv").hide('slow');
		jQuery("#dopdiv1").hide('slow');
		jQuery("#dopdiv2").hide('slow');
		
		return false;
		});
		
		
		
		jQuery('#hidediv4').click(function(){
		
		jQuery("#dopdiv").show('slow');
		jQuery("#dopdiv1").show('slow');
		jQuery("#dopdiv2").show('slow');
		return false;
		});
		
		
		jQuery("#savebut").click(function(){
		
			//alert('test');
		
		});
		
		jQuery('.delete').click(function(){
		var name = jQuery('#selvar :selected').text();
		
		var id = jQuery('#selvar :selected').attr("id");
		if(id==0)
			return;
		var res = confirm("Delete variation '"+name+"'?");
		if (res) {
	
		jQuery("#dynformdelete input[name='postid']").val(id);
		
		jQuery("#dynformdelete").sendForm({onSuccess: function(res){
			
			//console.log(res);
			jQuery("#toe_sub_screen_bg").click();
			
		
        }});
		
		}
		});
		
		
		
		jQuery('#selvar').change(function(){
		var id = jQuery('#selvar :selected').attr("id");
		if(id==0)
		{
			jQuery('.metadiv:first').hide('slow');
			return;
		}
		jQuery("#dynform input[name='var_id']").val(id);
        jQuery("#dynform").sendForm({onSuccess: function(res){
		console.log(res);
		//console.log(res.data.variation);
			if(res.html) {
				jQuery('.metadiv:first').hide('fast');
				jQuery('.metadiv:first').show('slow');
				//jQuery('.metadiv').html("<div style='color:green;'>IMSHA PRIVET</div>");
				jQuery('.metadiv').html(res.html);
			}
			else if(res.data.variation) {
				jQuery('.metadiv:first').hide('fast');
				jQuery('.metadiv:first').show('slow');
				for (var i in res.data.variation)
				{
					//console.log('.metadiv input[name=\''+i+'\']');
					//console.log(res.data.variation[i]);
					jQuery('#vari input[name=\''+i+'\']').val(res.data.variation[i]);
				}
				jQuery("input[name='pid']").val(id);
				jQuery("#dopdiv").html(res.data.extraFields);
            }
			else {
				jQuery('.metadiv:first').hide('slow');
				jQuery("input[name='pid']").val(0);
			}
			if(res.data.downloads) {
			
			
			
				jQuery("#dopdiv3").html(res.data.downloads);
				var name = 'userfile['+id+'][]';
				jQuery("#dopdiv3 #first_raw").find(".uploader").attr('name',name);
				
				jQuery("#post").attr('enctype','multipart/form-data');
				
				jQuery(".delete_product_file").click(function(){
				   var link = jQuery(this); 
				   if (confirm('Are you sure you want to delete this file?')) {
					   var id = link.attr('rel');
					   var data = {
						   id : id,
						   action: 'digital_delete'
					   };
					   jQuery.post(ajaxurl, data, function(){
						   link.parent().remove();
					   });
				   } 
				});
				jQuery("#dopdiv3 [name='save']").click(function() { 
					var id = jQuery('#selvar :selected').attr("id");
					var name = 'userfile['+id+'][]';
					if(jQuery("#dopdiv3 .upload_file").length>0)
					{
					jQuery("#dopdiv3 .upload_file").each(function(){
					jQuery(this).appendTo("#toeProductDownloads");
					});
					jQuery("#toeProductDownloads [name='save']").click();
					}
				});
				
				jQuery("#dopdiv3 #add_product_download").click(function(){
				var count = jQuery("#dopdiv3 .uploader").length;
				var name = 'userfile['+id+'][]';
				jQuery("#dopdiv3 #first_raw").clone().appendTo("#dopdiv3 #upload_file").removeAttr("id").find(".uploader").attr('name',name);
				});
			}
		
        }});
		});
    });
    function toeFillInEfVal(parentBox, optId) {
        jQuery(parentBox).find('.toeEfValuesBox input[name=selectedOpts]').val(optId);
        if(jQuery(optId).size() > 1) {  //If more than one value is selected - all inputs will be empty
            var setPrice = true;
            var setAbsolute = true;
            var setActive = 0;
            var setSKU = true;
            var setWidth = true;
            var setHeight = true;
            var setWeight = true;
            var setLength = true;
            var setSort = true;
            for(var i = 1; i < optId.length; i++) {
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][price]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][price]"]').val())
                    setPrice = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][sku]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][sku]"]').val())
                    setSKU = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][width]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][width]"]').val())
                    setWidth = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][height]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][height]"]').val())
                    setHeight = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][length]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][length]"]').val())
                    setLength = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][weight]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][weight]"]').val())
                    setWeight = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][sort_order]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][sort_order]"]').val())
                    setSort = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][price_absolute]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][price_absolute]"]').val())
                    setAbsolute = false;
                if(jQuery(parentBox).find('input[name="exVal['+ optId[i-1]+ '][disabled]"]').val() != jQuery(parentBox).find('input[name="exVal['+ optId[i]+ '][disabled]"]').val())
                    setActive = false;
            }
            if(setPrice)
                jQuery(parentBox).find('.toeEfValuesBox input[name=attrs_price]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][price]"]').val() );
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=attrs_price]').val('');
            if(setSKU)
                jQuery(parentBox).find('.toeEfValuesBox input[name=sku]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][sku]"]').val() );
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=sku]').val('');
				
            if(setHeight)
                jQuery(parentBox).find('.toeEfValuesBox input[name=height]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][height]"]').val() );
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=height]').val('');
            if(setWeight)
                jQuery(parentBox).find('.toeEfValuesBox input[name=weight]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][weight]"]').val() );
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=weight]').val('');
            if(setLength)
                jQuery(parentBox).find('.toeEfValuesBox input[name=length]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][length]"]').val() );
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=length]').val('');
            if(setWidth)
                jQuery(parentBox).find('.toeEfValuesBox input[name=width]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][width]"]').val() );
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=width]').val('');
				
				
            if(setSort)
                jQuery(parentBox).find('.toeEfValuesBox input[name=sort_order]').val( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][sort_order]"]').val() );
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=sort_order]').val('0');
            if(setAbsolute) {
                if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][price_absolute]"]').val() ))
                    jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').attr('checked', 'checked');
                else
                    jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').removeAttr('checked');
            } else
                jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').removeAttr('checked');
            if(setActive) {
                if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId[0]+ '][disabled]"]').val() ))
                    jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').attr('checked', 'checked');
                else
                    jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').removeAttr('checked');
            } else
                jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').removeAttr('checked');
        } else {
            jQuery(parentBox).find('.toeEfValuesBox input[name=attrs_price]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][price]"]').val() );

            jQuery(parentBox).find('.toeEfValuesBox input[name=sku]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][sku]"]').val() );

            jQuery(parentBox).find('.toeEfValuesBox input[name=width]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][width]"]').val() );
            jQuery(parentBox).find('.toeEfValuesBox input[name=length]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][length]"]').val() );
            jQuery(parentBox).find('.toeEfValuesBox input[name=weight]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][weight]"]').val() );
            jQuery(parentBox).find('.toeEfValuesBox input[name=height]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][height]"]').val() );
			
            jQuery(parentBox).find('.toeEfValuesBox input[name=sort_order]').val( jQuery(parentBox).find('input[name="exVal['+ optId+ '][sort_order]"]').val() );

            if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId+ '][price_absolute]"]').val() ))
                jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').attr('checked', 'checked');
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=price_absolute]').removeAttr('checked');
            
            if(parseInt( jQuery(parentBox).find('input[name="exVal['+ optId+ '][disabled]"]').val() ))
                jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').attr('checked', 'checked');
            else
                jQuery(parentBox).find('.toeEfValuesBox input[name=disabled]').removeAttr('checked');
        }
    }
// -->
</script>
<div id="vari">
<form action="<?php echo uri::mod('products', '', '', array('id' => $this->id))?>" method="post" id="dynformdelete">
<?php echo html::hidden('postid', array('value' => 0))?>
<?php echo html::hidden('action', array('value' => 'deleteVar'))?>
<?php echo html::hidden('page', array('value' => 'products'))?>
<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
</form>

<form action="<?php echo uri::mod('products', '', '', array('id' => $this->id))?>" method="post" id="dynform">
<?php echo html::hidden('var_id', array('value' => 0))?>
<?php echo html::hidden('action', array('value' => 'getVariationInfo'))?>
<?php echo html::hidden('page', array('value' => 'products'))?>
<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
</form>


<form action="<?php echo uri::mod('products', '', '', array('id' => $this->id))?>" method="post" id="variationInfo">
<div id="product_extras">
<?php //var_dump($this->variations);?>
<div style="float:left;">
<input type="button" class="add button" value="Add New" />



<?php foreach($this->fields as $f) { ?>
			<?php if($f->name != "var_name") continue;?>
			<?php $f->value = "";?>
                 <?php $f->display()?>
        <?php }?>

<?php echo html::text('var_name');?>


<?php echo html::button(array('attrs' => 'class="button" id="savebut"', 'value' => lang::_('Save')))?></div>

<div style="float:right;"><input type="button" class="delete button" value="Remove Selected" /></div></div>
<div style="float:left; margin-left:15px;">
<select id="selvar">
<option id='0'> Select variation </option>
<?php foreach($this->variations as $var): ?>
<option id='<?php echo $var[0]['post_id'];?>'><?php echo $var[0]['var_name'];?></option>
<?php endforeach;?>
</select>
</div>
<br style="clear:both;" />
<!--
<?php foreach($this->variations as $var): ?>
    <fieldset class="toeProdFieldset">
        <legend><?php lang::_e('Main Product data')?></legend>
		<?php $i = 0; $itemPerRow = 3;?>
		<?php foreach($var as $key => $v):?>
            <div class="product_extra">
                 <label for="<?php echo $key;?>"><?php echo $key;?> :</label>
                 <div class="product_field"><input type="text" value="<?php echo $v;?>"></div>
                 <br clear="all" />
             </div>
			<?php 
				if($i%$itemPerRow == $itemPerRow-1) { ?>
				<br clear="all" />
				<?php }
				$i++;
			?>
		<?php endforeach;?>
    </fieldset>
    <br clear="all" />
<?php endforeach;?>
-->
<br/>
<div class="metadiv" style="display:none;">
	<a id="hidediv1" class="curs">Hide/show product data</a>
	<a id="hidediv" class="curs">Hide/show extra product data</a>
	<a id="hidediv2" class="curs">Hide/show files</a>
	<a id="hidediv3" class="curs">Hide all</a>
	<a id="hidediv4" class="curs">Show all</a>
	<div id="dopdiv1">
    <fieldset class="toeProdFieldset mybox">
        <legend><?php lang::_e('Main Product data')?></legend>
		<?php $i = 0; $itemPerRow = 3;?>
        <?php foreach($this->fields as $f) { ?>
			<?php if($f->name == "var_name") continue;?>
            <?php if ($f->html != 'hidden') {?>
			<?php $f->value = "";?>
            <div class="product_extra">
                 <label for="<?php echo $f->name?>"><?php echo $f->label?> :</label>
                 <div class="product_field"><?php $f->display()?></div>
                 <br clear="all" />
             </div>
			<?php 
				if($i%$itemPerRow == $itemPerRow-1) { ?>
				<br clear="all" />
				<?php }
				$i++;
			?>
        <?php } else  {
             echo $f->viewField($f->name, $this->extra_values[$f->id]);
        }?>
        <?php }?>
    </fieldset>
	</div>
	<div id="dopdiv"></div>
	
	<div id="dopdiv2">
	
    <fieldset class="toeProdFieldset mybox">
        <legend><?php lang::_e('Product files')?></legend>
		<div id="dopdiv3" ></div>
    </fieldset>
	</div>
	
</div>
    <br clear="all" />
				<br style="clear:both;"/>
	
							<?php echo html::hidden('ID', array('value' => $this->postid))?>
							<?php echo html::hidden('pid', array('value' => '-1'))?>
							<?php echo html::hidden('action', array('value' => 'postVariation'))?>
							<?php echo html::hidden('page', array('value' => 'products'))?>
							<?php echo html::hidden('reqType', array('value' => 'ajax'))?>
			  <?php //var_dump($options);?>
			  
    <br clear="all" />
</div>
</form>
</div>