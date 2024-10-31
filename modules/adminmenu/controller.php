<?php
class adminmenuController extends controller {
    public function sendMailToDevelopers() {
        $res = new response();
        $data = req::get('post');
        $fields = array(
            'name' => new field('name', lang::_('Your name field is required.'), '', '', 'Your name', 0, array(), 'notEmpty'),
            'website' => new field('website', lang::_('Your website field is required.'), '', '', 'Your website', 0, array(), 'notEmpty'),
            'email' => new field('email', lang::_('Your e-mail field is required.'), '', '', 'Your e-mail', 0, array(), 'notEmpty, email'),
            'subject' => new field('subject', lang::_('Subject field is required.'), '', '', 'Subject', 0, array(), 'notEmpty'),
            'category' => new field('category', lang::_('You must select a valid category.'), '', '', 'Category', 0, array(), 'notEmpty'),
            'message' => new field('message', lang::_('Message field is required.'), '', '', 'Message', 0, array(), 'notEmpty'),
        );
        foreach($fields as $f) {
            $f->setValue($data[$f->name]);
            $errors = validator::validate($f);
            if(!empty($errors)) {
                $res->addError($errors);
            }
        }
        if(!$res->error) {
            $msg = 'Message from: '. get_bloginfo('name').', Host: '. $_SERVER['HTTP_HOST']. '<br />';
            foreach($fields as $f) {
                $msg .= '<b>'. $f->label. '</b>: '. nl2br($f->value). '<br />';
            }
			$headers[] = 'From: '. $fields['name']->value. ' <'. $fields['email']->value. '>';
			add_filter('wp_mail_content_type', array(frame::_()->getModule('messenger'), 'mailContentType'));
            wp_mail('ukrainecmk@ukr.net, simon@readyshoppingcart.com, support@readyecommerce.zendesk.com', 'Ready Ecommerce Contact Dev', $msg, $headers);
            $res->addMessage(lang::_('Done'));
        }
        $res->ajaxExec();
    }
}
?>
