<?php
/**
 * Helper class to work with units, like weight or size
 */
abstract class unitsModule extends module {
    /**
     * Data units, @example: array(
            'lb' => array('k' => array('kg' => 0.45359237,
                                        'oz' => 16)),
            'kg' => array('k' => array('lb' => 2.20462262,
                                        'oz' => 35.273944)),
            'oz' => array('k' => array('lb' => 2.20462262,
                                        'kg' => 0.02834954)),
        );
     * 
     */
    protected $_units = array();
    public function convert($val, $from, $to) {
        if($from == $to || !isset($this->_units[ $from ]['k'][ $to ]))
            return $val;
        if(is_array($val)) {
            $res = array();
            foreach($val as $k => $v) {
                $res[ $k ] = $this->convert($v, $from, $to);
            }
            return $res;
        } else {
            return ($val * $this->_units[ $from ]['k'][ $to ]);
        }
    }
    public function getUnits() {
        return $this->_units;
    }
}
?>