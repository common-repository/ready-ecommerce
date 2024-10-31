<?php
class currencyModel extends model {
    protected $_currencies = array();
    public function get($d = array()) {
        parent::get($d);
        $fields = NULL;
        if(isset($d['id']) && is_numeric($d['id'])) {
            if($d['id']) {
                frame::_()->getTable('currency')->fillFromDB($d['id']);
            }
            $fields = frame::_()->getTable('currency')->getFields();
        } elseif(!empty($d) && is_array($d)) {
            $fields = frame::_()->getTable('currency')->get('*', $d);
        } else {
            $fields = frame::_()->getTable('currency')->getAll();
        }
        return $fields;
    }
    public function post($d = array()) {
        $res = new response();
        $d = $this->_prepareData($d);
        if(!is_array($d)) {
            $res = $d;
        } elseif(!$res->error) {
            if($id = frame::_()->getTable('currency')->insert($d)) {
                if($d['use_as_default'])
                    $this->_clearDefaultField($id);
                $res->messages[] = lang::_('Currency Added');
                $res->data = array(
                    'id' => $id, 
                    'label' => $d['label'], 
                    'code' => $d['code']
                );
            } else
                $res->errors[] = lang::_('Currency Insert Failed');
        }
        return $res;
    }
    protected function _prepareData($d = array()) {
        $res = new response();
        if(!isset($d['use_as_default']))
            $d['use_as_default'] = 0;
        if(empty($d['symbol']))
            $res->addError (lang::_('Currency symbol is empty'));
        if(empty($d['value']))
            $d['value'] = 1;
        if(!$res->error) {
            /*switch($d['price_view']) {
                
            }*/
            /*if(strpos($d['currency_view'], $d['symbol']) > strpos($d['currency_view'], '1')) {
                $d['symbol_left'] = '';
                $d['symbol_right'] = substr($d['currency_view'], 1);
            } else {
                $d['symbol_left'] = substr($d['currency_view'], 0, -1);
                $d['symbol_right'] = '';
            }
            $priceView = str_replace(array('0', '1'), '', $d['price_view']);
            if(strlen($priceView) == 1) {
                $d['symbol_thousand'] = substr($priceView, 0, -1);
                $d['decimal_places'] = 0;       //no decimals
            } else {
                $d['symbol_point'] = substr($priceView, -1);
                $d['symbol_thousand'] = substr($priceView, 0, -1);
                $d['decimal_places'] = 2;       //must be changed to avoid problems
            }*/
            return $d;
        }
        return $res;
    }
    public function put($d = array()) {
        $res = new response();
        $id = $d['id'];
        $d = $this->_prepareData($d);
        if(!is_array($d)) {
            $res = $d;
        } elseif(is_numeric($id)) {
            if(frame::_()->getTable('currency')->update($d, array('id' => $id))) {
                if($d['use_as_default'])
                    $this->_clearDefaultField($id);
                $res->messages[] = lang::_('Currency Updated');
                $res->data = array(
                    'id' => $id, 
                    'label' => $d['label'], 
                    'code' => $d['code']
                );
            } else {
                $tableErrors = frame::_()->getTable('currency')->getErrors();
                if(empty($tableErrors))
                    $res->addError(lang::_('Currency update failed'));
                else 
                    $res->addError($tableErrors);
            }
        } else {
            $res->addError(lang::_('Error currency ID'));
        }
        return $res;
    }
    public function delete($d = array()) {
        $res = new response();
        $id = $d['id'];
        if(is_numeric($id)) {
            if(frame::_()->getTable('currency')->delete($d, array('id' => $id))) {
                $res->messages[] = lang::_('Currency Deleted');
            } else
                $res->errors[] = lang::_('Currency Delete Failed');
        } else 
            $res->errors[] = lang::_('Error currency ID');
        return $res;
    }
    protected function _clearDefaultField($excludeID) {
        if(is_numeric($excludeID)) {
            frame::_()->getTable('currency')->update(array(
                'use_as_default' => 0
            ), 'id != '. $excludeID);
        }
    }
}
?>
