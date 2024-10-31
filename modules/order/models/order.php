<?php
class orderModel extends model {
    public function post($d = array()) {
        $id = 0;
        $cart = frame::_()->getModule('user')->getModel('cart')->get();
        if($cart) {
            frame::_()->getModule('checkout')->clearCurrent();
            frame::_()->getModule('order')->clearCurrent();
            $do = 'insert';
            $currentID = frame::_()->getModule('order')->getCurrentID();
            if($currentID) 
                $do = 'update';
            
            $address = $this->_getAddresses($d);
            frame::_()->getModule('order')->setToCurrent('shipping_address', $address['shipping']);
            frame::_()->getModule('order')->setToCurrent('billing_address', $address['billing']);
			$d['shipping_module'] = isset($d['shipping_module']) ? $d['shipping_module'] : 0;
            $totalData = frame::_()->getModule('checkout')->calculate(array('all'), array('shippingMod' => $d['shipping_module']));
            $taxes = empty($totalData['taxrate']) ? '' : utils::jsonEncode($totalData['taxrate']);
            $user = wp_get_current_user();
            if(!empty($user) && !empty($user->ID))
                $uid = $user->ID;
            else
                $uid = 0;
            $shippingModuleData = frame::_()->getModule('shipping')->getModel()->get($d['shipping_module']);
            if(empty($shippingModuleData))
                $shippingModuleData = '';
            else {
				$shippingModuleData = toeObjectToArray($shippingModuleData);
                $shippingModuleData['cost'] = $totalData['shipping'];
                //$shippingModuleData = utils::jsonEncode( db::escape($shippingModuleData) );
				$shippingModuleData =  $this->_prepareAddressFieldToDb( db::escape($shippingModuleData )) ;
            }
            $sqlData = array(
                'shipping_address' => $this->_prepareAddressFieldToDb( $address['shipping'] ),
                'billing_address' => $this->_prepareAddressFieldToDb( $address['billing'] ),
                'shipping_module' => $shippingModuleData ,
                'payment_module' => db::prepareHtml($d['payment_module']),
                'taxes' =>  utils::jsonEncode( db::escape($totalData['taxrate']) ),
                'user_id' => $uid,
                'sub_total' => (float)$totalData['subTotal'],     //Will be modified in next version
                'tax_rate' => (float)$totalData['taxrate']['rateTotal'],
                'total' => (float)$totalData['total'],
                'status' => db::escape(frame::_()->getTable('orders')->getField('status')->default),
                'currency' => utils::jsonEncode( db::escape(frame::_()->getModule('currency')->getDefault()) ),
                'comments' => db::prepareHtml($d['comments']),
				'date_created' => time(),
            );
			// Really bad code, but this is only case when we can create fields with ' symbol, because we have quotes with ' symbol in our table class
			//$sqlData['shipping_address'] = str_replace(array("\\'"), array("\\\\\\\'"), $sqlData['shipping_address']);
			//$sqlData['billing_address'] = str_replace(array("\\'"), array("\\\\\\\'"), $sqlData['billing_address']);
            $dbRes = ($do == 'insert') ? 
                frame::_()->getTable('orders')->$do($sqlData) : 
                frame::_()->getTable('orders')->$do($sqlData, $currentID);
            if($dbRes) {
                if($do == 'insert') {
                    $currentID = db::lastID();
                    frame::_()->getModule('order')->setCurrentID($currentID);
                } else {
                    $this->deleteItems($currentID);
                }
                frame::_()->getModule('checkout')->clearCurrent();
                frame::_()->getModule('order')->clearCurrent();
                foreach($cart as $inCartId => $item) {
                    $product_params = array('options' => $item['options']);
                    $product_params = utils::jsonEncode( db::escape($product_params) );
                    $product_params = dispatcher::applyFilters('postOrderItemParams', $product_params, $item);
                    $sqlData = array(
                        'order_id' => $currentID,
                        'product_id' => $item['pid'],
                        'product_name' => db::escape($item['name']),
                        'product_sku' => db::escape($item['sku']),
                        'product_price' => $item['price'],
                        'product_qty' => $item['qty'],
                        'product_params' => $product_params,
                    );
                    frame::_()->getTable('orders_items')->insert($sqlData);
                }
                dispatcher::doAction('orderPost', $currentID);
            } else
                errors::push(frame::_()->getTable('orders')->getErrors());
        }
        return $currentID;
    }
    public function delete($d = array()) {
        frame::_()->getTable('orders')->delete($d);
        $this->deleteItems($d);
    }
    public function deleteItems($d = array()) {
        if(is_numeric($d) && $d) {
            frame::_()->getTable('orders_items')->delete(array('order_id' => $d));
        } else {
            frame::_()->getTable('orders_items')->delete($d);
        }
    }
	protected function _prepareAddressFieldToDb($address) {
		return str_replace(array("\\'"), array("\\\\\\\'"), utils::jsonEncode( db::prepareHtml($address) ));
	}
    public function prePostValidate($d = array()) {
        $res = new response();
        $address = $this->_getAddresses($d);
        $address['shipping']['useFor'] = 'shipping';
        $address['billing']['useFor'] = 'billing';
		$shippingSameAsBilling = (bool) req::getVar('shippingSameAsBilling');
        if($shippingAddrErrors = frame::_()->getModule('options')->getModel('userfields')->validate($address['shipping'])) {
            $errors = array();
            foreach($shippingAddrErrors as $key => $e) {
				$errKey = $shippingSameAsBilling ? 'billing_' : 'shipping_';
				$errKey .= $key;
                $errors[$errKey] = lang::_('Shipping Address: '). $e;
            }
            $res->addError($errors);
        }
        if($billingAddrErrors = frame::_()->getModule('options')->getModel('userfields')->validate($address['billing'])) {
            $errors = array();
            foreach($billingAddrErrors as $key => $e) {
				$errKey = 'billing_'. $key;
                $errors[$errKey] = lang::_('Billing Address: '). $e;
            }
            $res->addError($errors);
        }
        if(frame::_()->getModule('options')->getModel()->get('shipping_mandatory') 
			&& empty($d['shipping_module'])
			&& frame::_()->getModule('user')->getModel('cart')->haveShippingItems()
		) {
            $res->addError(lang::_('You must select Shipping Module before continue'), 'shipping_module');
        }
        if(frame::_()->getModule('options')->getModel()->get('payment_mandatory') && empty($d['payment_module'])) {
			$paymentIsMandatory = true;
			if(frame::_()->getModule('options')->getModel()->get('ignore_payments_on_zero_total')) {
				// If option "Ignore payments if order total amount is 0" is enabled and sub-total is 0 - ignore this validation
				$subTotal = frame::_()->getModule('checkout')->getSubTotal();
				if(empty($subTotal)) {
					$paymentIsMandatory = false;
				}
			}
			if($paymentIsMandatory)
				$res->addError(lang::_('You must select Payment Module before continue'), 'payment_module');
        }
		$steps = frame::_()->getModule('checkout')->getSteps();
        if(!$steps['terms']['disable'] && empty($d['checkTerms'])) {
            $res->addError(lang::_('You must read Terms and Conditions before continue'), 'checkTerms');
        }
       /* if(!empty($d['shipping_module']) && frame::_()->getModule('shipping')->moduleActive($d['shipping_module'])) {
                frame::_()->getModule('shipping')->getEngineFor($d['shipping_module'])->getRate(array_merge($d, $address));
                if(frame::_()->getModule('shipping')->getEngineFor($d['shipping_module'])->allowShipping() == false) {
                    $res->addError( frame::_()->getModule('shipping')->getEngineFor($d['shipping_module'])->getErrors() );
                }
        }*/
		if(!$this->validateShippingModule($d, $address))
			$res->pushError( $this->getErrors() );
        $res = dispatcher::applyFilters('orderPrePostValidate', $res);
        return $res;
    }
	public function validateShippingModule($d = array(), $address = array()) {
		if(!empty($d['shipping_module']) && frame::_()->getModule('shipping')->moduleActive($d['shipping_module'])) {
			frame::_()->getModule('shipping')->getEngineFor($d['shipping_module'])->getRate(array_merge($d, $address));
			if(frame::_()->getModule('shipping')->getEngineFor($d['shipping_module'])->allowShipping() == false) {
				$this->pushError( frame::_()->getModule('shipping')->getEngineFor($d['shipping_module'])->getErrors() );
				return false;
			}
        }
		return true;
	}
    /**
     * Retrives fields from input for payment and shipping
     * @param array $d input data
     * @return array array with 2 keys: shipping - shipping address, billing - billing address
     */
    protected function _getAddresses($d = array()) {
        $res = array(
            'shipping' => array(),
            'billing' => array(),
            /*Next 2  keys are used for combination with data keys in order array*/
            'shipping_address' => array(),
            'billing_address' => array(),
        );
        $shippingFields = frame::_()->getModule('user')->getModel()->getUserMeta(0, 'shipping');
        $billingFields = frame::_()->getModule('user')->getModel()->getUserMeta(0, 'billing');
        if(!empty($shippingFields)) {
            foreach($shippingFields as $f) {
                $res['shipping'][$f->name] = $d['shipping_'. $f->name];
            }
        }
        if(!empty($billingFields)) {
            foreach($billingFields as $f) {
                $res['billing'][$f->name] = $d['billing_'. $f->name];
            }
        }
        $res['shipping_address'] = $res['shipping'];
        $res['billing_address'] = $res['billing'];
        return $res;
    }
	/**
	 * Public alias for _getAddresses() method
	 */
	public function getAddresses($d = array()) {
		return $this->_getAddresses($d);
	}
    public function get($d = array()) {
        global $wpdb;
        $data = array();
        $orders = array();
        $query = 'SELECT o.*, oi.order_id, oi.product_id, oi.product_name, oi.product_sku, oi.product_price, oi.product_qty, oi.product_params, u.user_email
                FROM @__orders o 
                INNER JOIN @__orders_items oi ON oi.order_id = o.id
                LEFT JOIN '. $wpdb->users. ' u ON u.ID = o.user_id';
        if(is_numeric($d)) {    //Get by Order ID
           $query .= ' WHERE o.id = "'. (int)$d. '"';
           $data = db::get($query);
           if($data) {
               $orders = $this->recordFromDB($data, $data[0]['order_id']);
           }
        } else {
            if($d) {
                $w = frame::_()->getTable('orders')->_getQueryString($d);
                if($w) {
                    $query .= ' WHERE '. $w;
                }
            }
			$query .= ' ORDER BY o.id';
			if(isset($d['orderByType'])) {	// DESC or ASC
				$query .= ' '. $d['orderByType'];
			}
            $data = db::get($query);

            foreach($data as $d) {
                if(!isset($orders[$d['order_id']]))
                    $orders[$d['order_id']] = $this->recordFromDB($data, $d['order_id']);
            }
        }
        return $orders;
    }
    protected function _extractProduct($item) {
        $fields = frame::_()->getTable('orders_items')->getFields();
        $prod = array();
        foreach($fields as $f) {
            $prod[$f->name] = $item[$f->name];
            if($f->name == 'product_params') {
                if(empty($item[$f->name]))
                    $prod[$f->name] = array();
                else 
                    $prod[$f->name] = utils::jsonDecode ($item[$f->name]);
            }
        }
        return $prod;
    }
    public function recordFromDB($d, $oid) {
        $record = array();
        foreach($d as $item) {
            if($item['order_id'] == $oid) {
                if(empty($record)) {
                    $record = $item;
                    $record['products'] = array();
                }
                $record['products'][] = $this->_extractProduct($item);
            }
        }
		
        if(empty($record['shipping_address']))
            $record['shipping_address'] = array();
        else {
			$addressFromDb = utils::jsonDecode($record['shipping_address']);
            $record['shipping_address'] = toeMultArrayMap('stripslashes', $addressFromDb);
		}
        if(empty($record['billing_address']))
            $record['billing_address'] = array();
        else {
			$addressFromDb = utils::jsonDecode($record['billing_address']);
            $record['billing_address'] = toeMultArrayMap('stripslashes', $addressFromDb);
		}
        $record['currency'] = $this->_extractCurrency($record['currency']);
        $record['shipping_module'] = utils::jsonDecode($record['shipping_module']);
		
		$record = db::prepareHtmlOut( $record );
        return $record;
    }
    protected function _extractCurrency($currency) {
        if(empty($currency))
            return frame::_()->getModule('currency')->getDefault();
        elseif(is_string($currency))
            return (array)json_decode($currency);
        else
            return $currency;
    }
    public function put($d = array()) {
        $id = isset($d['id']) ? intval($d['id']) : NULL;
        if($id) {
            $sqlData = array();
            $fields = frame::_()->getTable('orders')->getFields();
            foreach($fields as $name => $f) {
                if(isset($d[$name])) {
					switch($name) {
						case 'shipping_address':
						case 'billing_address':
							$sqlData[$name] = is_array($d[$name]) ? 
								$this->_prepareAddressFieldToDb( $d[$name] ) :
								$d[$name];
							break;
						case 'shipping_module':
							$sqlData[$name] = is_array($d[$name]) ? 
								utils::jsonEncode( db::escape( $d[$name] )) :
								$d[$name];
							break;
							break;
						default:
								$sqlData[$name] = $d[$name];
							break;
					}
                }
            }
            if($sqlData) {
				$oldData = $this->get( $id );
                frame::_()->getTable('orders')->update($sqlData, $id);
				$newData = $this->get( $id );
				$this->_fillAudit($oldData, $newData);
                if(req::getVar('notifyCustomer')) {
					$order = $this->get( $id );	// Data after changes
					if(isset($order['user_id']) && !empty($order['user_id']))
						$order['email'] = get_the_author_meta('user_email', $order['user_id']);
                    frame::_()->getModule('order')->getController()->sendNotification($order);
				}
                return true;
            }
        }
        return false;
    }
	
