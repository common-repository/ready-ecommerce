<?php

class messengerController extends controller {
    /**
     * Sends the email to reciever
     * @global type $phpmailer
     * @param string $to
     * @param string $module
     * @param string $template
     * @param array $variables 
     */
    public function sendNotification($to, $module, $template, $variables) {
        //global $phpmailer;
        $this->getModule()->addContentTypeFilter();
        $template = frame::_()->getModule('messenger')->getModel('email_templates')->getTemplate($module, $template);
        
        $template->renderContent($variables);

        $subject = $template->getSubject();
        $message = $template->getMessage();
        
        $headers = '';
        $fromName = frame::_()->getModule('options')->get('store_name');
        $fromAddress = '';
        if(frame::_()->getModule('options')->get('use_store_email_as_from')) {
            $fromAddress = frame::_()->getModule('options')->get('store_email');
        } else {
            $host = utils::getHost();
            if(strpos($host, '.')) {
                $fromAddress = 'noreply@'. $host;
            }
        }
        if(!empty($fromName) && !empty($fromAddress)) {
            $headers = 'From: '. $fromName. ' <'. $fromAddress. '>' . "\r\n";
        }
        $result = wp_mail($to, $subject, $message, $headers);
        frame::_()->getModule('log')->getModel()->post(array(
            'type' => 'email',
            'data' => array(
                'to' => $to,
                'subject' => $subject,
                'headers' => htmlspecialchars($headers),
                'message' => $message,
                'result' => $result ? S_SUCCESS : S_FAILED,
            ),
        ));
    }
    /**
     * Get Edit Template Form
     * 
     * @return response 
     */
    public function getEditTemplate() {
        $res = new response();
        $res->html = $this->getView('messengerTab')->editTemplate();
        return $res->ajaxExec();
    }
    /**
     * Save the template
     * 
     * @return response 
     */
    public function putTemplate() {
        $res = new response();
        $res = $this->getModel('email_templates')->put(req::get('post'));
        return $res->ajaxExec();
    }
    /**
     * Add New Subscriber
     * @return response 
     */
    public function postSubscriber(){
        $res = new response();
        $res = $this->getModel('subscribers')->post(req::get('post'));
        return $res->ajaxExec();
    }
}
?>
