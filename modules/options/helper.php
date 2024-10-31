<?php
/**
 * optionsHelper class for field's elements
 */
class optionsHelper extends toechelper {
    /**
     * Get the extra field options from database
     * @param int $ef_id 
     * @return array
     */
    public function getOptions($ef_id) {
        $options = array();
        if (is_numeric($ef_id)) {
            $items = frame::_()->getTable('extraoptions')->get('id,value','ef_id ='.$ef_id);
        } else {
            return $options;
        }
        foreach ($items as $item) {
            $options[$item['id']] = $item['value'];
        }
        return $options;
    }
}