	public function removeProductFromOrder($params){
		$orderId = (int)$params['orderID'];
		$productId = (int)$params['productID'];
		
		$res = frame::_()->getTable('orders_items')->get("*",array("order_id"=>$orderId,'product_id'=>$productId));
		$currentItem = array();
		
		if(empty($res)){
			return false;
		}
		$currentItem['quantity'] = $res[0]['product_qty'];
		$currentItem['price'] = $res[0]['product_price'];		
		$subPrice = $currentItem['quantity'] * $currentItem['price'];
		$changeAmountQuery = "update @__orders set total=total-$subPrice,sub_total=sub_total-$subPrice where id='".$orderId."' ";
		$addProductQty="update @__products set quantity=quantity+".$currentItem['quantity']." where post_id='".$productId."'";
		
		if(frame::_()->getTable('orders_items')->delete($res[0]['id']) && db::query($changeAmountQuery)){
				db::query($addProductQty);
				return array('itemPrice'=>$subPrice);
		}	
		return false;
	}
	public function addProductsToOrder($params){
		$changedAmount=0;

		$orderExistsProducts=frame::_()->getTable('orders_items')->get("*",array('order_id'=>$params['orderId']));
		if(!empty($orderExistsProducts)){
			foreach($params['products'] as &$item){
				$changedAmount += ((float)$item['product_price'])*((float)$item['product_qty']);
					if($item==null){
						continue;
					}	
					$item ['order_id']=$params['orderId'];
					$item['product_params']=json_encode(array('options'=>array()));
					for($i=0;$i<count($orderExistsProducts);$i++){
						if($orderExistsProducts[$i]['product_id'] ==$item['product_id']){

							$itemTotalQuantity = $orderExistsProducts[$i]['product_qty']+$item['product_qty'];
						
							frame::_()->getTable('orders_items')->update(array('product_qty'=>$itemTotalQuantity),array('product_id'=>$item['product_id']));

							$itemTotalPrice=number_format($orderExistsProducts[$i]['product_price']* $itemTotalQuantity,2,'.','');//to rem
							
							$amountChange = $item['product_qty']*$item['product_price']; 
							$updateOrderQuery = "update @__orders set total=total+".$amountChange.", sub_total=sub_total+".$amountChange." where id='".$params['orderId']."'";
							
							$changeProductQty="update @__products set quantity=quantity-".$item['product_qty']." where post_id='".$item['product_id']."'";

							if(!db::query($updateOrderQuery) ||	!db::query($changeProductQty)){
								$this->pushError(lang::_('Update failed'));
							}
							$item=null;
						}
					}

				}
		}
	

		foreach($params['products'] as $newProd){

			$amountChange=$newProd['product_price']*$newProd['product_qty']; 

			$updateOrderQuery = "update @__orders set total=total+".$amountChange.", sub_total=sub_total+".$amountChange." where id='".$params['orderId']."'";
			$res = frame::_()->getTable('orders_items')->insert($newProd);
			
			$changeProductQty="update @__products set quantity=quantity-".$newProd['product_qty']." where post_id='".$newProd['product_id']."'";
			if(!$res || !db::query($updateOrderQuery) || !db::query($changeProductQty)){
				$this->pushError('Update Failed');
			}
		}
		$errors = $this->getErrors(); 	
		return !empty($errors);
	}
	
