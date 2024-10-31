<?php
class orderView extends view {
    public function printStatic() {
        $order = frame::_()->getModule('order')->getCurrent();
        if($order) {
            $this->assign('order', $order);
        } else
            return $this->getNotFound();

        return parent::getContent('orderStaticInfo');
    }
    public function printConfirnationPage() {
        $order = frame::_()->getModule('order')->getCurrent();
        if($order) {
            $this->assign('order', $order);
        } else
            return $this->getNotFound();
            
        return parent::getContent('orderConfirm');
    }
    public function getNotFound() {
        return parent::getContent('noOrderFound');
    }
    public function getAllOrders($filter = array()) {
		// Save tab usage
		frame::_()->getModule('promo_ready')->saveUsageStat('order.getAllOrders');
		
		frame::_()->addScript('datatable', S_JS_PATH. 'jquery.dataTables.js');
		frame::_()->addScript('timepicker', S_JS_PATH. 'jquery-ui-timepicker-addon.js');
		frame::_()->addScript('toeColumnFilter', S_JS_PATH. 'jquery.dataTables.columnFilter.js');
		frame::_()->addScript('toeOrderTableOptions', S_JS_PATH. 'toeOrderTableOptions.js');
		frame::_()->addScript('toeAddRemoveProd', S_JS_PATH. 'toeAddRemoveProd.js');

	   
		$uid = (int) req::getVar('uid');
		if($uid)
			$filter['user_id'] = $uid;
		$filter['orderByType'] = 'DESC';
        $orders = frame::_()->getModule('order')->getModel()->get( $filter );
        if($orders) {
			$ordersStatuses = frame::_()->getTable('orders')->getField('status')->getHtmlParam('options');
            $ordersByStatus = array();
			foreach($ordersStatuses as $sKey => $sVal) {
				$ordersByStatus[ $sKey ] = array();
			}
            foreach($orders as $o) {
                $ordersByStatus[ $o['status'] ][] = $o;
            }
            $listsByStatusHtml = array(
                'all' => $this->getList(array('orders' => $orders)),
            );
            foreach($ordersByStatus as $status => $statusOrders) {
                $listsByStatusHtml[ $status ] = $this->getList(array('orders' => $statusOrders));
            }
            $this->assign('listsByStatusHtml', $listsByStatusHtml);
            $this->assign('fields', frame::_()->getTable('orders')->getFields());
            $this->assign('ordersListAdminBottom', dispatcher::applyFilters('ordersListAdminBottom', ''));
            
            $tpl = 'allOrders';
        } else
            $tpl = 'noOrderFound';
        parent::display($tpl);
    }
    public function getList($d = array()) {
		$this->assign('orders', $d['orders']);
		return parent::getContent('list');
    }
    public function showOne($id = 0, $params = array('canEdit' => NULL)) {
        if(!$id)
            $id = req::getVar('id');
        if($id) {
            $user = frame::_()->getModule('user')->getCurrent();
            if(isset($params['canEdit']) && !is_null($params['canEdit'])) {
                $canEdit = $params['canEdit'];
            } else {
                if($user->isAdmin) {
                    $canEdit = true;
                } else {
                    $canEdit = false;
                }
            }
            $this->assign('canEdit', $canEdit);
            
            $order = frame::_()->getModule('order')->getModel()->get($id);
            $productsDisplay = array();
            foreach($order['products'] as $pid => $p) {
                $productsDisplay[$pid] = $p;
                $productsDisplay[$pid]['addDisplayData'] = dispatcher::applyFilters('addProdOrderDisplayData', '', $id, $p);
            }
            $order['products'] = $productsDisplay;
            $userMeta = frame::_()->getModule('user')->getModel()->getMeta();
            // Check for downloadable products
            $downloadsHtml = '';
            if(frame::_()->getTable('user_files')->exists($id, 'order_id'))
                $downloadsHtml = frame::_()->getModule('digital_product')->getView('downloads')->getDownloads(array('order_id' => $id));
            $otherOrderDataBottom = dispatcher::applyFilters('orderShowOneBottom', '', $order, $canEdit);
            $this->assign('order', $order);
            $this->assign('userMeta', $userMeta);
            $this->assign('downloadsHtml', $downloadsHtml);
            $this->assign('additionalSubtotalData', dispatcher::applyFilters('orderView::showOne[addSubtotal]', '', $id));
            $this->assign('otherOrderDataBottom', $otherOrderDataBottom);
            return parent::getContent('orderDetails');
        }
        return false;
    }
	public function showOneFull($id = 0, $params = array('canEdit' => NULL)) {
		$orderDataHtml = $this->showOne($id, $params);
		if($orderDataHtml) {
			$auditHtml = $this->showAudit($id);
			$this->assign('orderDataHtml', $orderDataHtml);
			$this->assign('auditHtml', $auditHtml);
			return parent::getContent('orderDetailsFull');
		}
		return false;
	}
	public function showAudit($id = 0) {
		$messages = frame::_()->getModule('log')->getModel()->get(array('type' => 'order', 'oid' => $id));
		$this->assign('messages', $messages);
		return parent::getContent('orderAudit');
	}
}
?>
