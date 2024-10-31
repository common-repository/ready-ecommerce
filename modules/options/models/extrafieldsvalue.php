<?php
class extrafieldsvalueModel extends model {
    /**
     * Store extra field value to database
     * @param array $d
     * @return int 
     */
    public function insert($d=array()) {
        if (!empty($d)) {
            $id = frame::_()->getTable('extrafieldsvalue')->insert($d);
        } else {
            return false;
        }
        if ($id) {
            return $id;
        } else {
            return false;
        }
    }
    /**
     * Updates extra fields values in database
     * @param array $d
     * @return int 
     */
    public function update($d=array(),$where) {
        if (!empty($d)) {
            $id = frame::_()->getTable('extrafieldsvalue')->update($d, $where);
            return $id;
        } else {
            return false;
        }
    }
    /**
     * Returns the value of extra field
     * @param int $parent_id
     * @param string $parent_type
     * @param id $ef_id 
     */
    public function get($parent_id, $parent_type, $ef_id) {
		if(is_array($ef_id)) {
			$conditions = sprintf("parent_id = %d AND parent_type= '%s' AND ef_id IN (%s)", $parent_id, $parent_type, implode(',', $ef_id));
		} else {
			$conditions = sprintf("parent_id = %d AND parent_type= '%s' AND ef_id = %d", $parent_id, $parent_type, $ef_id);
		}
        $value = frame::_()->getTable('extrafieldsvalue')->get('value', $conditions);
        if (!empty($value)) {
            return is_array($ef_id) ? $value : $value[0]['value'];
        } else {
            return false;
        }
        
    }
    /**
     * Store extra fields for given parent
     * 
     * @param array $post
     * @param array $fields
     * @param int $parent_id
     * @param string $parent_type 
     */
    public function saveExtraFields($post, $fields, $parent_id, $parent_type){
        foreach($fields as $key => $f) {
            if(!isset($post[$key]))
                continue;
			if(!isset($post['extra_field']))
				$post['extra_field'] = array();
            $ef_id = isset($post['extra_field'][$key]) ? $post['extra_field'][$key] : 0;
            if(empty($ef_id) && is_object($f) && isset($f->id))
               $ef_id = $f->id;
            $data = array(
                'parent_id' => $parent_id,
                'parent_type' => $parent_type,
                'value' => is_array($post[$key]) ? utils::jsonEncode(toeMultArrayMap('htmlspecialchars', $post[$key])) : htmlspecialchars($post[$key]),
                'ef_id' => $ef_id,
            );
            $oldValue = $this->get($parent_id, $parent_type, $ef_id);
            if ($oldValue !== false) {
                $conditions = array(
                    'parent_id' => $parent_id,
                    'parent_type' => $parent_type,
                    'ef_id' => $ef_id,
                );
                $this->update($data, $conditions);
            } else {
                $this->insert($data);
            }
        }
    }
   
}
?>