<?php
class pages extends module {
    /**
     * Current Page data
     */
    protected $_current = null;
    
    public function __construct($d, $params = array()) {
        parent::__construct($d, $params);
        $this->_current = new stdClass();
    }
    public function init() {
        add_action('posts_results', array($this, 'initPage'));
        add_filter('wp_get_nav_menu_items', array($this, 'preMenuLinksShow'));
        add_filter('get_pages', array($this, 'preMenuLinksShow'), 0, 2);
        add_filter('wp_page_menu_args', array($this, 'parsePagesArgs'));
        add_filter('loop_end', array($this, 'checkPermissionsGlobal'));     //Global permissions check
		add_action('wp_head', array($this, 'checkSysMessages'));
		add_filter('page_link', array($this, 'overwriteProtocol'), 10, 3);			//Use https on pages that we want
        parent::init();
    }
    public function parsePagesArgs($args) {
        $args['toeUseExcludePages'] = true;
        return $args;
    }
    public function getCurrent() {
        return $this->_current;
    }
    public function initPage($posts) {
        if(count($posts) == 1) {
            if($posts[0]->post_type == 'page') {
                if($page = $this->getByID($posts[0]->ID)) {
                    $this->_current = $page;
                    remove_filter('the_content', 'wpautop');
					frame::_()->getModule($page->mod)->exec($page->action);
                }
            }
        }
        return $posts;
    }
    public function getByID($id) {
        foreach($this->_params as $p) {
            if(isset($p->page_id) && (int)$p->page_id == (int)$id)
                return $p;
        }
        return NULL;
    }
    public function getAll() {
        return $this->_params;
    }
    public function getLink($params = array('id' => 0, 'mod' => '', 'action' => '', 'data' => array())) {
        if($page = $this->getPage($params)) {
            $urlParams = array('page_id' => $page->page_id);
            if(!empty($params['data']) && is_array($params['data']))
                $urlParams = array_merge($urlParams, $params['data']);
            return uri::_($urlParams);
        }
        return '';
    }
    public function getPage($params = array('id' => 0, 'mod' => '', 'action' => '')) {
        foreach($this->_params as $key => $p) {
            if(($p->mod == $params['mod'] && $p->action == $params['action']) ||
                (isset($params['id']) && $p->page_id == $params['id'])) {

                return $p;
            }
        }
        return NULL;
    }
    public function preMenuLinksShow($items, $r = array()) {
        if(is_admin()) return $items;
        $idFieldName = 'object_id';
        if(empty($r)) {
            $r['toeUseExcludePages'] = true;
            $idFieldName = 'object_id';
        }
        if(isset($r['toeUseExcludePages']) && $r['toeUseExcludePages']) {
            $res = array();
            if(frame::_()->getModule('user')->getCurrent()) 
                $for = 'logged';
            else
                $for = 'guest';
            $included = array();
			$includedPages = array();
            foreach($items as $item) {
				$plugPage = null;
				if($item->post_type != 'page' && $item->object == 'page')	{	// If this is really page and not any taxomony or other post/tax type
					$plugPage = $this->getByID($item->$idFieldName);
					if(is_null($plugPage) /*&& !in_array($item->$idFieldName, $included)*/) {
						$res[] = $item;
						$included[] = $item->$idFieldName;
					}
					if(!empty($plugPage) 
							&& is_object($plugPage) 
							&& in_array($plugPage->showFor, array('all', $for)) 
							&& !$plugPage->hideFromMenu 
							&& !in_array($item->$idFieldName, $included)
					) {
						$res[] = $item;
						$included[] = $item->$idFieldName;
					}
				} else if($item->post_type == 'page') {
					$plugPage = $this->getByID($item->ID);
					if((!empty($plugPage) 
							&& is_object($plugPage) 
							&& in_array($plugPage->showFor, array('all', $for)) 
							&& (!isset($plugPage->hideFromMenu) || !$plugPage->hideFromMenu)
							&& !in_array($item->ID, $includedPages)) || empty($plugPage)
					) {
						$res[] = $item;
						$includedPages[] = $item->ID;
					}
				} else
					$res[] = $item;
            }
            return $res;
        }
        return $items;
    }
    public function checkLogged($params = array('id' => 0, 'mod' => '', 'action' => '', 'data' => array())) {
        $isLogged = frame::_()->getModule('user')->getCurrent();
        $currentPage = $this->getPage($params);
        if(is_object($currentPage)) {
			if($currentPage->showFor == 'logged' && !$isLogged) {
				redirect( 
					$this->getLink(array(
						'mod' => 'user', 
						'action' => 'getLoginForm', 
						'data' => array(
							'toeReturn' => $this->getLink($params),
						),
					)) 
				);
			}
        }
        return true;
    }
    public function checkPermissionsGlobal() {
        global $post;
        if(is_page($post)) {
            $currentPage = $this->getByID($post->ID);
            $this->checkLogged(array('mod' => $currentPage->mod, 'action' => $currentPage->action));
        }
    }
    /**
     * Check if current page is page describet using mod and action params in $check variable
     * @see installer::init() - $pages variable
     */
    public function is($check = array()) {
        $currentPage = $this->getCurrent();
        if(is_object($currentPage) && isset($currentPage->page_id)) {
            if(isset($check['mod']) && isset($check['action']) && $check['mod'] == $currentPage->mod && $check['action'] == $currentPage->action) {
                return true;
            }
        }
        return false;
    }
    /**
     * Check if current page is Login page
     */
    public function isLogin() {
        return $this->is(array('mod' => 'user', 'action' => 'getLoginForm'));
    }
    /**
     * Check if current page is Registration page
     */
    public function isRegistration() {
        return $this->is(array('mod' => 'user', 'action' => 'getRegisterForm'));
    }
    /**
     * Check if current page is Shopping Cart page
     */
    public function isCart() {
        return $this->is(array('mod' => 'user', 'action' => 'getShoppingCart'));
    }
    /**
     * Check if current page is Checkout Step 1 page
     */
    public function isCheckoutStep1() {
        $checkoutStep = req::getVar('step');
        if(!empty($checkoutStep) && (int) $checkoutStep != 1)
            return false;
		//Some difference - we do not have separated page for this
        if(req::getVar('mod') == 'checkout' && req::getVar('action') == 'getTotalHtml') {
            return true;
        }
        return $this->is(array('mod' => 'checkout', 'action' => 'getAllHtml'));
    }
    /**
     * Check if current page is Checkout Step 2 page
     */
    public function isCheckoutStep2() {
        //Some difference - we do not have separated page for this
        if(req::getVar('mod') == 'order' && req::getVar('action') == 'addFromCheckout') {
            return true;
        }
        $checkoutStep = req::getVar('step');
        if((int) $checkoutStep == 2 && $this->is(array('mod' => 'checkout', 'action' => 'getAllHtml')))
            return true;
        return false;
    }
    /**
     * Check if current page is Checkout Step 2 page
     */
    public function isCheckoutStep3() {
        //Some difference - we do not have separated page for this
        if(req::getVar('mod') == 'order' && req::getVar('action') == 'addSuccess') {
            return true;
        }
        $checkoutStep = req::getVar('step');
        if((int) $checkoutStep == 3 && $this->is(array('mod' => 'checkout', 'action' => 'getAllHtml')))
            return true;
        return false;
    }
    /**
     * Check if current page is Account Summary page
     */
    public function isAccountSummary() {
        return $this->is(array('mod' => 'user', 'action' => 'getAccountSummaryHtml'));
    }
    /**
     * Check if current page is Profile page
     */
    public function isProfile() {
        return $this->is(array('mod' => 'user', 'action' => 'getProfileHtml'));
    }
    /**
     * Check if current page is Orders List page
     */
    public function isOrdersList() {
        return $this->is(array('mod' => 'user', 'action' => 'getOrdersList'));
    }
    /**
     * Check if current page is Downloads List page
     */
    public function isDownloadsList() {
        return $this->is(array('mod' => 'digital_product', 'action' => 'getDownloadsList'));
    }
	/**
     * Check if current page is Categories page
     */
    public function isCategories() {
        return $this->is(array('mod' => 'products', 'action' => 'getCategoriesListHtml'));
    }
	/**
     * Check if current page is Brands page
     */
    public function isBrands() {
        return $this->is(array('mod' => 'products', 'action' => 'getBrandsListHtml'));
    }
	/**
     * Check if current page is All Products page
     */
    public function isAllProducts() {
        return $this->is(array('mod' => 'products', 'action' => 'getAllProductsListHtml'));
    }
	/**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    public function getTabs(){
        $tabs = array();
        $tab = new tab(lang::_('Pages'), $this->getCode());
        $tab->setView('pagesTab');
		$tab->setSortOrder(2);
		$tab->setParent('templates');
		$tab->setNestingLevel(1);
        $tabs[] = $tab;
        return $tabs;
    }
	/**
	 * Show messages at the top of the page. This is kinda weird way to do this, 
	 * but WP gives me no other way to do this - no action or hook right after body tag opening or somwere around this place
	 */
	public function checkSysMessages() {
		$messages = array();
		if(is_404() && frame::_()->getModule('user')->isAdmin()) {
			$messages['adminAlerts'][] = lang::_(array(
				'If you are trying to view your product and see this message - maybe you have some troubles with permalinks settings.',
				'Try to go to <a href="'. get_admin_url(). 'options-permalink.php'. '">Admin panel -> Settings -> Permalinks</a> and re-saive this settings (just click on "Save Changes").<br />',
				'<a href="http://readyshoppingcart.com/faq/ecommerce-plugin-alerts">',
				'Please check FAQ.',
				'</a>',
				
			));
		}
		if(!empty($messages)) {
			$this->getView()->assign('forAdminOnly', lang::_('This message will be visible for admin only.'));
			$this->getView()->assign('messages', $messages);
			$this->getView()->display('pagesSystemMessages');
		}
	}
	public function overwriteProtocol($link, $id, $sample) {
		static $pagesCache;
		$makeHttpsReplace = false;
		if(frame::_()->getModule('options')->get('ssl_on_checkout') || frame::_()->getModule('options')->get('ssl_on_account')) {
			if(!isset($pagesCache[ $id ])) {
				$pageParams = $this->getByID($id);
				if($pageParams == NULL)
					$pagesCache[ $id ] = false;
				else 
					$pagesCache[ $id ] = $pageParams;
			}
			if($pagesCache[ $id ] && is_object($pagesCache[ $id ])) {
				if(
					(frame::_()->getModule('options')->get('ssl_on_checkout')
					&& (($pagesCache[ $id ]->mod == 'checkout') 
						|| ($pagesCache[ $id ]->mod == 'user' && in_array($pagesCache[ $id ]->action, array('getShoppingCart')))))
					|| (frame::_()->getModule('options')->get('ssl_on_account')
					&& (($pagesCache[ $id ]->mod == 'user' && in_array($pagesCache[ $id ]->action, array('getLoginForm', 'getRegisterForm', 'getAccountSummaryHtml', 'getProfileHtml', 'getOrdersList')))
						|| ($pagesCache[ $id ]->mod == 'digital_product' && in_array($pagesCache[ $id ]->action, array('getDownloadsList')))))
				) {
					$makeHttpsReplace = true;
				}
			}
		}
		if($makeHttpsReplace) {
			$link = uri::makeHttps($link);
		}
		return $link;
	}
	public function isProductEdit() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		$get = req::get('get');
		if(empty($get))
			$get = array();
		if($scriptFile == 'post.php' // Edit post
			&& isset($get['post']) 
			&& isset($get['action']) 
			&& $get['action'] == 'edit' 
			&& function_exists('get_post_type') 
			&& get_post_type($get['post']) == S_PRODUCT // check if this is product
		) {	
			return true;
		}
		return false;
	}
	public function isProducAdd() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		$get = req::get('get');
		if(empty($get))
			$get = array();
		if($scriptFile == 'post-new.php' // Add post
			&& isset($get['post_type']) 
			&& $get['post_type'] == S_PRODUCT	// check if this is product
		) {
			return true;
		}
		return false;
	}
	public function isCategoryAddEdit() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		$get = req::get('get');
		if(empty($get))
			$get = array();
		if($scriptFile == 'edit-tags.php'
			&& isset($get['taxonomy']) 
			&& $get['taxonomy'] == S_CATEGORIES
		) {
			return true;
		}
		return false;
	}
	public function isBrandAddEdit() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		$get = req::get('get');
		if(empty($get))
			$get = array();
		if($scriptFile == 'edit-tags.php'
			&& isset($get['taxonomy']) 
			&& $get['taxonomy'] == S_BRANDS
		) {
			return true;
		}
		return false;
	}
	public function isAdminMainPage() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		if($scriptFile == 'admin.php'
			&& req::getVar('page') == frame::_()->getModule('mainmenu')->getView()->getMainSlug()
		) {
			return true;
		}
		return false;
	}
	public function isAdminOptionsPage() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		if($scriptFile == 'admin.php'
			&& req::getVar('page') == frame::_()->getModule('mainmenu')->getView()->getOptionsSlug()
		) {
			return true;
		}
		return false;
	}
	public function isAdminOrders() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		if($scriptFile == 'admin.php'
			&& req::getVar('page') == frame::_()->getModule('mainmenu')->getView()->getOrdersSlug()
		) {
			return true;
		}
		return false;
	}
	public function isAdminLog() {
		$scriptFile = basename($_SERVER['SCRIPT_NAME']);
		if($scriptFile == 'admin.php'
			&& req::getVar('page') == frame::_()->getModule('mainmenu')->getView()->getLogSlug()
		) {
			return true;
		}
		return false;
	}
}

