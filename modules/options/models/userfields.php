<?php
// we need to use extrafields model
require_once 'extrafields.php';

class userfieldsModel extends extrafieldsModel {  
    /**
     * Function to get all the user extra fields
     * @param array $d
     * @return array
     */
    public function get($d=array()) {
        $condition = "parent = '". S_USER. "'";
        if(isset($d['activeOnly']) && $d['activeOnly']) {
            $condition .= ' AND '. frame::_()->getTable('extrafields')->alias(). '.active = 1';
        }
        $items = parent::get($d, $condition, S_USER);
        if (isset($d['id']) && is_numeric($d['id'])) {
            $items['destination']->adapt['html'] = 'userFieldDestHtml';
            $items['destination']->html = 'checkboxlist';
            $validationMethods = array_merge(array('' => lang::_('noneValidation')), validator::getUserValidationMethods());
            $items['validate']->addHtmlParam('options', $validationMethods);
        }
        return $items;
    }
    /**
     * Function to update user extra fields
     * @param array $d
     * @return array 
     */
    public function put($d = array()) {
        $d['parent'] = S_USER;
        if(!isset($d['active']))
            $d['active'] = 0;
        else 
            $d['active'] = (int) $d['active'];
		if(isset($d['mandatory']))
			$d['mandatory'] = 1;
        return parent::put($d);
    }
    /**
     * Function to store user extra fields
     * @param array $d
     * @return array 
     */
    public function post($d = array()) {
        $d['parent'] = S_USER;
        $d['ordering'] = 0;
		if(isset($d['mandatory']))
			$d['mandatory'] = 1;
        return parent::post($d);
    }
    public function validate($d = array()) {
        $d['allFields'] = $this->get(array('activeOnly' => 1));
        if(isset($d['useFor'])) {
            $tempFields = $d['allFields'];
            foreach($tempFields as $i => $f) {
                $destination = utils::jsonDecode($f['destination']);
                if(!in_array($d['useFor'], $destination)) {     //check only needed fields - shipping, billing or registration
                    unset($d['allFields'][$i]);
                } elseif(!in_array($d['allFields'][$i]['htmltype_id'], array(1))) {	//additional validation available for text field type only
					$d['allFields'][$i]['validate'] = false;
                }
				
            }
        }
        return parent::validate($d);
    } 
}
?>
