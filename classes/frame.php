<?php
class frame {
    private $_modules = array();
    private $_tables = array();
    private $_allModules = array();
    /**
     * bool Uses to know if we are on one of the plugin pages
     */
    private $_inPlugin = false;
    /**
     * Array to hold all scripts and add them in one time in addScripts method
     */
    private $_scripts = array();
    private $_scriptsInitialized = false;
    private $_styles = array();
	private $_stylesInitialized = false;
    
    private $_scriptsVars = array();
    private $_mod = '';
    private $_action = '';
	static public function getInstance() {
        static $instance;
        if(!$instance) {
            $instance = new frame();
        }
        return $instance;
    }
    static public function _() {
        return self::getInstance();
    }
    public function parseRoute() {
		$pl = req::getVar('pl');
		// If there are pl in request - then this is request for our other plugin, not Ready! Ecommerce, 
		// as this plugin use clear requests - just mod and action, without pl
		if(!empty($pl))
			return;
        $mod = req::getMode();
        if($mod)
            $this->_mod = $mod;
        $action = req::getVar('action');
        if($action)
            $this->_action = $action;
    }
    public function setMod($mod) {
        $this->_mod = $mod;
    }
    public function setAction($action) {
        $this->_action = $action;
    }
    protected function _extractModules() {
        $activeModules = $this->getTable('modules')
                ->innerJoin( $this->getTable('modules_type'), 'type_id' )
                ->get($this->getTable('modules')->alias(). '.*, '. $this->getTable('modules_type')->alias(). '.label as type_name');
		if($activeModules) {
			$modsWithTables = array('affiliate', 'catalog', 'check_payment', 'googlecheckout',
				'intuit_scheduled', 'shipping_per_item', 'coupons', 'gifts', 'special_products', 
				'stripe', 'translator', 'ready_tpl_mod');
            foreach($activeModules as $m) {
                $code = $m['code'];
                $moduleLocationDir = S_MODULES_DIR;
                if(!empty($m['ex_plug_dir'])) {
                    $moduleLocationDir = utils::getExtModDir( $m['ex_plug_dir'] );
				}
                if(is_dir($moduleLocationDir. $code)) {
                    $this->_allModules[$m['code']] = 1;
                    if((bool)$m['active']) {
                        importClass($code, $moduleLocationDir. $code. DS. 'mod.php');
                        $moduleClass = toeGetClassName($code);
						if(class_exists($moduleClass)) {
							$this->_modules[$code] = new $moduleClass($m);
							$this->_modules[$code]->setParams((array)json_decode($m['params']));
							if(in_array($code, $modsWithTables) && is_dir($moduleLocationDir. $code. DS. 'tables')) {
								$this->_extractTables($moduleLocationDir. $code. DS. 'tables'. DS);
							}
						}
                    }
                }
            }
        }
    }
    protected function _initModules() {
        if(!empty($this->_modules)) {
            foreach($this->_modules as $mod) {
                 $mod->init();
            }
        }
    }
    public function init() {
        lang::init();
        req::init();
        
        $this->_extractModules();
        $this->_initModules();
		modInstaller::checkActivationMessages();
        
        $this->_execModules();

        add_action('init', array($this, 'addScripts'));
        add_action('init', array($this, 'addStyles'));
        
        register_activation_hook(S_DIR. DS. S_MAIN_FILE, array('installer', 'init')); //See classes/install.php file
        register_deactivation_hook(S_DIR. DS. S_MAIN_FILE, array('installer', 'delete'));
        
        add_action('admin_notices', array('errors', 'displayOnAdmin'));
    }
	/**
	 * Check permissions for action in controller by $code and made corresponding action
	 * @param string $code Code of controller that need to be checked
	 * @param string $action Action that need to be checked
	 * @return bool true if ok, else - should exit from application
	 */
	public function checkPermissions($code, $action) {
		if($this->havePermissions($code, $action))
			return true;
		else {
			exit(lang::_e('You have no permissions to view this page'));
		}
	}
	/**
	 * Check permissions for action in controller by $code
	 * @param string $code Code of controller that need to be checked
	 * @param string $action Action that need to be checked
	 * @return bool true if ok, else - false
	 */
	public function havePermissions($code, $action) {
		$res = true;
		$mod = $this->getModule($code);
		$action = strtolower($action);
		if($mod) {
			$permissions = $mod->getController()->getPermissions();
			if(!empty($permissions)) {	// Special permissions
				if(isset($permissions[S_METHODS]) 
					&& !empty($permissions[S_METHODS])
					
				) {
					foreach($permissions[S_METHODS] as $method => $perm) {	// Make case-insensitive
						$permissions[S_METHODS][strtolower($method)] = $perm;
					}
					if(array_key_exists($action, $permissions[S_METHODS])) {		// Permission for this method exists
						$currentUserPosition = frame::_()->getModule('user')->getCurrentUserPosition();
						if((is_array($permissions[ S_METHODS ][ $action ]) && !in_array($currentUserPosition, $permissions[ S_METHODS ][ $action ]))
							|| (!is_array($permissions[ S_METHODS ][ $action ]) && $permissions[S_METHODS][$action] != $currentUserPosition)
						) {
							$res = false;
						}
					}
				}
				if(isset($permissions[S_USERLEVELS])
					&& !empty($permissions[S_USERLEVELS])
				) {
					$currentUserPosition = frame::_()->getModule('user')->getCurrentUserPosition();
					// For multi-sites network admin role is undefined, let's do this here
					if(is_multisite() && is_admin()) {
						$currentUserPosition = S_ADMIN;
					}
					foreach($permissions[S_USERLEVELS] as $userlevel => $methods) {
						if(is_array($methods)) {
							$lowerMethods = array_map('strtolower', $methods);			// Make case-insensitive
							if(in_array($action, $lowerMethods)) {						// Permission for this method exists
								if($currentUserPosition != $userlevel) 
									$res = false;
								break;
							}
						} else {
							$lowerMethod = strtolower($methods);			// Make case-insensitive
							if($lowerMethod == $action) {					// Permission for this method exists
								if($currentUserPosition != $userlevel) 
									$res = false;
								break;
							}
						}
					}
				}

			}
		}
		return $res;
	}
    protected function _execModules() {
        if($this->_mod) {
			// If module exist and is active
            $mod = $this->getModule($this->_mod);
            if($mod && $this->_action) {
				if($this->checkPermissions($this->_mod, $this->_action)) {
					switch(req::getVar('reqType')) {
						case 'ajax':
							add_action('wp_ajax_'. $this->_action, array($mod->getController(), $this->_action));
							add_action('wp_ajax_nopriv_'. $this->_action, array($mod->getController(), $this->_action));
							break;
						default:
							$mod->exec($this->_action);
							break;
					}
				}
            }
        }
    }
    protected function _extractTables($tablesDir = S_TABLES_DIR) {
        $mDirHandle = opendir($tablesDir);
        while(($file = readdir($mDirHandle)) !== false) {
            if(is_file($tablesDir. $file) && $file != '.' && $file != '..' && strpos($file, '.php')) {
                $this->_extractTable( str_replace('.php', '', $file), $tablesDir );
            }
        }
    }
    protected function _extractTable($tableName, $tablesDir = S_TABLES_DIR) {
        importClass('noClassNameHere', $tablesDir. $tableName. '.php');
        $this->_tables[$tableName] = table::_($tableName);
    }
    /**
     * public alias for _extractTables method
     * @see _extractTables
     */
    public function extractTables($tablesDir) {
        if(!empty($tablesDir))
            $this->_extractTables($tablesDir);
    }
    public function exec() {
        /**
         * @deprecated
         */
        /*if(!empty($this->_modules)) {
            foreach($this->_modules as $mod) {
                $mod->exec();
            }
        }*/
    }
    public function getTables () {
        return $this->_tables;
    }
    /**
     * Return table by name
     * @param string $tableName table name in database
     * @return object table
     * @example frame::_()->getTable('products')->getAll()
     */
    public function getTable($tableName) {
        if(empty($this->_tables[$tableName])) {
            $this->_extractTable($tableName);
        }
        return $this->_tables[$tableName];
    }
    public function getModules($filter = array()) {
        $res = array();
        if(empty($filter))
            $res = $this->_modules;
        else {
            foreach($this->_modules as $code => $mod) {
                if(isset($filter['type'])) {
                    if(is_numeric($filter['type']) && $filter['type'] == $mod->getTypeID())
                        $res[$code] = $mod;
                    elseif($filter['type'] == $mod->getType())
                        $res[$code] = $mod;
                }
            }
        }
        return $res;
    }
    
