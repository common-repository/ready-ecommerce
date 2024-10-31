<?php

class email_templatesModel extends model {
    /**
     * Template object from database
     * 
     * @var object 
     */
    var $template;
    /**
     * template subject
     * @var string 
     */
    var $subject = '';
    /**
     * Template Message
     * @var string 
     */
    var $message = '';
    /**
     * Return the object with fields
     * @param array $d
     * @return array 
     */
    public function get($d = array()) {
        parent::get($d);
        $fields = NULL;
        if(is_numeric($d['id'])) {
            if($d['id']) {
                frame::_()->getTable('email_templates')->fillFromDB($d['id']);
            }
            $fields = frame::_()->getTable('email_templates')->getFields();
            $fields['active']->addHtmlParam('checked', (bool)$fields['active']->value);
        } elseif(!empty($d) && is_array($d)) {
            $fields = frame::_()->getTable('email_templates')->get('*', $d);
        } else {
            $fields = frame::_()->getTable('email_templates')->getAll();
            if(!empty($fields)) {
                for($i = 0; $i < count($fields); $i++) {
                    $fields[$i]['data'] = $fields[$i]['data'];
                }
            }
        }
        return $fields;
    }
    /**
     * Get all templates
     * 
     * @return array
     */
    public function getAll() {
        $templates = frame::_()->getTable('email_templates')->get('*');
        return $templates;
    }
    /**
     * Get the template by ID
     * @param int $id
     * @return object 
     */
    public function getById($id) {
        if (is_numeric($id)) {
            $conditions = array('id' => $id);
        } else {
            return false;
        }
        $templates = frame::_()->getTable('email_templates')->get('*',$conditions);
        return $templates[0];
    }
    /**
     * Update the template
     * 
     * @param array $d 
     */
    public function put($d=array()) {
        $res = new response();
        if (!empty($d)) {
            if (is_numeric($d['id'])) {
                $id = $d['id'];
                if (frame::_()->getTable('email_templates')->update($d, array('id' => $id))){
                    $res->messages[] = lang::_('Template Updated');
                    $res->data = array(
                        'id' => $id, 
                        'label' => $d['label'], 
                        'subject' => $d['subject'], 
                        'body' => $d['body'], 
                        'active' => $d['active']
                    );
                }
            } else {
                $res->errors[] = lang::_('Invalid Template');
            }
        }
        return $res;
    }
    /**
     * Get the template from database
     * @param string $module
     * @param string $template
     * @return object Email Template 
     */
    public function getTemplate($module, $template) {
        $conditions = array('module' => $module, 'name' => $template);
        $template = frame::_()->getTable('email_templates')->get('*',$conditions);
        $this->template = $template[0];
        return $this;
    }
    /**
     * Render content
     * 
     * @param array $variables 
     */
    public function renderContent($variables) {
       //$replacements = utils::jsonDecode($this->template['variables']);
       $this->message = utils::makeVariablesReplacement($this->template['body'], $variables);
       $this->subject = utils::makeVariablesReplacement($this->template['subject'], $variables);
       /*foreach($replacements as $k => $v) {
            $message = str_replace(":$v", $variables[$v], $message);
            $subject = str_replace(":$v", $variables[$v], $subject);
       } // foreach
       $this->subject = $subject;
       $this->message = $message;*/
    }
    
    public function getSubject() {
        return $this->subject;
    }
    
    public function getMessage() {
        return $this->message;
    }
}
?>
