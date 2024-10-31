<?php
/**
 * Base of Payment Modules Engine
 * Maybe will be like interface? ......
 */
abstract class paymentModule extends module {
    protected $_haveProcessHtml = false;
    protected $_haveSuccessHtml = false;
    protected $_inputFields = array(
        //'cc_owner' => array('type' => 'text', 'label' => 'Card Owner', 'validate' => 'notNull'),
    );
    protected $_gatewayUrl = '';
    public function getGatewayUrl() {
        return $this->_gatewayUrl;
    }
    public function getProcessHtml() {
        return '';
    }
    public function haveProcessHtml() {
        return $this->_haveProcessHtml;
    }
    public function haveSuccessHtml() {
        return $this->_haveSuccessHtml;
    }
    public function process() {
        
    }
    public function getSuccessHtml() {
        return '';
    }
    public function sendTransactionToGateway($url, $parameters) {
        $server = parse_url($url);

        if (!isset($server['port'])) {
            $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
        }

        if (!isset($server['path'])) {
            $server['path'] = '/';
        }

        if (isset($server['user']) && isset($server['pass'])) {
            $header[] = 'Authorization: Basic ' . base64_encode($server['user'] . ':' . $server['pass']);
        }
        $result = false;
        if (function_exists('curl_init')) {
            $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
            curl_setopt($curl, CURLOPT_PORT, $server['port']);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);

            $result = curl_exec($curl);

            curl_close($curl);
        }

        return $result;
    }
    function formatRaw($number, $currency_code = '', $currency_value = '') {
        /*global $currencies, $currency;

        if (empty($currency_code) || !$this->is_set($currency_code)) {
            $currency_code = $currency;
        }

        if (empty($currency_value) || !is_numeric($currency_value)) {
            $currency_value = $currencies->currencies[$currency_code]['value'];
        }

        return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');*/
    }
    protected function _prepareParams() {
        if(is_array($this->_params) && isset($this->_params[0]))
            $this->_params = $this->_params[0];
        if(empty($this->_params) && !is_object($this->_params))
            $this->_params = new stdClass();
    }
    public function init() {
        parent::init();
        $this->_prepareParams();
    }
    public function validateInput($d = array()) {
        $res = new response();
        foreach($this->_inputFields as $key => $attrs) {
            if(!isset($attrs['validate'])) continue;
            $action = '';
            switch($attrs['validate']) {
                case 'notNull':
                    if(empty($d[$key])) {
                        $action = in_array($d[$key]['type'], array('selectbox', 'radiobuttons', 'checkboxlist', 'selectlist')) ? 'Please select' : 'Please enter';
                        $res->addError(array(
                            $key => lang::_($action). ' '. lang::_($attrs['label']
                        )));
                    }
                    break;
            }
        }
        return $res;
    }
    public function getInputFields() {
        return $this->_inputFields;
    }
    /**
     * Validate responce from payment gateway
     */
    public function validateResponce() {
        return true;
    }
	public function getAdditionalParams() {
		$this->_additionalParams = array(
			'success_order_status' => array(
				'type' => 'radiobuttons', 
				'label' => 'Success order status', 
				'value' => '', 
				'htmlParams' => array('options' => frame::_()->getTable('orders')->getField('status')->getHtmlParam('options'))),
		);
		return $this->_additionalParams;
	}
}

