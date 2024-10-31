<?php
class size extends unitsModule {
    protected $_units = array(
        'inch' => array('k' => array('m' => 0.0254,
									'cm' => 2.54,
									'mm' => 25.4)),
        
		'm' => array('k' => array('inch' => 39.3701,
								'cm' => 100,
								'mm' => 1000)),
		
		'cm' => array('k' => array('inch' => 0.393701,
								'm' => 100,
								'mm' => 0.1)),
		
		'mm' => array('k' => array('inch' => 0.0393701,
								'm' => 1000,
								'cm' => 10)),
    );
}