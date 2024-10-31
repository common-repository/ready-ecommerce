<?php
class weight extends unitsModule {
    protected $_units = array(
        'lb' => array('k' => array('kg' => 0.45359237,
                                    'oz' => 16,
									'g' => 453.59237)),
		
        'kg' => array('k' => array('lb' => 2.20462262,
                                    'oz' => 35.273944,
									'g' => 1000)),
		
        'oz' => array('k' => array('lb' => 0.0625,
                                    'kg' => 0.02834954,
									'g' => 028.34954)),
		
		'g' => array('k' => array('oz' => 0.00220462262,
									'lb' => 0.0000625,
                                    'kg' => 0.001)),
    );
}