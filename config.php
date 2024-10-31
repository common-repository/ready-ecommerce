<?php
    global $wpdb;
    if (WPLANG == '') {
        define('S_WPLANG', 'en_GB');
    } else {
        define('S_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('S_PLUG_NAME', basename(dirname(__FILE__)));
    define('S_DIR', WP_PLUGIN_DIR. DS. S_PLUG_NAME. DS);
    define('S_CLASSES_DIR', S_DIR. 'classes'. DS);
    define('S_TABLES_DIR', S_CLASSES_DIR. 'tables'. DS);
	define('S_HELPERS_DIR', S_CLASSES_DIR. 'helpers'. DS);
    define('S_LANG_DIR', S_DIR. 'lang'. DS);
    define('S_IMG_DIR', S_DIR. 'img'. DS);
    define('S_TEMPLATES_DIR', S_DIR. 'templates'. DS);
    define('S_MODULES_DIR', S_DIR. 'modules'. DS);
    define('S_FILES_DIR', S_DIR. 'files'. DS);
    define('S_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('S_SITE_URL', get_bloginfo('wpurl'). '/');
    define('S_JS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/js/');
    define('S_CSS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/css/');
    define('S_IMG_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/img/');
    define('S_MODULES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/modules/');
    define('S_TEMPLATES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/templates/');
    define('S_JS_DIR', S_DIR. 'js/');
	
    define('S_URL', S_SITE_URL);

    define('S_LOADER_IMG', S_IMG_PATH. 'loading-cube.gif');
    define('S_DATE_DL', '/');
    define('S_DATE_FORMAT', 'd/m/Y');
    define('S_DATE_FORMAT_HIS', 'm/d/Y (H:i:s)');	// Month at First!!!
    define('S_DATE_FORMAT_HIS_WB', 'm-d-Y H:i:s');	// Month at First!!!
    define('S_DATE_FORMAT_JS', 'dd/mm/yy');
    define('S_DATE_FORMAT_CONVERT', '%d/%m/%Y');
    define('S_WPDB_PREF', $wpdb->prefix);
    define('S_DB_PREF', 'toe_');    /*TheOneEcommerce*/
    define('S_MAIN_FILE', 'ecommerce.php');

    define('S_DEFAULT', 'default');
    define('S_CURRENT', 'current');
    define('S_PRODUCT', 'product');
	define('S_VARIATION', 'variation');
    define('S_CURRENCY', 'currency');
    define('S_PLUGIN_INSTALLED', true);
    define('S_VERSION', '0.6.0');
    define('S_USER', 'user');
	
	define('S_CATEGORIES', 'products_categories');
	define('S_BRANDS', 'products_brands');
    
    define('S_TIM_PATH', plugin_dir_url(__FILE__).'modules/timthumb.php?src=');
    define('S_CLASS_PREFIX', 'toec');        
    define('S_FREE_VERSION', false);
    
    define('S_API_UPDATE_URL', '');
    
    define('S_SUCCESS', 'Success');
    define('S_FAILED', 'Failed');
	define('S_ERRORS', 'toeErrors');
	
	define('S_THEME_MODULES', 'theme_modules');
	define('S_TOE', 'toe');
	
	define('S_ADMIN',	'admin');
	define('S_LOGGED',	'logged');
	define('S_GUEST',	'guest');
	
	define('S_ALL',		'all');
	
	define('S_METHODS',		'methods');
	define('S_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('S_CODE', 'toe');
	
	define('S_MYSQL_MAX_INT', 2147483646);	// Actually it's 2147483647, but we will take 2147483646
	/**
	 * Plugin name
	 */
	define('S_WP_PLUGIN_NAME', 'Ready! Ecommerce');
	/**
	 * End of Line
	 */
	define('S_EOL', "\n");