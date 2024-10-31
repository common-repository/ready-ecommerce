<?php
class shippingModel extends model {
    public function get($d = array()) {
        if(is_numeric($d))
            $data = frame::_()->getTable('shipping')->getById($d);
        else
            $data = frame::_()->getTable('shipping')->get('*', $d);
        
        if(!empty($data)) 
            $data = $this->afterGet($data, is_numeric($d));
        
        return $data;
    }
    public function afterGet($data, $oneValue = false) {
        if($oneValue) 
            $data = array($data);
        $temp = $data;
        foreach($temp as $i => $t) {
            $data[$i] = frame::_()->getTable('shipping')->prepareOutput($data[$i]);
            $data[$i]['params'] = (array) json_decode($t['params']);    //like in frame::_extractModules() params usage
        }
        return $oneValue ? $data[0] : $data;
    }
    public function beforeStore($d = array()) {
        $d = frame::_()->getTable('shipping')->prepareInput($d);
        if(!empty($d['params']))
            $d['params'] = utils::jsonEncode($d['params']);
        return $d;
    } 
    public function store($d = array()) {
        $res = false;
        $d = $this->beforeStore($d);
        if(isset($d['code']) && empty($d['code'])) {
            $this->pushError(lang::_('Please enter Code'));
            return false;
        }
            
        if($d['id']) {
            $dbRes = frame::_()->getTable('shipping')->update($d, $d['id']);
        } else {
            $dbRes = frame::_()->getTable('shipping')->insert($d);
        }

        if($dbRes) {
            if($d['id']) {
                $res = $d['id'];
            } else {
                $res = $dbRes;
            }
        } else
            $this->pushError( frame::_()->getTable('shipping')->getErrors() );
        return $res;
    }
    public function delete($d = array()) {
        $d['id'] = (int) $d['id'];
        $res = false;
        if($d['id']) {
            if(frame::_()->getTable('shipping')->delete($d['id'])) {
                $res = $d['id'];
            } else
                $this->pushError( frame::_()->getTable('shipping')->getErrors() );
        } else 
            $this->pushError(lang::_('Invalid ID'));
        return $res;
    }
}
?>
