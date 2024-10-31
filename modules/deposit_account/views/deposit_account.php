<?php
class deposit_accountView extends view {
    public function getOnSuccessHtml($d = array()) {
        $this->assign('payText', $d['payText']);
        return parent::getContent('success');
    }
}
?>
