var removedProdsCountCsp=0;
var allProdsList={};
var currentOrderId;
jQuery(document).ready(function(){
	jQuery('.toeRemoveOrderedProductCsp').click(function(){
		if(!confirm("Remove this product from order?")){
			return false;
		}
		prodId = this.id;
		var currentForm = jQuery(this).parents('form');
		orderId = jQuery(this).attr('order_key');
		sendData = {
					page      :'order',
					action    :"removeProductFromOrder",
					module    :"order",
					reqType   :"ajax",
					productID :prodId,
					orderID   :orderId
			}
			jQuery.sendForm({
				msgElID:currentForm.find('#msg'),
				data:sendData,
				onSuccess:function(resp){
					if(resp.messages.itemPrice){
						subTotal = currentForm.find("input[name='sub_total']").val();
						
						subTotal = (subTotal- resp.messages.itemPrice).toFixed(2);
						currentForm.find("input[name='sub_total']").val(subTotal);
						
						total = currentForm.find("input[name='total']").val();
						total = (total -resp.messages.itemPrice).toFixed(2);
						currentForm.find("input[name='total']").val(total);
						currentForm.find('#msg').empty();
					}
				}
			})
			
		jQuery('tr.prod_item_'+prodId).remove();
		itemToRemoveField ="<input type='hidden' name='remove_item["+removedProdsCountCsp+"]' value='"+prodId+"'>";
		jQuery('.toeAdditionalOptionsEditOrderCsp').append(itemToRemoveField);
		removedProdsCountCsp++;
	})
	
	jQuery('body').on('click','#toeaddNewProductToOrder',function(){
			currentOrderId=jQuery(this).attr('order_id');
			sendData = {
					page      :'products',
					action    :"getAllProducts",
					reqType   :"ajax"
			}
			
			if(jQuery('.toePopupAddNewProduct').length==0){
				jQuery("#wpbody").append("<div class='toePopupAddNewProduct'> 	<div class='addProductPopupContainer'> 	<a class='toeClosePopup button'>X</a> 		<div class='toeNewProductsResponse'></div></div></div>")
			}
					
			jQuery('.toeNewProductsResponse').empty();
			jQuery('.toePopupAddNewProduct').show();
			jQuery('.toePopupAddNewProduct').addClass('toeLoader');
			
			jQuery.sendForm({
				msgElID:"", 
				data: sendData,
				onSuccess: function(res) {
					if(!res.error){
					console.table(res.data.products);
						products = res.data.products;
						itemResp = "";
						jQuery('.toePopupAddNewProduct').removeClass('toeLoader');
						for(var i in products){

							currentProd = products[i];
							allProdsList[currentProd.ID]=currentProd;
							item="<td>"+currentProd.ID+"</td>";
							item+="<td>"+currentProd.post_title+"<input  type='hidden' name='products["+currentProd.ID+"][post_title]' value='"+currentProd.post_title+"' class='toeItemТitle'></td>";
							item+="<td>"+currentProd.price+"<input type='hidden' name='products["+currentProd.ID+"][price]' value='"+currentProd.price+"' class='toeItemPrice'></td>";
							item+="<td>"+currentProd.weight+"</td>";
							item+="<td>"+currentProd.quantity+"<input type='hidden' name='products["+currentProd.ID+"][quantity]' value='"+currentProd.quantity+"' class='toeItemQuantity'></td>";
							item+="<td><input type='checkbox' class='toeAddItemToOrder' value='"+currentProd.ID+"' ></td>";
							item+="<td><input type='text' name='products["+currentProd.ID+"][selected_quantity]' value='1' class='toeSelectProdCountToAddtoOrder'  id='"+currentProd.quantity+"'></td>";
							
							item = "<tr class='options_list toe_admin_row toe_order_row'>"+item+"</tr>";
							itemResp+=item;
						}
						toTable = "<h3>Select Products to add order</h3><table ><thead><tr class='toe_admin_row_header'><th>Number</th><th>Product Title</th><th>Price</th><th>Weight</th><th>Quantity</th><th>Add?</th><th>Count of product</th></tr></thead><tbody>"+itemResp+"</tbody></table><a class='button button-primary' id='toeaddSelectedProducts'>Add Selected Products</a><div class='toeAddProdToOrdMsg'></div>";
						jQuery('.toePopupAddNewProduct .toeNewProductsResponse').append(toTable)
						
					}
				}
			});
	})
	
	jQuery('body').on('click','.toeClosePopup',function(){
		jQuery('.toePopupAddNewProduct').hide();
		jQuery('.toeNewProductsResponse').empty();
	})
	jQuery('body').on("change",".toeSelectProdCountToAddtoOrder",function(){
		max_quantity =  parseInt(this.id);
		
		newVal = parseInt(jQuery(this).val());	
		if(newVal > max_quantity){
			alert("Max quantity is "+max_quantity);
			jQuery(this).val('');
			return false;
		}
	})
	jQuery('body').on('click','#toeaddSelectedProducts',function(){
		selectedProds=[];
		jQuery(this).prev('table').find('.toeAddItemToOrder').each(function(){
			if(jQuery(this).is(":checked")){
				
				prodId=jQuery(this).val();

				quantity = jQuery("input[name='products["+prodId+"][selected_quantity]'].toeSelectProdCountToAddtoOrder").val()			

				title = jQuery("input[name='products["+prodId+"][post_title]'].toeItemТitle").val();

				price = jQuery("input[name='products["+prodId+"][price]'].toeItemPrice").val();				
				selectedProds[selectedProds.length]={
					product_id:prodId,
					product_qty:quantity,
					product_price:price,
					product_name:title,
					product_sku:allProdsList[prodId].sku,
					
				}
			}
		})
	
		jQuery.sendForm({
			msgElID:jQuery('.toeAddProdToOrdMsg'),
			data:{page:'order',action:'addProductsToOrder',reqType:'ajax',params:{products:selectedProds,orderId:currentOrderId}},
			onSuccess: function(res){
				jQuery('.toeClosePopup').trigger('click');
				toeGetEditOrderScreen(currentOrderId);
			}
		})
		jQuery('#toeaddSelectedProducts').unbind('click');
	})
})	