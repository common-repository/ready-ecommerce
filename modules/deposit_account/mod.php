<?php
class deposit_account extends paymentModule {
    protected $_haveSuccessHtml = true;
    public function getSuccessHtml() {
        return $this->getView()->getOnSuccessHtml(array('payText' => $this->_params->payText));
    }
}
?>