	protected function _fillAudit($oldData, $newData) {
		$userMeta = null;
		$keyToName = array(
			'sub_total' => lang::_('Sub total'),
			'tax_rate' => lang::_('Tax Rate'),
			'total' => lang::_('Total'),
			'status' => lang::_('Status'),
		);
		foreach($oldData as $k => $v) {
			$auditData = array('oid' => $newData['id']);
			if($oldData[ $k ] != $newData[ $k ]) {
				switch($k) {
					case 'shipping_address':
					case 'billing_address':
						if(is_array($oldData[ $k ]) && is_array($newData[ $k ])) {
							if(empty($userMeta))
								$userMeta = frame::_()->getModule('user')->getModel()->getMeta();
							foreach($oldData[ $k ] as $addrK => $addrV) {
								if($oldData[ $k ][ $addrK ] != $newData[ $k ][ $addrK ]) {
									if(isset($userMeta[ $addrK ])) 
										$auditData['fieldName'] = lang::_($userMeta[ $addrK ]->label);
									else
										$auditData['fieldName'] = $addrK;
									$auditData['fieldName'] = $k == 'shipping_address' ? 
										lang::_('Shipping address - '). $auditData['fieldName'] :
										lang::_('Billing address - '). $auditData['fieldName'];
									switch($addrK) {
										case 'country':
											$auditData['from'] = fieldAdapter::displayCountry( $oldData[ $k ][ $addrK ] );
											$auditData['to'] = fieldAdapter::displayCountry( $newData[ $k ][ $addrK ] );
											break;
										case 'state':
											$auditData['from'] = fieldAdapter::displayState( $oldData[ $k ][ $addrK ] );
											$auditData['to'] = fieldAdapter::displayState( $newData[ $k ][ $addrK ] );
											break;
										default:
											$auditData['from'] = $oldData[ $k ][ $addrK ];
											$auditData['to'] = $newData[ $k ][ $addrK ];
											break;
									}
									$this->_addAuditChangeMessage($auditData);
								}
							}
						}
						break;
					case 'sub_total':
					case 'tax_rate':
					case 'total':
					case 'status':
						$auditData['fieldName'] = $keyToName[ $k ];
						$auditData['from'] = $oldData[ $k ];
						$auditData['to'] = $newData[ $k ];
						$this->_addAuditChangeMessage($auditData);
						break;
					case 'shipping_module':
						if(isset($oldData[ $k ]['cost']) && isset($newData[ $k ]['cost']) && ($oldData[ $k ]['cost'] != $newData[ $k ]['cost'])) {
							$auditData['fieldName'] = lang::_('Shiiping price');
							$auditData['from'] = $oldData[ $k ]['cost'];
							$auditData['to'] = $newData[ $k ]['cost'];
							$this->_addAuditChangeMessage($auditData);
						}
						break;
				}
			}
			if(!empty($msg)) {
				
			}
		}
	}
	private function _addAuditChangeMessage($d = array('fieldName' => '', 'from' => '', 'to' => '', 'msg' => '', 'oid' => '')) {
		if(empty($d['msg'])) {
			if(empty($d['from'])) {
				$d['msg'] = $d['fieldName']. lang::_(' was set to '). $d['to'];
			} elseif(empty($d['to'])) {
				$d['msg'] = $d['fieldName']. lang::_(' was cleaned from '). $d['from'];
			} else {
				$d['msg'] = $d['fieldName']. lang::_(' was changed from '). $d['from']. lang::_(' to '). $d['to'];
			}
		}
		if(!empty($d['msg'])) {
			frame::_()->getModule('log')->getModel()->post(array('type' => 'order', 'data' => $d['msg'], 'oid' => $d['oid']));
		}
	}
    public function getLastID() {
        return frame::_()->getTable('orders')->getLastInsertID();
    }
    public function getSuccessVariables($params = array('order' => NULL, 'getKeysOnly' => false)) {
        if(isset($params['getKeysOnly']) && $params['getKeysOnly'])
            return array(
                'user_name',
                'order_info',
                'store_name',
                'order_id',
                'account_login_link',
                'account_downloads_link',
            );
        if(empty($params['order']))
            $params['order'] = frame::_()->getModule('order')->getCurrent();
        if(!function_exists('get_user_by'))
            frame::_()->loadPlugins ();
        $user = get_user_by('id', $params['order']['user_id']);
        $userName = $user ? $user->data->display_name : '';
        $order_info = frame::_()->getModule('order')->getView('order')->showOne($params['order']['order_id'], array('canEdit' => false));
        $store_name = frame::_()->getModule('options')->getModel('options')->get(array('code'=>'store_name'));
        return array(
            'user_name' => $userName,
            'order_info' => $order_info,
            'store_name' => $store_name,
            'order_id' => $params['order']['order_id'],
            'account_login_link' => frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getAccountSummaryHtml')),
            'account_downloads_link' => frame::_()->getModule('pages')->getLink(array('mod' => 'digital_product', 'action' => 'getDownloadsList')),
			'account_orders_link' => frame::_()->getModule('pages')->getLink(array('mod' => 'user', 'action' => 'getOrdersList')),
        );
    }
	public function userHaveOrders($uid = 0) {
		$res = false;
		if($uid) {
			$res = (bool)frame::_()->getTable('orders')->exists($uid, 'user_id');
		}
		return $res;
	}
	public function remove($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			frame::_()->getTable('orders')->delete($d['id']);
			$this->deleteItems($d['id']);
			frame::_()->getTable('log')->delete(array('oid' => $d['id']));
			return $d['id'];
		} else
			$this->pushError (lang::_('Invalid Order ID'));
		return false;
	}
}
?>