    public function getModule($code) {
        return (isset($this->_modules[$code]) ? $this->_modules[$code] : NULL);
    }
    public function inPlugin() {
        return $this->_inPlugin;
    }
    /**
     * Push data to script array to use it all in addScripts method
     * @see wp_enqueue_script definition
     */
    public function addScript($handle, $src = '', $deps = array(), $ver = false, $in_footer = false, $vars = array()) {
		$src = empty($src) ? $src : uri::_($src);
        if($this->_scriptsInitialized) {
            wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
        } else {
            $this->_scripts[] = array(
                'handle' => $handle, 
                'src' => $src, 
                'deps' => $deps, 
                'ver' => $ver, 
                'in_footer' => $in_footer,
                'vars' => $vars
            );
        }
    }
    /**
     * Add all scripts from _scripts array to wordpress
     */
    public function addScripts() {
        if(!empty($this->_scripts)) {
            foreach($this->_scripts as $s) {
                wp_enqueue_script($s['handle'], $s['src'], $s['deps'], $s['ver'], $s['in_footer']);
                
                if($s['vars'] || isset($this->_scriptsVars[$s['handle']])) {
                    $vars = array();
                    if($s['vars'])
                        $vars = $s['vars'];
                    if($this->_scriptsVars[$s['handle']])
                        $vars = array_merge($vars, $this->_scriptsVars[$s['handle']]);
                    if($vars) {
                        foreach($vars as $k => $v) {
                            wp_localize_script($s['handle'], $k, $v);
                        }
                    }
                }
            }
        }
        $this->_scriptsInitialized = true;
    }
    public function addJSVar($script, $name, $val) {
        if($this->_scriptsInitialized) {
            wp_localize_script($script, $name, $val);
        } else {
            $this->_scriptsVars[$script][$name] = $val;
        }
    }
    
