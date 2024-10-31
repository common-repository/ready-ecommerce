<?php
class userfieldsView extends view {
    public function editUserfield($someWPVar = '', $show = true) {
        return $this->userfieldsForm(array('id' => req::getVar('id')));
    }
    public function addUserfields() {
        return $this->userfieldsForm();
    }
    public function userfieldsForm($d = array()) {
        $id = (isset($d['id']) && !empty($d['id'])) ? (int) $d['id'] : 0;
        $method = $id ? 'put' : 'post';
        $userfields = frame::_()->getModule('options')->getModel('userfields')->get(array('id' => $id));
        $this->assign('paramsHtml', $this->_getParamsHtml(array_merge($userfields, array('id' => $id))));
        $this->assign('userfields', $userfields);
        $this->assign('id', $id);
        if($method == 'post')
            $this->assign('show_field', req::getVar('show_field'));
        $this->assign('method', $method);
        $tpl = 'addEditUserfield';
        if(isset($show))
            parent::display($tpl);
        else
            return parent::getContent($tpl);
    }
    protected function _getParamsHtml($d = array()) {
        $this->assign('userfields', $d);
        return parent::getContent('editUserfieldParams');
    }
}

