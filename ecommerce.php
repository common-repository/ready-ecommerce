<?php
/**
 * 
 * Plugin Name: Ready! Ecommerce
 * Plugin URI: http://readyshoppingcart.com/
 * Description: Shopping cart / E-commerce WP plugin. Easy to setup your own online shop. Powerfull functionality, no programming skill require. Include free themes.
 * Version: 0.6.0
 * Author: readyshoppingcart.com
 * Author URI: http://readyshoppingcart.com
 * 
 **/
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
    importClass('db');
    importClass('installer');
    importClass('baseObject');
    importClass('module');
    importClass('model');
    importClass('view');
    importClass('controller');
    importClass('helper');
    importClass('tab');
    importClass('dispatcher');
    importClass('field');
    importClass('table');
    importClass('frame');
    importClass('lang');
    importClass('req');
    importClass('uri');
    importClass('html');
    importClass('response');
    importClass('fieldAdapter');
    importClass('shippingModule');
    importClass('paymentModule');
    importClass('validator');
    importClass('errors');
    importClass('utils');
    importClass('modInstaller');
    importClass('filegenerator');
    importClass('fileuploader');
    importClass('simpleImage');
    importClass('csvgenerator');
    importClass('SxGeo');
    importClass('XMLParser');
    importClass('unitsModule');
	importClass('toeWordpressWidget');
	importClass('toeLegacy');
	importClass('installerDbUpdater');
	importClass('recapcha',			S_HELPERS_DIR. 'recapcha.php');
	importClass('mobileDetect',		S_HELPERS_DIR. 'mobileDetect.php');

    installer::update();
    errors::init();
	toeLegacy::init();

    dispatcher::doAction('onBeforeRoute');
    frame::_()->parseRoute();
    dispatcher::doAction('onAfterRoute');

    dispatcher::doAction('onBeforeInit');
    frame::_()->init();
    dispatcher::doAction('onAfterInit');
	
    dispatcher::doAction('onBeforeExec');
    frame::_()->exec();
    dispatcher::doAction('onAfterExec');