    public function addStyle($handle, $src = false, $deps = array(), $ver = false, $media = 'all') {
		$src = empty($src) ? $src : uri::_($src);
		if($this->_stylesInitialized) {
			wp_enqueue_style($handle, $src, $deps, $ver, $media);
		} else {
			$this->_styles[] = array(
				'handle' => $handle,
				'src' => $src,
				'deps' => $deps,
				'ver' => $ver,
				'media' => $media 
			);
		}
    }
    public function addStyles() {
        if(!empty($this->_styles)) {
            foreach($this->_styles as $s) {
                wp_enqueue_style($s['handle'], $s['src'], $s['deps'], $s['ver'], $s['media']);
            }
        }
		$this->_stylesInitialized = true;
    }
    //Wery interesting thing going here.............
    public function loadPlugins() {
        require_once(ABSPATH. 'wp-includes/pluggable.php'); 
        //require_once(ABSPATH.'wp-load.php');
        //load_plugin_textdomain('some value');
    }
    public function loadWPSettings() {
        require_once(ABSPATH. 'wp-settings.php'); 
    }
    public function moduleActive($code) {
        return isset($this->_modules[$code]);
    }
    public function moduleExists($code) {
        if($this->moduleActive($code))
            return true;
        return isset($this->_allModules[$code]);
    }
	public function isTplEditor() {
		$tplEditor = req::getVar('tplEditor');
		return (bool) $tplEditor;
	}
	public function getModuleById($id) {
		foreach($this->_modules as $m) {
			if($m->getID() == $id)
				return $m;
		}
		return false;
	}
	public function isAdminPlugPage() {
		global $post;
		$page = req::getVar('page');
		$taxonomy = req::getVar('taxonomy');
		$postType = req::getVar('post_type');
		$postId = (int)req::getVar('post');
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		if(($page && in_array($page, array('ready-ecommerce', 'toeoptions', 'orders', 'toelog', 'toetranslator')))
			|| ($taxonomy && in_array($taxonomy, array(S_CATEGORIES, S_BRANDS)))
			|| ($postType && in_array($postType, array(S_PRODUCT)))
			|| ($post && is_object($post) && isset($post->post_type) && in_array($post->post_type, array(S_PRODUCT)))
			|| ($scriptFile == 'post.php' && $postId)	// In some cases - we need to call this before global $post; is initialized
		) {
			return true;
		}
		return false;
	}
}
