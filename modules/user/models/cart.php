<?php
class cartModel extends model {
    /**
     * Product columns data to show on cart page
     */
    protected $_columns = array(
        'id' => array('title' => 'ID', 'sortOrder' => 0, 'disable' => false),
        'img' => array('title' => 'Image', 'sortOrder' => 0, 'disable' => false),
        'name' => array('title' => 'Name', 'sortOrder' => 0, 'disable' => false),
        'qty' => array('title' => 'QTY', 'sortOrder' => 0, 'disable' => false),
        'price' => array('title' => 'Price', 'sortOrder' => 0, 'disable' => false),
        'total' => array('title' => 'Total', 'sortOrder' => 0, 'disable' => false),
        'action' => array('title' => 'Action', 'sortOrder' => 0, 'disable' => false),
    );
    protected $_columnsDetected = false;
    public function getColumns() {
        if(!$this->_columnsDetected)
            $this->_detectColumns();
        return $this->_columns;
    }
    protected function _detectColumns() {
        $this->_columnsDetected = true;
        $fromDb = frame::_()->getModule('options')->get('cart_columns');
        $fromDb = utils::jsonDecode($fromDb);
        if(!empty($fromDb)) {
            $current = $this->_columns;
            if(!empty($fromDb['sortOrder'])) {
                $this->_columns = array();
                foreach($fromDb['sortOrder'] as $i => $key) {
                    $this->_columns[$key] = $current[$key];
                    $this->_columns[$key]['sortOrder'] = (int) $i;
                }
            }
            $current = $this->_columns;
            if(!empty($fromDb['disable'])) {
                foreach($fromDb['disable'] as $k => $v) {
                    $this->_columns[$k]['disable'] = true;
                }
            }
        }
    }
	/**
	 * Will update $key for new $value in provided by $inCartId product data.
	 * Be carefull with this function!
	 */
	public function update($inCartId, $key, $value) {
		$cart = req::getVar('cart', 'session');
		if(isset($cart[ $inCartId ]) && !empty($cart[ $inCartId ]) && isset($cart[ $inCartId ][ $key ])) {
			$cart[ $inCartId ][ $key ] = $value;
			req::setVar('cart', $cart, 'session');
			return true;
		}
		return false;
	}
    public function put($d = array()) {
        $pid = isset($d['pid']) ? (int) $d['pid'] : false;
        $inCartId = isset($d['inCartId']) ? (int)$d['inCartId'] : false;
        if($pid) {
            $qty = (isset($d['qty']) && is_numeric($d['qty'])) ? (int) $d['qty'] : 1;
            if(!frame::_()->getModule('options')->get('stock_allow_checkout')) {    //Check product in stock
                if(frame::_()->getModule('products')->checkInStock(array(
                    'buyQty'		=> $qty,
                    'pid'			=> $pid,
					'dontUseCart'	=> $inCartId !== false)) === false	// It can be 0
				) {
					$this->pushError(lang::_('This product is out of stock'), '', 'qty');
					return false;
				}
            }
            $cart = req::getVar('cart', 'session');
            if(!is_array($cart)) req::setVar('cart', array(), 'session');
            $cart = req::getVar('cart', 'session');
			// TODO: check $this->exist() algorithm again
            $inCartId = $inCartId === false ? $this->exist($pid, $d['options']) : $inCartId;
			
            if($inCartId !== false) {
                if(!$d['addQty']) {
                    if($cart[$inCartId]['gift'] && $cart[$inCartId]['qty'] < $qty)
                        return false;
                    $cart[$inCartId]['qty'] = $qty;
                    if($cart[$inCartId]['qty'] <= 0) {
                        $this->delete($inCartId);        //If qty was < 0
                        $cart = $this->get();
                    }
                } else
                    $cart[$inCartId]['qty'] += $qty;
                if(isset($d['gift']) && $d['gift']) {
                    $cart[$inCartId]['gifts'][] = $d['gift'];
                }
            } else {
                $prod = frame::_()->getModule('products')->getModel('products')->get($pid);
                $post = get_post($pid);
                $inCartId = $this->getNextCartId();
				if(frame::_()->getModule('options')->get('use_new_products_fields')) {
					$options = frame::_()->getModule('products_fields')->getModel()->getProductFieldsFromRequest($d);
				} else {
					$options = frame::_()->getModule('options')->getModel('productfields')->getFieldsDesc($d);
				}
                if(empty($options))
                    $options = array();
				if(frame::_()->getModule('options')->get('use_new_products_fields')) {
					$allProductOptions = frame::_()->getModule('products_fields')->getModel()->getForProduct(array('pid' => $pid));
					if(!empty($allProductOptions)) {
						foreach($allProductOptions as $opt) {
							if((int)$opt['mandatory'] && empty($options[ $opt['id'] ]['selected'])) {
								// For text and textarea field types we should say Fill out, for other - select
								$selectionQuestion = in_array($opt['htmltype'], array('text', 'textarea')) ? 'Please fill out' : 'Please select';
								$this->pushError (lang::_(array($selectionQuestion, $opt['label'], 'at first')), 'options['. $opt['id']. ']', 'product_field');
							}
						}
					}
				} else {
					$allProductOptions = frame::_()->getModule('options')->getModel('productfields')->getProductExtraField($post, array('unsetEmptyFields' => true, 'where' => 'active = 1 AND '));
					if(!empty($allProductOptions)) {
						foreach($allProductOptions as $opt) {
							if((int)$opt->mandatory && empty($options[ $opt->getID() ]['selected'])) {
								// For text and textarea field types we should say Fill out, for other - select
								$selectionQuestion = in_array($opt->getHtml(), array('text', 'textarea')) ? 'Please fill out' : 'Please select';
								$this->pushError (lang::_(array($selectionQuestion, $opt->getLabel(), 'at first')), 'options['. $opt->getID(). ']', 'product_field');
							}
						}
					}
				}
                if($this->haveErrors()) {
                    return false;
                }
				
				$sku = $prod['sku']->value;
				$width =  $prod['width']->value;
				$height = $prod['height']->value;
				$length = $prod['length']->value;
				$weight = $prod['weight']->value;
				
				$inCartName = $post->post_title;
				if($post->post_type == S_VARIATION) {
					$parentPost = frame::_()->getModule('products')->getProductsPost($post->post_parent);
					if($parentPost && isset($parentPost->post_title)) {
						$inCartName = $parentPost->post_title. ': '. $post->post_title;
					}
				}
                $cart[$inCartId] = array(
                    'pid' => $pid,
                    'qty' => $qty, 
                    'name' => $inCartName, 
                    'sku' => $sku,
                    'categories' => frame::_()->getModule('products')->getModel()->getCategories($pid),
                    'brands' => frame::_()->getModule('products')->getModel()->getBrands($pid),
                    'weight' => $weight, //frame::_()->getModule('products')->getWeight($pid, $weight, $qty, array('options' => $options)),
                    
                    'width' => $width,
                    'height' => $height,
                    'length' => $length,
                    
                    'options' => $options,
					'gift'		=> false,	// All products in common are NOT gifts
                );
                if(isset($d['gift']) && $d['gift']) {
                    $cart[$inCartId]['price'] = 0;   //It's a gift
                    $cart[$inCartId]['gifts'] = array($d['gift']);
                    $cart[$inCartId]['gift'] = true;
                } else {
					$cart[$inCartId]['price'] = $prod['price']->value;	// Original price here
                }
            }
            req::setVar('cart', $cart, 'session');
            if(frame::_()->getModule('user')->isCustomer()) {   //If customer is logged in
                $this->_updateDB(frame::_()->getModule('user')->getCurrentID(), $cart);
            }
            return true;
        }
        return false;
    }
    /**
     * Checks the product in shopping cart
     */
    public function exist($pid, $options = array()) {
        $cart = $this->get();
        if(!empty($cart)) {
            foreach($cart as $id => $p) {
                if($p['pid'] == $pid && (empty($options) || $this->_isOptionsEqual($p['options'], $options))) {
                    return $id;
				}
            }
        }
        return false;
    }
    /**
     * Retrive product not only by ID, but buy options too
     * Alias for cartModel::exist() method
     */
    public function getProduct($pid, $options = array()) {
        return $this->exist($pid, $options);
    }
    protected function _isOptionsEqual($inCart, $fromForm) {
        $res = false;
        $foundInCart = 0;
        $fromFormNotEmptyNum = 0;
        foreach($fromForm as $fromFormId => $fromFormValue) {
            if(frame::_()->getModule('options')->getModel('productfields')->isValEmpty($fromFormValue)) continue;
            $fromFormNotEmptyNum++;
            if(isset($inCart[ $fromFormId ]) && $inCart[ $fromFormId ]['selected'] == $fromFormValue) {
                $foundInCart++;
            }
        }
        $res = ($foundInCart == $fromFormNotEmptyNum);
        return $res;
    }
    public function getNextCartId() {
        $cart = $this->get();
		if(empty($cart))
			return 0;
		$inCartIds = array_keys($cart);
		sort($inCartIds);
        return $inCartIds[ count($inCartIds)-1 ] + 1;
    }
    public function contentToDB($cart) {
        return json_encode($cart);
    }
    public function contentFromDB($cart) {
        return (array) json_decode($cart, true);
    }
    protected function _updateDB($user_id, $content) {
        if(frame::_()->getTable('cart')->exists($user_id, 'user_id'))
            if(empty($content)) {
                frame::_()->getTable('cart')->delete(array('user_id' => $user_id));
            } else {
                frame::_()->getTable('cart')->update(
                        array('content' => $this->contentToDB($content), 'updated' => time()), 
                        array('user_id' => $user_id)
                    );
            }
        else
            frame::_()->getTable('cart')->insert(array(
                    'user_id' => $user_id, 
                    'content' => $this->contentToDB($content),
                    'updated' => time()));
    }
    public function getForTplEditor() {
		$cart = array();
		$selectedPids = utils::unserialize(frame::_()->getModule('options')->get('page_editor_example_cart'));
		if(empty($selectedPids)) {
			$allProducts = frame::_()->getModule('products')->getHelper()->getProducts();
			$defaultProdsNum = 3;
			if(count($allProducts) > $defaultProdsNum) {
				$i = 0; 
				foreach($allProducts as $pid => $name) {
					if($i >= $defaultProdsNum) 
						break;
					$selectedPids[] = $pid;
					$i++;
				}
			} else {
				$selectedPids = array_keys($allProducts);
			}
		}
		if(!empty($selectedPids)) {
			$qty = 1;
			$options = array();
			foreach($selectedPids as $pid) {
				$prod = frame::_()->getModule('products')->getModel('products')->get($pid);
                $post = get_post($pid);
				$cart[] = array(
                    'pid' => $pid,
                    'qty' => $qty, 
                    'name' => $post->post_title, 
                    'sku' => $prod['sku']->value,
                    'categories' => frame::_()->getModule('products')->getModel()->getCategories($pid),
                    'brands' => frame::_()->getModule('products')->getModel()->getBrands($pid),
                    'weight' => frame::_()->getModule('products')->getWeight($pid, $prod['weight']->value, $qty, array('options' => $options)),
                    
                    'width' => $prod['width']->value,
                    'height' => $prod['height']->value,
                    'length' => $prod['length']->value,
                    
                    'options' => $options,
                );
				$qty++;
			}
		}
		return $cart;
	}
    public function get($d = array()) {
		if(frame::_()->isTplEditor()) {
			$data = $this->getForTplEditor();
		} else {
			$data = req::getVar('cart', 'session');
			if(is_numeric($d) && isset($data[$d])) {    //return data by product's ID
				$data = $data[$d];
			} elseif(isset($d['pid']) && ($inCartId = $this->exist($d['pid'])) !== false) {
				$data = $data[$inCartId];
			}
		}
        return $data;
    }
    public function delete($d = array()) {
        if(is_numeric($d)) {    //delete one product ID
            //$inCartId = $this->exist($d);
            $cart = $this->get();
			$prod = $cart[$d];
            unset($cart[$d]);
            req::setVar('cart', $cart, 'session');
            if(frame::_()->getModule('user')->isCustomer()) {   //If customer is logged in
                $this->_updateDB(frame::_()->getModule('user')->getCurrentID(), $cart);
            }
			dispatcher::doAction('cartProdDelete', $prod);
        } else {
			$cart = $this->get();
            req::clearVar('cart', 'session');
			if(!empty($cart)) {
				foreach($cart as $inCartId => $p) {
					dispatcher::doAction('cartProdDelete', $p);
				}
			}
		}
        if(frame::_()->getModule('user')->isCustomer()) {   //If customer is logged in
                
        } else {                                            //Guest user

        }
    }
    public function restoreFromDB($data) {
        $user_id = $data['user_id'];
        $content = $this->get();
        $contentFromDB = frame::_()->getTable('cart')->get(
                'content', 
                array('user_id' => $data['user_id']),
                '', 
                'one');
        if($contentFromDB) {
            $contentFromDB = $this->contentFromDB($contentFromDB);
            $content = $this->mergeDbAndSession($contentFromDB, $content);
        }
        req::setVar('cart', $content, 'session');
    }
    public function mergeDbAndSession($db, $session) {
        $res = array();
        foreach($db as $inCartId => $data) {
            $add = $data;
            if(isset($session[$inCartId])) {
                $add['qty'] += $session[$inCartId]['qty'];
                unset($session[$inCartId]);
            }
            $res[$inCartId] = $add;
        }
        if(!empty($session)) {
            $res = array_merge($res, $session);
        }
        return $res;
    }
    public function getCurrentWeight() {
        $totalWeight = 0;
        $cart = $this->get();
        if(!empty($cart)) {
            foreach($cart as $p) {
                $totalWeight += frame::_()->getModule('products')->getWeight($p['pid'], $p['weight'], $p['qty'], $p['options']);
            }
        }
        return $totalWeight;
    }
	public function getCurrentCubicVolume() {
		$totalVolume = 0;
        $cart = $this->get();
        if(!empty($cart)) {
            foreach($cart as $p) {
                $pSizes = frame::_()->getModule('products')->getSizes($p['pid'], $p, $p['qty'], $p['options']);     //as $p should contain sizes data in it
				$totalVolume += ($pSizes['width'] * $pSizes['height'] * $pSizes['length']);
            }
        }
        return $totalVolume;
	}
	public function getCurrentCubicSize() {
		$size = 0;
		$totalVolume = $this->getCurrentCubicVolume();
		if($totalVolume) {
			// cube root from total volume
			$size = pow($totalVolume, (1/3));
		}
		return $size;
	}
    public function getCurrentSizes() {
        $sizes = array('width' => 0, 'height' => 0, 'length' => 0);
        $cart = $this->get();
        if(!empty($cart)) {
            foreach($cart as $p) {
                $pSizes = frame::_()->getModule('products')->getSizes($p['pid'], $p, $p['qty'], $p['options']);     //as $p should contain sizes data in it
                $sizes['width'] += $pSizes['width'];
                $sizes['height'] += $pSizes['height'];
                $sizes['length'] += $pSizes['length'];
            }
        }
        return $sizes;
    }
	public function getTotalQty() {
		$totalQty = 0;
		$cart = $this->get();
		if(!empty($cart)) {
            foreach($cart as $p) {
                $totalQty += (int) $p['qty'];
            }
        }
		return $totalQty;
	}
	// Alias for checkout module method
	public function getTotalPrice() {
		return frame::_()->getModule('checkout')->getSubTotal();
	}
	public function haveShippingItems() {
		$cartItems = $this->get();
		foreach($cartItems as $product) {
			if(!frame::_()->getTable('product_files')->get('*', array('pid' => $product['pid'], 'type_id' => 1))) {	// Sell file types
				return true;
}
		}
		return false;
	}
}