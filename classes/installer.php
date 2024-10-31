<?php
    class installer {
        static public $update_to_version_method = '';
        static public function init() {
            global $wpdb;
			//$start = microtime(true);					// Speed debug info
			//$queriesCountStart = $wpdb->num_queries;	// Speed debug info
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $current_version = get_option('re_db_version', 0);
            $installed = (int) get_option('re_db_installed', 0);
            /*if($installed) {
                if ($current_version) {
                    if (version_compare(S_VERSION, $current_version,'>')) {
                        $update_to_version_method = 'update_'.str_replace('.','',S_VERSION);
                        if (method_exists($this, $update_to_version_method)) {
                            return self::$update_to_version_method();
                        } else {
                            return true;
                        }
                    }
                } else {
                    add_option('re_db_version', '0.0.1','','no');
                }
            }*/
            
            /**
             * cart table
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."cart")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."cart` (
					  `user_id` int(11) NOT NULL,
					  `content` text NOT NULL,
					  `updated` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`user_id`)
					) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            /**
			 * currency 
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."currency")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."currency` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `code` varchar(32) NOT NULL,
				  `label` varchar(128) NOT NULL,
				  `value` float(9,3) NOT NULL DEFAULT '1.000',
				  `symbol` varchar(16) DEFAULT NULL,
				  `symbol_left` varchar(32) DEFAULT NULL,
				  `symbol_right` varchar(32) DEFAULT NULL,
				  `symbol_point` varchar(2) DEFAULT NULL,
				  `symbol_thousand` varchar(2) DEFAULT NULL,
				  `decimal_places` smallint(2) NOT NULL DEFAULT '2',
				  `use_as_default` tinyint(1) NOT NULL DEFAULT '0',
				  `currency_view` varchar(32) DEFAULT NULL,
				  `price_view` varchar(32) DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."currency` VALUES
                (1, 'USD', 'US Dollar', 1, '$', '', ' \$', ',', ' ', 2, 1, '1$', '100 000,00'),
                (2, 'EUR', 'Euro', 1.23, 'EUR', '', ' EUR', ',', ' ', 2, 0, '1EUR', '100 000,00')");
            /**
			 * htmltype 
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."htmltype")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."htmltype` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(32) NOT NULL,
				  `description` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `label` (`label`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."htmltype` ADD UNIQUE INDEX `label` (`label`)");
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."htmltype` VALUES
                (1, 'text', 'Text'),
                (2, 'password', 'Password'),
                (3, 'hidden', 'Hidden'),
                (4, 'checkbox', 'Checkbox'),
                (5, 'checkboxlist', 'Checkboxes'),
                (6, 'datepicker', 'Date Picker'),
                (7, 'submit', 'Button'),
                (8, 'img', 'Image'),
                (9, 'selectbox', 'Drop Down'),
                (10, 'radiobuttons', 'Radio Buttons'),
                (11, 'countryList', 'Countries List'),
                (12, 'selectlist', 'List'),
                (13, 'countryListMultiple', 'Country List with posibility to select multiple countries'),
                (14, 'block', 'Will show only value as text'),
                (15, 'statesList', 'States List'),
                (16, 'textFieldsDynamicTable', 'Dynamic table - multiple text options set'),
                (17, 'textarea', 'Textarea')");
            /**
			 * modules 
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."modules")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."modules` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `code` varchar(64) NOT NULL,
				  `active` tinyint(1) NOT NULL DEFAULT '0',
				  `type_id` smallint(3) NOT NULL DEFAULT '0',
				  `params` text,
				  `has_tab` tinyint(1) NOT NULL DEFAULT '0',
				  `label` varchar(128) DEFAULT NULL,
				  `description` text,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `code` (`code`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."modules` 
				  ADD UNIQUE INDEX `code` (`code`)");
            $payPalData = array(
                array('email' => '', 'testMode' => '0')
            );
            $flatRateData = array(
                array('rate' => 5)
            );
            $ratingParams = array(
                array('maxStars' => 5, 'deactivate' => false)
            );
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
              (NULL,'adminmenu',1,1,'',0,'Admin Menu',''),
              (NULL,'products',1,1,'',1,'Products Mod',''),
              (NULL,'options',1,1,'',1,'Options',''),
              (NULL,'user',1,1,'',1,'Users',''),
              (NULL,'pages',1,1,'". json_encode(array()). "',0,'Pages',''),
              (NULL,'checkout',1,1,'',1,'Checkout',''),
              (NULL,'payondelivery',1,2,'',0,'Pay On Delivery',''),
              (NULL,'flatrate',1,3,'". json_encode($flatRateData)."',0,'Flat Rate',''),
              (NULL,'order',1,1,'',0,'Order',''),
              (NULL,'currency',1,1,'',1,'Currency',''),
              (NULL,'paypal',1,2,'". json_encode($payPalData). "',0,'PayPal',''),
              (NULL,'taxes',1,1,'',1,'Taxes',''),
              (NULL,'cartwidget',1,4,'',0,'Shopping Cart widget',''),
              (NULL,'bcwidget',1,4,'',0,'Brands & Categories widget',''),
              (NULL,'templates',1,1,'',1,'Templates for Plugin',''),
              (NULL,'digital_product',1,5,'',0,'Digital Product','Provides ability to sell files'),
              (NULL, 'messenger', 1, 1, '', 1, 'Notifications', 'Module provides the ability to create templates for user notifications and for mass mailing.'),
              (NULL, 'tablerate', 1, 3, '', 0, 'Table Rate Shipping', ''),
              (NULL, 'featured_products', 1, 4, '', 0, 'Featured Products Block Widget', 'Widget to display Featured Products'),
              (NULL, 'single_product', 1, 4, '', 0, 'Single Product Block Widget', 'Widget to display Single Product'),
              (NULL, 'deposit_account', 1, 2, '', 0, 'Deposit Account', 'Deposit Account Payment Module'),
              (NULL, 'breadcrumbs', 1, 4, '', 0, 'Breadcrumbs Widget', 'Widget to display Breadcrumbs'),
              (NULL, 'pagination', 1, 1, '', 0, 'Pagination', 'Pagination module'),
              (NULL, 'currencywidget', 1, 4, '', 0, 'Currency Widget', 'Currency Widget module'),
              (NULL, 'bestsellers_widget', 1, 4, '', 0, 'Bestsellers Widget', 'Bestsellers Widget module'),
              (NULL, 'recent_products_widget', 1, 4, '', 0, 'Recent Products Widget', 'Recent Products Widget module'),
              (NULL, 'search_widget', 1, 4, '', 0, 'Search Widget', 'Search Widget module'),
              (NULL, 'shipping', 1, 1, '', 1, 'Shipping', 'Provides shipping modules control'),
              (NULL, 'slider_widget', 1, 4, '', 0, 'Slider Widget', 'Slider Widget'),
              (NULL, 'shortcodes', 1, 6, '', 0, 'Shortcodes', 'Shortcodes data (accept products and categories shortcodes for now)'),
              (NULL, 'img', 1, 1, '', 0, 'Images', 'Images pre-processing'),
              (NULL, 'twitter_widget', 1, 4, '', 0, 'Twitter Widget', 'Twitter Widget'),
              (NULL, 'mostviewed_widget', 1, 4, '', 0, 'Most Viewed Products', 'Most Viewed Products module'),
              (NULL, 'related_widget', 1, 4, '', 0, 'Related Products Widget', 'Related Products Widget'),
              (NULL, 'categorybrands_products', 1, 4, '', 0, 'Category/Brands Products', 'Category/Brands Products module'),
              (NULL, 'alsopurchased_widget', 1, 4, '', 0, 'Also purchased products', 'Show Also purchased products widget'),
              (NULL, 'weight', 1, 1, '', 0, 'Weight', 'Internal system module to work with weight'),
              (NULL, 'size', 1, 1, '', 0, 'Size', 'Internal system module to work with sizes'),
              (NULL, 'log', 1, 1, '', 0, 'Log', 'Internal system module to log some actions on server'),
              (NULL, 'comments_widget', 1, 4, '', 0, 'Products Comments Widget', 'Products Comments Widget');");
            
              db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."modules`
                            ADD COLUMN `ex_plug_dir` varchar(255) DEFAULT NULL;");
			  /* from now pages will have tab on options page */
			  db::query("UPDATE `".S_WPDB_PREF.S_DB_PREF."modules` SET has_tab = 1 WHERE code = 'pages' LIMIT 1");
            if(!$installed) {
                self::createPages();
            }
            
            /**
			 *  modules_type 
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."modules_type")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."modules_type` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(64) NOT NULL,
				  PRIMARY KEY (`id`)
				) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules_type` VALUES
              (1,'system'),
              (2,'payment'),
              (3,'shipping'),
              (4,'widget'),
              (5,'product_extra'),
              (6,'addons')");
            /**
			 * options 
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."options")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."options` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `code` varchar(64) CHARACTER SET latin1 NOT NULL,
				  `value` text NULL,
				  `label` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
				  `description` text CHARACTER SET latin1,
				  `htmltype_id` smallint(2) NOT NULL DEFAULT '1',
				  `params` text NULL,
				  `cat_id` mediumint(3) DEFAULT '0',
				  `sort_order` mediumint(3) DEFAULT '0',
				  PRIMARY KEY (`id`),
				  KEY `id` (`id`),
				  UNIQUE INDEX `code` (`code`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."options` 
				ADD UNIQUE INDEX `code` (`code`)");
            db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."options`
                    ADD COLUMN `params` text NULL;");
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."options`
                    ADD COLUMN `cat_id` mediumint(3) DEFAULT '0';");
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."options`
                    ADD COLUMN `sort_order` mediumint(3) DEFAULT '0';");
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
              (NULL,'store_name','The One Ecomerce','Store Name','Name of Your Store',1,NULL,1,0),
              (NULL,'store_email','','Store Email','Email to notify about Orders or Other actions',1,NULL,1,0),
			  (NULL,'notify_on_reg','0','Notify on user registration','Notify on user registration',4,NULL,1,0),
              (NULL,'use_store_email_as_from','0','Include Store Email in Mails','Use Store Email as From address in emails, sent from store',4,NULL,2,0),
              (NULL,'shipping_mandatory','0','Shipping is Mandatory on checkout','If true - customer will not be able to continue checkout without selecting shipping module ',4,NULL,2,0),
              (NULL,'payment_mandatory','1','Payment is Mandatory on checkout','If true - customer will not be able to continue checkout without selecting payment module',4,NULL,2,0),
              ".//(NULL,'js_animation_speed','50','Animations speed','This parameter determines how fast, for example, will sub-windows minimazed and maximazed. Supported integer values for animation time in miliseconds or constant fast (=200ms) and slow(=600ms)',1,NULL),
              "(NULL,'stock_check','1','Check products stock','',4,NULL,2,0),
              (NULL,'stock_allow_checkout','1','Allow buy product if it is out-of-stock','',4,NULL,2,0),
              (NULL,'stock_substract','1','Substract quantity from product after checkout','If this option is enabled - after checkout quantity of product in database will be reduced the number of product were buyed',4,NULL,2,0),
              (NULL,'allow_guest_checkout','1','Allow unregistered users to checkout','If not checked - registration will be mandatory before checkout',4,NULL,2,0),
              (NULL,'autofill_sku','1','Generate SKU automatically','If checked - SKU will be generated automatically on product add page.',4,NULL,4,0),
              (NULL,'version','". S_VERSION. "','Plugin version','Plugin version',3,NULL,1,0),
              (NULL,'default_theme','standard','Plugin Theme','Plugin default Theme. To change it go to Template tab',3,NULL,1,0),
			  
              (NULL,'product_preview_width','220','Product Category Preview Width','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. ', 1,NULL,3,0),
			  (NULL,'product_preview_height','220','Product Category Preview Height','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. ', 1,NULL,3,0),
			  (NULL,'product_preview_crop','0','Product Category Preview Crop','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. ', 4,NULL,3,0),
              (NULL,'product_display_width','400','Product Big Image Width','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product big image at product overview page. Note that on click the full size image will appear.', 1,NULL,3,0),
			  (NULL,'product_display_height','400','Product Big Image Height','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product big image at product overview page. Note that on click the full size image will appear.', 1,NULL,3,0),
			  (NULL,'product_display_crop','0','Product Big Image Crop','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product big image at product overview page. Note that on click the full size image will appear.', 4,NULL,3,0),
			  (NULL,'product_small_width','70','Product Preview Image Width','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product small images at product overview page.', 1,NULL,3,0),
			  (NULL,'product_small_height','70','Product Preview Image Height','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product small images at product overview page.', 1,NULL,3,0),
			  (NULL,'product_small_crop','','Product Preview Image Crop','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product small images at product overview page.', 4,NULL,3,0),
			  
				(NULL,'product_single_width','70','Single Product Image Width','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product image at single product widget.', 1,NULL,3,0),
				(NULL,'login_after_register','0','Login New customers after registration','This will make new customer logged in right after registration',4,NULL,1,0),
              (NULL,'checkout_steps','','Checkot steps','Checkout steps data, see Checkout tab',3,NULL,2,0),
              (NULL,'cart_columns','','Cart Product Columns','See Checkout tab',3,NULL,2,0),
              (NULL,'checkout_success_text','','Checkout Success Step Text','See Checkout tab',3,NULL,2,0),
              (NULL,'checkout_skip_confirm_step','','Skip confirmation step on checkout','If this option is selected, customer will be redirected to checkout success step or to billing site right after first checkout step',4,NULL,2,0),
              (NULL,'show_subscreen_on_center','1','Sub Screen pop-up on center','Always show Sub Screen pop-up on center of the page',4,NULL,1,0),
              (NULL,'enable_img_cache','1','Enable cache for images','Allow to cache images data',4,NULL,3,0),
              (NULL,'checkout_force_create_user','0','Create user on checkout in any case','This will create user on each checkout in ANY case - even if some data, that need for WP is missed - it will be stored with made-up data',4,NULL,2,0),
              (NULL,'weight_units','lb','Weight Units','Units of weight for your products',9,'". utils::serialize(array('options' => array('lb' => lang::_('Pounds'), 'kg' => lang::_('Kilogram'), 'oz' => lang::_('Ounce')))). "',4,0),
              (NULL,'size_units','inch','Size Units','Units of size dimensions of your products',9,'". utils::serialize(array('options' => array('inch' => lang::_('Inches'), 'm' => lang::_('Metre')))). "',4,0),
              (NULL,'store_company','','Your company name','Company name of your store owner.',1,NULL,5,0),
              (NULL,'store_phone','','Your phone number','Phone number of your store or your manager.',1,NULL,5,0),
              (NULL,'store_address','','Your address','Address of your store (or warehouse) location, used when calculating shipping charges of some shipping methods.',1,NULL,5,0),
              (NULL,'store_city','','Your city','City of your store (or warehouse) location, used when calculating shipping charges of some shipping methods.',1,NULL,5,0),
              (NULL,'store_country','','Store Country','Select Country were Your store is situated',11,NULL,5,0),
              (NULL,'store_state','','Your state','State of your store (or warehouse) location, used when calculating shipping charges of some shipping methods.',15,NULL,5,0),
              (NULL,'store_zip','','Your postal/zip code','Postal/zip code of your store (or warehouse) location, used when calculating shipping charges of some shipping methods.',1,NULL,5,0),   
			  (NULL ,  'terms', '' ,  'Terms and conditions',  'Terms and conditions',  3, NULL ,  2,  0),		  
              (NULL,'enable_log_actions','0','Enable log actions','Enable log actions. This feature can not support all actions. Be aware that this can load your database.',4,NULL,1,0),
              (NULL,'img_preprocessing_type','','Images preprocessing type','None - will show primary uploaded image, Timthumb is more faster, but Built-in Plug method allow image manipulation by addons for images (such as watermarks)',9,'". utils::serialize(array('options' => array('' => lang::_('None'), 'timthumb' => lang::_('Timthumb'), 'plugin' => lang::_('Built-in Plug')))). "',3,0),
			  (NULL,'prod_show_from_label_if_opt_exist','1','Show \"From\" label near product price','Will add mark \"From\" near products price in listing if product have at least on additional field which affect on product price',4,NULL,4,0),
			  (NULL,'dialog_after_prod_add','0','Show dialog after product was added','Show dialog box after product was added (via ajax) or not',4,NULL,4,0),
			  (NULL,'shipp_same_as_bill','1','By default, Shipping address is same as Billing','On checkout shipping address will be same as billing by default (corresponding checkbox will be checked by default).',4,NULL,2,0),
			  (NULL,'buy_now_redirect','checkout','Redirect for \"Buy Now\"','Redirect to page after user click on \"Buy Now\" button',9,'". utils::serialize(array('options' => array('checkout' => lang::_('Checkout Page'), 'shopping_cart' => lang::_('Shopping Cart Page')))). "',2,0),
			  (NULL,'ssl_on_checkout','','Use SSL on checkout','If you have already setup correct SSL for your site and want always open checkout with SSL protocol - enable this option',4,NULL,2,0),
			  (NULL,'ssl_on_account','','Use SSL on user account','If you have already setup correct SSL for your site and want always open registration, login and user account pages with SSL protocol - enable this option',4,NULL,2,0),
			  
				(NULL,'use_only_one_country','','Use only one country for users','If your store will use only one country - select it here. In this case all country select lists for shipping or billing user fields will be hidden and countryy will be set to selected automaticaly.',11,NULL,2,0);");
            
			// This take too mach time for us - comented
			//self::assignOptionsToCategories();
			
			/* options categories */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."options_categories")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."options_categories` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(128) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id` (`id`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
			db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options_categories` VALUES
				(1, 'General'),
				(2, 'Checkout'),
				(3, 'Images'),
				(4, 'Products'),
				(5, 'Store Address'),
				(6, 'Other')");
			
            /* orders */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."orders")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."orders` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `shipping_address` text NOT NULL,
				  `billing_address` text NOT NULL,
				  `shipping_module` text NOT NULL,
				  `payment_module` text NOT NULL,
				  `taxes` text,
				  `user_id` int(11) NOT NULL DEFAULT '0',
				  `status` enum('created','pending','paid','confirmed','delivered','cancelled') NOT NULL DEFAULT 'created',
				  `sub_total` float(10,2) NOT NULL DEFAULT '0.00',
				  `tax_rate` float(10,2) NOT NULL DEFAULT '0.00',
				  `total` float(10,2) NOT NULL DEFAULT '0.00',
				  `currency` text NOT NULL,
				  `comments` text,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
			db::query("ALTER TABLE `". S_WPDB_PREF.S_DB_PREF. "orders`
                ADD COLUMN date_created INT(11) NOT NULL DEFAULT 0;");
            /* orders_items */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."orders_items")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."orders_items` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`order_id` INT(11) NOT NULL,
					`product_id` INT(11) NOT NULL,
					`product_name` VARCHAR(255) NOT NULL,
					`product_sku` VARCHAR(64) NOT NULL,
					`product_price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
					`product_qty` INT(11) NOT NULL DEFAULT '0',
					`product_params` text,
					PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."orders_items` 
				ADD COLUMN product_params text;");
            db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."orders_items`
                ADD INDEX `order_id` (`order_id`),
                ADD INDEX `product_id` (`product_id`)");
            /* related_products */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."related_widget")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."related_widget` (
				  `id` bigint(15) NOT NULL AUTO_INCREMENT,
				  `rel_from` int(11) NOT NULL,
				  `rel_to` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
            /* variations */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."variations")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."variations` (
				  `id` int(15) NOT NULL AUTO_INCREMENT,
				  `product_id` int(11) NOT NULL,
				  `variation_id` int(11) NOT NULL,
				  `var_name` varchar(128) NOT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
            /* products */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."products")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."products` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `post_id` int(11) NOT NULL DEFAULT '0',
				  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
				  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
				  `weight` float(7,3) DEFAULT '0.000',
				  `sku` varchar(64) NOT NULL,
				  `quantity` int(11) unsigned NOT NULL DEFAULT '0',
				  `featured` tinyint(1) NOT NULL DEFAULT '0',
				  `mark_as_new` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            db::query("ALTER TABLE `". S_WPDB_PREF.S_DB_PREF. "products`
                ADD COLUMN views INT(11) NOT NULL DEFAULT 0;");
            db::query("ALTER TABLE `". S_WPDB_PREF.S_DB_PREF. "products`
                ADD COLUMN `width` decimal(10,6) NOT NULL DEFAULT 0;");
            db::query("ALTER TABLE `". S_WPDB_PREF.S_DB_PREF. "products`
                ADD COLUMN `height` decimal(10,6) NOT NULL DEFAULT 0;");
            db::query("ALTER TABLE `". S_WPDB_PREF.S_DB_PREF. "products`
                ADD COLUMN `length` decimal(10,6) NOT NULL DEFAULT 0;");
			db::query("ALTER TABLE `". S_WPDB_PREF.S_DB_PREF. "products`
                ADD COLUMN `mark_as_new` tinyint(1) NOT NULL DEFAULT '0';");
            /* extrafields */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."extrafields")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."extrafields` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `code` varchar(32) NOT NULL,
				  `parent` varchar(32) NOT NULL,
				  `htmltype_id` smallint(2) NOT NULL DEFAULT '1',
				  `default_value` varchar(128) DEFAULT NULL,
				  `params` text,
				  `label` varchar(128) NOT NULL,
				  `description` text,
				  `destination` text,
				  `mandatory` tinyint(1) NOT NULL DEFAULT '0',
				  `ordering` int(11) NOT NULL DEFAULT '0',
				  `validate` varchar(128) NULL,
				  `active` tinyint(1) NOT NULL DEFAULT '1',
				  `system` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `code` (`code`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."extrafields` 
				ADD UNIQUE INDEX `code` (`code`)");
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."extrafields` 
				MODIFY COLUMN `destination` text");
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."extrafields` VALUES
                (NULL,'first_name','user',1,'','','First Name','','[\"registration\",\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'last_name','user',1,'','','Last Name','','[\"registration\",\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'address','user',1,'','','Address','','[\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'city','user',1,'','','City','','[\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'zip','user',1,'','','Zip / Postal Code','','[\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'country','user',11,'','','Country','','[\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'state','user',15,'','','State','','[\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'phone','user',1,'','','Contact Phone','','[\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'fax','user',1,'','','Fax','','[\"shipping\",\"billing\"]',0,0,'',1,1),
                (NULL,'company','user',1,'','','Company','','[\"shipping\",\"billing\"]',0,0,'',0,1),
                (NULL,'username','user',1,'','','Username','','[\"registration\"]',0,0,'',1,1),
                (NULL,'password','user',2,'','','Password','','[\"registration\"]',0,0,'',1,1),
                (NULL,'re_password','user',2,'','','Repeat Password','','[\"registration\"]',0,0,'',1,1),
                (NULL,'email','user',1,'','','Email','','[\"registration\"]',0,0,'',1,1);");
            /**
             * extra fields options
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."ef_options")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."ef_options` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`ef_id` int(11) NOT NULL,
					`value` varchar(255) NOT NULL,
					`data` text,
					`price` decimal(10,2) NOT NULL DEFAULT '0.00',
					`price_absolute` tinyint(1) NOT NULL DEFAULT '0',
					`sort_order` INT(11) NOT NULL DEFAULT 0,
					PRIMARY KEY (`id`)
					) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_options`
                ADD COLUMN `data` TEXT;");
            /**
             * extra fields values
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."ef_val")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."ef_val` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `parent_id` int(11) NOT NULL,
					  `parent_type` varchar(32) NOT NULL,
					  `ef_id` int(11) NOT NULL,
					  `opt_id`  int(11) NOT NULL DEFAULT 0,
					  `value` longtext NOT NULL,
					  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
					  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
					  `price_absolute` tinyint(1) NOT NULL DEFAULT '0',
					  `disabled` tinyint(1) NOT NULL DEFAULT '0',
					  `sort_order` INT(11) NOT NULL DEFAULT 0,
					  PRIMARY KEY (`id`),
					  KEY `parent_id` (`parent_id`,`ef_id`)
					) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_val`
                ADD COLUMN `price_absolute` tinyint(1) NOT NULL DEFAULT '0';");
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_val`
                ADD COLUMN `disabled` tinyint(1) NOT NULL DEFAULT '0';");
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_val`
                ADD COLUMN `opt_id` int(11) NOT NULL DEFAULT 0;");
            /**
			 * taxes
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."taxes")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."taxes` (
				 `id` int(11) NOT NULL AUTO_INCREMENT,
				 `label` varchar(128) NOT NULL,
				 `type` enum('address','total','category','undefined') NOT NULL DEFAULT 'undefined',
				 `data` text,
				 PRIMARY KEY (`id`)
			   ) DEFAULT CHARSET=utf8;";
			   dbDelta($q);
			}
            /**
			 * countries
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."countries")) {
				$q = "CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."countries` (
				 `id` int(11) NOT NULL AUTO_INCREMENT,
				 `name` varchar(128) NOT NULL,
				 `iso_code_2` varchar(2) DEFAULT NULL,
				 `iso_code_3` varchar(3) DEFAULT NULL,
				 PRIMARY KEY (`id`)
			   ) DEFAULT CHARSET=utf8;";
			   dbDelta($q);
			   self::_insertCountries();
			}
            /**
             * Product categories extra fields
             */
			if(!db::exist(S_WPDB_PREF."products_brandsmeta")) {
				dbDelta("CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF."products_brandsmeta` (
				  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `products_brands_id` bigint(20) unsigned NOT NULL DEFAULT '0',
				  `meta_key` varchar(255) DEFAULT NULL,
				  `meta_value` longtext,
				  PRIMARY KEY (`meta_id`),
				  KEY `products_brands_id` (`products_brands_id`),
				  KEY `meta_key` (`meta_key`)
				) DEFAULT CHARSET=utf8");
			}
            /**
             * Product Brand Extra Fields
             */
			if(!db::exist(S_WPDB_PREF."products_categoriesmeta")) {
				dbDelta("CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF."products_categoriesmeta` (
				  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				  `products_categories_id` bigint(20) unsigned NOT NULL DEFAULT '0',
				  `meta_key` varchar(255) DEFAULT NULL,
				  `meta_value` longtext,
				  PRIMARY KEY (`meta_id`),
				  KEY `products_categories_id` (`products_categories_id`),
				  KEY `meta_key` (`meta_key`)
				) DEFAULT CHARSET=utf8");
			}
            /**
             * Product files
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."product_files")) {
				dbDelta("CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."product_files` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `pid` int(11) NOT NULL,
				  `name` varchar(255) NOT NULL,
				  `path` varchar(255) NOT NULL,
				  `mime_type` varchar(255) DEFAULT NULL,
				  `size` int(11) NOT NULL DEFAULT '0',
				  `active` tinyint(1) NOT NULL,
				  `date` datetime DEFAULT NULL,
				  `download_limit` int(11) NOT NULL DEFAULT '0',
				  `period_limit` int(11) NOT NULL DEFAULT '0',
				  `description` text NOT NULL,
				  `type_id` SMALLINT(5) NOT NULL DEFAULT 1,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
			}
            db::query("ALTER TABLE ".S_WPDB_PREF.S_DB_PREF."product_files 
                ADD COLUMN type_id SMALLINT(5) NOT NULL DEFAULT 1");
            /**
			 * Product file types
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."product_files_types")) {
				dbDelta("CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."product_files_types` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(255) NOT NULL,
				  `label` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
			}
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."product_files_types` VALUES
                (1, 'sell', 'For selling'),
                (2, 'simple', 'Simple file')");
            /**
             * User Product files
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."user_files")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."user_files` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `uid` int(11) NOT NULL,
				  `order_id` int(11) NOT NULL,
				  `fid` int(11) NOT NULL,
				  `downloads` int(11) NOT NULL DEFAULT '0',
				  `expires` datetime DEFAULT NULL,
				  `token` text DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            /**
             * Email Templates
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."email_templates")) {
				dbDelta("CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."email_templates` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `label` varchar(128) NOT NULL,
					  `subject` varchar(255) NOT NULL,
					  `body` text NOT NULL,
					  `variables` text NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `name` varchar(128) NOT NULL,
					  `module` varchar(128) NOT NULL,
					  PRIMARY KEY (`id`)
					) DEFAULT CHARSET=utf8");
			}
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."email_templates`
				ADD UNIQUE INDEX `name` (`name`)");
            db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."email_templates` (`id`, `label`, `subject`, `body`, `variables`, `active`, `name`, `module`) VALUES
                            (1, 'Digital product links', 'Ordered files from :store_name', 'Hello, :user_name <br />\r\nYou made an Order #:order_id at :store_name. <br />\r\nOrder Products List: <br />\r\n:products\r\n<br />\r\nYou can download your files with the links below: <br />\r\n:links \r\n<br />\r\nOr at your account page.\r\n<br />\r\nThanks for your purchase at :store_name.', '[\"user_name\", \"store_name\", \"products\", \"links\", \"order_id\"]', 1, 'links_list', 'digital_product'),
                            (2, 'Order success placement', 'Your order at :store_name', 'Hello, :user_name <br />\r\nYou made an Order #:order_id at :store_name. <br />\r\n:order_info\r\n<br />\r\nThanks for your purchase at :store_name.', '[\"user_name\", \"store_name\", \"order_info\", \"order_id\", \"account_login_link\"]', 1, 'success_placement', 'order'),
                            (3, 'Registration success', 'Registration at :store_name', 'Hello, :username <br />\r\nYou have been registered at :store_name. <br />\r\nYou login info: <br />\r\nUsername: :username<br />\r\nPassword: :password<br />\r\nThanks for using our online store!', '[\"username\", \"store_name\", \"password\"]', 1, 'registration', 'user'),
                            (4, 'Password Reset', 'Password Reset at :store_name', 'Hello, :first_name <br />\r\nYour new password at :store_name are:<br />\r\nPassword: :password<br />\r\nThanks for using our online store!', '[\"first_name\", \"store_name\", \"password\"]', 1, 'reset_password', 'user'),
							(5, 'Password Reset Request', 'Password Reset Request at :store_name', 'Hello, :first_name <br />\r\nYou requested password reset at :store_name.<br />\r\nFollow this link<br />\r\n<a href=\":reset_link\">:reset_link</a><br />\r\nto complete your request.', '[\"first_name\", \"store_name\", \"reset_link\"]', 1, 'reset_password_request', 'user'),
							(6, 'New user registration', 'New user registration at :store_name', 'New user registered at :store_name. <br />\r\nUser info: <br />\r\nUsername: :username<br />\r\nPassword: :password<br />\r\nThanks for using our online store!', '[\"username\", \"store_name\", \"password\"]', 1, 'admin_notify', 'user');");
            /**
             * Store Subscribers
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."subscribers")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."subscribers` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `user_id` int(11) NOT NULL DEFAULT '0',
					  `email` varchar(255) NOT NULL,
					  `name` varchar(255) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `unsubscribe_date` datetime DEFAULT NULL,
					  `active` tinyint(4) NOT NULL DEFAULT '1',
					  `token` varchar(255) DEFAULT NULL,
					  PRIMARY KEY (`id`),
					  KEY `user_id` (`user_id`)
					) DEFAULT CHARSET=utf8";
				dbDelta($q);
			}
            /**
             * States
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."states")) {
				$q = "CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."states` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(128) COLLATE utf8_romanian_ci NOT NULL,
				  `code` varchar(6) COLLATE utf8_romanian_ci NOT NULL,
				  `country_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8";
				dbDelta($q);
				self::_insertStates();
			}
            /**
             * Ratings
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."rating")) {
				dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."rating` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `pid` int(11) NOT NULL DEFAULT '0',
					  `ip` varchar(32) COLLATE utf8_romanian_ci NOT NULL,
					  `rate` tinyint(2) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) DEFAULT CHARSET=utf8");
			}
            db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."rating`
                ADD COLUMN `comment_id` int(11) NOT NULL DEFAULT '0';");
            db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."rating`
                ADD COLUMN `approved` int(11) NOT NULL DEFAULT '1';");
            /**
             * Shipping modules
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."shipping")) {
				dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."shipping` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(255) NOT NULL,
				  `description` text,
				  `code` varchar(128) NOT NULL,
				  `params` text,
				  `active` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
			}
            /**
             * Image Statuses
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."img_status")) {
				dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."img_status` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `parent_id` int(11) NOT NULL DEFAULT '0',
				  `post_id` int(11) NOT NULL DEFAULT '0',
				  `status` enum('all','catt','catt_only') DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
			}
            /**
             * Log table - all logs in project
             */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."log")) {
				dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."log` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `type` varchar(64) NOT NULL,
				  `data` text,
				  `date_created` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
			}
			/* User ID, who made changes*/
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."log`
				ADD COLUMN `uid` int(11) NOT NULL DEFAULT 0;");
			/* Order ID for orders log */
			db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."log`
				ADD COLUMN `oid` int(11) NOT NULL DEFAULT 0;");
			/**
			 * Images, that are used for content, not for media gallery
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."content_img")) {
				dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."content_img` (
				  `img_id` int(11) NOT NULL,
				  `pid` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`img_id`)
				) DEFAULT CHARSET=utf8");
			}
			/**
			 * Plugin usage statistics
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."usage_stat")) {
				dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."usage_stat` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `code` varchar(64) NOT NULL,
				  `visits` int(11) NOT NULL DEFAULT '0',
				  `spent_time` int(11) NOT NULL DEFAULT '0',
				  `modify_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				  UNIQUE INDEX `code` (`code`),
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8");
				db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."usage_stat` (code, visits) VALUES ('installed', 1)");
			}
			/**
			 * Products fields
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."products_fields")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."products_fields` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(255) NOT NULL,
				  `htmltype_id` smallint(2) NOT NULL,
				  `default_value` text,
				  `mandatory` tinyint(1) NOT NULL DEFAULT '0',
				  `active` tinyint(1) NOT NULL DEFAULT '0',
				  `sort_order` int(11) NOT NULL DEFAULT '0',
				  `original_id` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
			/**
			 * Products fields Options
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."products_fields_options")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."products_fields_options` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `products_field_id` int(11) NOT NULL DEFAULT '0',
				  `label` varchar(255) NOT NULL,
				  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
				  `absolute` tinyint(1) NOT NULL DEFAULT '0',
				  `sort_order` int(11) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`),
				  INDEX `products_field_id` (`products_field_id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
			/**
			 * Products fields to Categories
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."products_fields_to_categories")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."products_fields_to_categories` (
				  `products_field_id` int(11) NOT NULL DEFAULT '0',
				  `category_id` int(11) NOT NULL DEFAULT '0',
				  INDEX `products_field_id` (`products_field_id`),
				  INDEX `category_id` (`category_id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
			/**
			 * Products fields to Products
			 */
			if(!db::exist(S_WPDB_PREF.S_DB_PREF."products_fields_to_products")) {
				$q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."products_fields_to_products` (
				  `products_field_id` int(11) NOT NULL DEFAULT '0',
				  `product_id` int(11) NOT NULL DEFAULT '0',
				  INDEX `products_field_id` (`products_field_id`),
				  INDEX `product_id` (`product_id`)
				) DEFAULT CHARSET=utf8;";
				dbDelta($q);
			}
			// First time lauch
			
			installerDbUpdater::runUpdate($current_version);
			if(!$installed) {
                add_option('re_db_version', S_VERSION);
			} else {
				self::setUsed();
			}
			
            update_option('re_db_version', S_VERSION);
            add_option('re_db_installed', 1);
            db::query("UPDATE `".S_WPDB_PREF.S_DB_PREF."options` SET value = '". S_VERSION. "' WHERE code = 'version' LIMIT 1");
			
			//$time = microtime(true) - $start;	// Speed debug info
        }
		static public function setUsed() {
			update_option('re_used', 1);
		}
		static public function isUsed() {
			return (int) get_option('re_used');
		}
		/**
		 * Set each option to it's category. Made this for instances, where options was already present - just to made update of table
		 */
		static public function assignOptionsToCategories() {
			$optToCat = array(
				'store_name' => 1,
				'store_email' => 1,
				'use_store_email_as_from' => 2,
				'shipping_mandatory' => 2,
				'payment_mandatory' => 2,
				'stock_check' => 2,
				'stock_allow_checkout' => 2,
				'stock_substract' => 2,
				'allow_guest_checkout' => 2,
				'autofill_sku' => 4,
				'version' => 1,
				'default_theme' => 1,
				
				'product_preview_width' => 2,
				'product_preview_height' => 2,
				'product_preview_crop' => 2,
				'product_display_width' => 2,
				'product_display_height' => 2,
				'product_display_crop' => 2,
				'product_small_width' => 2,
				'product_small_height' => 2,
				'product_small_crop' => 2,
				
				'product_single_width' => 3,
				'login_after_register' => 1,
				'checkout_steps' => 2,
				'cart_columns' => 2,
				'checkout_success_text' => 2,
				'checkout_skip_confirm_step' => 2,
				'show_subscreen_on_center' => 1,
				'enable_img_cache' => 3,
				'checkout_force_create_user' => 2,
				'weight_units' => 4,
				'size_units' => 4,
				'store_company' => 5,
				'store_phone' => 5,
				'store_address' => 5,
				'store_city' => 5,
				'store_country' => 5,
				'store_state' => 5,
				'store_zip' => 5,
				'enable_log_actions' => 1,
				'img_preprocessing_type' => 3,
				'prod_show_from_label_if_opt_exist' => 4,
				'dialog_after_prod_add' => 4,
				'shipp_same_as_bill' => 2,
			);
			foreach($optToCat as $code => $cat_id) {
				db::query('UPDATE `'. S_WPDB_PREF.S_DB_PREF. 'options` SET cat_id = '. $cat_id. ' WHERE `code` = "'. $code. '" LIMIT 1');
			}
		}
		/**
		 * Create pages for plugin usage
		 */
		static public function createPages($defaultPagesData = array()) {
			$defaultPagesData = empty($defaultPagesData) ? array(
				array('page_id' => 0,	'mod' => 'user',			'action' => 'getLoginForm',					'showFor' => 'guest',	'title' => 'Login'),
				array('page_id' => 0,	'mod' => 'user',			'action' => 'getRegisterForm',				'showFor' => 'guest',	'title' => 'Registration'),
				array('page_id' => 0,	'mod' => 'user',			'action' => 'getShoppingCart',				'showFor' => 'all',		'title' => 'Shopping Cart'),
				array('page_id' => 0,	'mod' => 'checkout',		'action' => 'getAllHtml',					'showFor' => 'all',		'title' => 'Checkout'),
				array('page_id' => 0,	'mod' => 'user',			'action' => 'getAccountSummaryHtml',		'showFor' => 'logged',	'title' => 'My Account'),
				array('page_id' => 0,	'mod' => 'user',			'action' => 'getProfileHtml',				'showFor' => 'logged',	'title' => 'My Profile',	'parentTitle' => 'My Account'),
				array('page_id' => 0,	'mod' => 'user',			'action' => 'getOrdersList',				'showFor' => 'logged',	'title' => 'My Orders',		'parentTitle' => 'My Account'),
				array('page_id' => 0,	'mod' => 'digital_product',	'action' => 'getDownloadsList',				'showFor' => 'logged',	'title' => 'My Downloads',	'parentTitle' => 'My Account'),
				array('page_id' => 0,	'mod' => 'user',			'action' => 'getPasswordRecoverConfirm',	'showFor' => 'guest',	'title' => 'Password Recovery Confirmation', 'hideFromMenu' => true),
				array('page_id' => 0,	'mod' => 'products',		'action' => 'getCategoriesListHtml',		'showFor' => 'all',		'title' => 'Categories'),
				array('page_id' => 0,	'mod' => 'products',		'action' => 'getBrandsListHtml',			'showFor' => 'all',		'title' => 'Brands'),
				array('page_id' => 0,	'mod' => 'products',		'action' => 'getAllProductsListHtml',		'showFor' => 'all',		'title' => 'All Products'),
			) : $defaultPagesData;
			$toePages = @json_decode(get_option('toe_pages'));
			if(empty($toePages) || !is_array($toePages)) {
				$toePages = array();
				foreach($defaultPagesData as $p) {
					$pageData = $p;
					if(isset($p['parentTitle']) && ($parentPage = self::_getPageByTitle($p['parentTitle'], $toePages)))
						$pageData['page_id'] = self::_addPageToWP($p['title'], $parentPage->page_id);
					else
						$pageData['page_id'] = self::_addPageToWP($p['title']);	
					$toePages[] = (object) $pageData;
				}
			} else {
				$existsTitles = array();
				foreach($toePages as $i => $p) {
					if(!isset($p->page_id)) continue;
					$page = get_page($p->page_id);
					$existsTitles[] = $p->title;
					if(empty($page) || (!empty($page) && $page->post_type != 'page')) {
						if(isset($p->parentTitle) && ($parentPage = self::_getPageByTitle($p->parentTitle, $toePages))) {
							$toePages[ $i ]->page_id = self::_addPageToWP($p->title, $parentPage->page_id);
						} else
							$toePages[ $i ]->page_id = self::_addPageToWP($p->title);	
					}
				}
				// Create new added after update pages
				if(count($existsTitles) != count($defaultPagesData)) {
					foreach($defaultPagesData as $p) {
						if(!in_array($p['title'], $existsTitles)) {
							$pageData = $p;
							if(isset($p['parentTitle']) && ($parentPage = self::_getPageByTitle($p['parentTitle'], $toePages)))
								$pageData['page_id'] = self::_addPageToWP($p['title'], $parentPage->page_id);
							else
								$pageData['page_id'] = self::_addPageToWP($p['title']);	
							$toePages[] = (object) $pageData;
						}
					}
				}
			}
			db::query("UPDATE `".S_WPDB_PREF.S_DB_PREF."modules` SET params = '". json_encode($toePages). "' WHERE code = 'pages' LIMIT 1");
			update_option('toe_pages', json_encode($toePages));
		}
		/**
		 * Return page data from given array, searched by title, used in self::createPages()
		 * @return mixed page data object if success, else - false
		 */
		static private function _getPageByTitle($title, $pageArr) {
			foreach($pageArr as $p) {
				if($p->title == $title)
					return $p;
			}
			return false;
		}
        static public function delete() {
            global $wpdb;
            $deleteProducts = req::getVar('deleteProducts');
            $deleteOrders = req::getVar('deleteOrders');
            $deleteOptions = req::getVar('deleteOptions');
            if(frame::_()->getModule('pages')) {
                if(is_null($deleteProducts)) {
                    frame::_()->getModule('pages')->getView()->displayDeactivatePage();
                    exit();
                }
                //Delete All pages, that was installed with plugin
                $pages = frame::_()->getModule('pages');
                if(is_object($pages)) {
                    $pages = $pages->getAll();
                    if($pages) {
                        foreach($pages as $p) {
                            wp_delete_post($p->page_id, true);
                        }
                    }
                }
            }
            if((bool) $deleteOrders) {
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."orders`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."orders_items`");
            }
            if((bool) $deleteProducts) {
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."product_files`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."products`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF."products_categoriesmeta`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF."products_brandsmeta`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF."rating`");
				
				$wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF."products_fields`");
				$wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF."products_fields_options`");
				$wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF."products_fields_to_categories`");
				$wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF."products_fields_to_products`");
				
            }
            if((bool) $deleteOptions) {
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."modules`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."modules_type`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."options`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."htmltype`");

                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."currency`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."countries`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."taxes`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."cart`");
            
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."templates`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."user_files`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."email_templates`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."subscribers`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."states`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."shipping`");

                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."extrafields`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."ef_options`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."ef_val`");
                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."userfields`");

                $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."ef_options_exclude`");
            }
            delete_option('re_db_version');
            delete_option('re_db_installed');
        }
        static protected function _addPageToWP($post_title, $post_parent = 0) {
            return wp_insert_post(array(
                 'post_title' => lang::_($post_title),
                 'post_content' => lang::_(array($post_title, 'Page Content')),
                 'post_status' => 'publish',
                 'post_type' => 'page',
                 'post_parent' => $post_parent,
                 'comment_status' => 'closed'
            ));
        }
        
        static public function update_013() {
            global $wpdb;
            if(!db::exist('@__options', 'code', 'product_preview_width')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES 
                    (NULL,'product_preview_width','220','Product Category Preview Width','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. Preview width on category pages and for category preview. ', 1)");
            }
            if(!db::exist('@__options', 'code', 'product_display_width')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES 
                    (NULL,'product_display_width','400','Product Big Image Width','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product big image at product overview page. Note that on click the full size image will appear.', 1)");
            }
            if(!db::exist('@__options', 'code', 'product_small_width')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES 
                    (NULL,'product_small_width','70','Product Preview Image Width','DO NOT CHANGE THIS PARAMETER IF YOU HAVE ALREADY UPLOADED IMAGES TO PRODUCTS. ALL OLD IMAGES WILL NOT BE RESIZED. OR YOU CAN REUPLOAD OLD IMAGES. The width of the product small images at product overview page.', 1)");
            }
            if(!db::exist('@__orders_items', 'product_params')) {
                $wpdb->query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."orders_items` ADD COLUMN product_params text;");
            }
            update_option('re_db_version', S_VERSION);
            return true;
        }
        static public function update_017() {
            global $wpdb;
            if(!db::exist('@__modules', 'code', 'deposit_account')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` VALUES
                          (23, 'deposit_account', 1, 2, '', 0, 'Deposit Account', 'Deposit Account Payment Module');");
            }
            if(!db::exist('@__htmltype', 'label', 'statesList')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."htmltype` VALUES (15, 'statesList', 'States List')");
            }
            if(!db::exist('@__extrafields', 'code', 'state')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."extrafields` VALUES (9,'state','user',15,'','','State','','[\"shipping\",\"billing\"]',0,0,'',1,1)");
            }
            if(!db::exist('@__states')){
                $q = "CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."states` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(128) COLLATE utf8_romanian_ci NOT NULL,
                  `code` varchar(6) COLLATE utf8_romanian_ci NOT NULL,
                  `country_id` int(11) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) DEFAULT CHARSET=utf8";
                dbDelta($q);
                self::_insertStates();
            }
            if(!db::exist('@__rating')){
                dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."rating` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `pid` int(11) NOT NULL DEFAULT '0',
                  `ip` varchar(32) COLLATE utf8_romanian_ci NOT NULL,
                  `rate` tinyint(2) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`)
                ) DEFAULT CHARSET=utf8");
                self::_insertStates();
            }
            update_option('re_db_version', S_VERSION);
            return true;
        }
        static public function update_018() {
            global $wpdb;
            if(!db::exist('@__modules', 'code', 'pagination')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` VALUES
                          (NULL, 'pagination', 1, 1, '', 0, 'Pagination', 'Pagination module');");
            }
        }
        static public function update_021() {
            global $wpdb;
            if(!db::exist('@__ef_options', 'data')) {
                $wpdb->query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_options` 
                    ADD COLUMN `data` text;");
            }
        }
        static public function update_022() {
            global $wpdb;
            if(!db::exist('@__options', 'name', 'checkout_steps')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
                  (NULL,'checkout_steps','','Checkot steps','Checkout steps data, see Checkout tab',3);");
            }
            if(!db::exist('@__options', 'name', 'cart_columns')) {
                $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
                  (NULL,'cart_columns','','Cart Product Columns','See Checkout tab',3);");
            }
        }
        static public function update_023() {
            global $wpdb;
            if(!db::exist('@__modules', 'code', 'bestsellers_widget')) {
                 $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` VALUES
                    (NULL, 'bestsellers_widget', 1, 4, '', 0, 'Bestsellers Widget', 'Bestsellers Widget module');");
            }
            if(!db::exist('@__modules', 'code', 'recent_products_widget')) {
                 $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` VALUES
                    (NULL, 'recent_products_widget', 1, 4, '', 0, 'Recent Products Widget', 'Recent Products Widget module');");
            }
            if(!db::exist('@__modules', 'code', 'search_widget')) {
                 $wpdb->query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` VALUES
                    (NULL, 'search_widget', 1, 4, '', 0, 'Search Widget', 'Search Widget module');");
            }
        }
        static public function update_024() {

        }
        static public function update_025() {
            db::query('ALTER TABLE `@__htmltype` ADD UNIQUE INDEX `label` (`label`)');
            db::query('ALTER TABLE `@__modules` ADD UNIQUE INDEX `code` (`code`)');
            db::query('ALTER TABLE `@__options` ADD UNIQUE INDEX `code` (`code`)');
            db::query("INSERT INTO `@__htmltype` VALUES (17, 'textarea', 'Textarea')");
            
            if(!db::exist('@__options', 'name', 'checkout_steps')) {
                db::query("INSERT INTO `@__options` VALUES
                  (NULL,'checkout_success_text','','Checkout Success Step Text','See Checkout tab',3);");
            }
            db::query("ALTER TABLE @__options MODIFY value text NULL");
            if(!db::exist('@__options', 'name', 'checkout_skip_confirm_step')) {
                db::query("INSERT INTO `@__options` VALUES
                  (NULL,'checkout_skip_confirm_step','','Skip confirmation step on checkout','If this option is selected, customer will be redirected to checkout success step or to billing site right after first checkout step',4);");
            }
            if(!db::exist('@__modules', 'code', 'shipping')) {
                db::query("INSERT INTO `@__modules` VALUES
                  (NULL, 'shipping', 1, 1, '', 1, 'Shipping', 'Provides shipping modules control');");
            }
            dbDelta("CREATE TABLE `".S_WPDB_PREF.S_DB_PREF."shipping` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `label` varchar(255) NOT NULL,
              `description` text,
              `code` varchar(128) NOT NULL,
              `params` text,
              `active` tinyint(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
            ) DEFAULT CHARSET=utf8");
        }
        static public function update_026() {
            db::query("ALTER TABLE `@__ef_options` 
                    ADD COLUMN `price_absolute` tinyint(1) NOT NULL DEFAULT '0';");
            db::query("ALTER TABLE `@__ef_options` 
                    ADD COLUMN `active` tinyint(1) NOT NULL DEFAULT '1';");
            db::query("ALTER TABLE `@__ef_val` 
                    ADD COLUMN `opt_id` int(1) NOT NULL DEFAULT '0';");
            db::query("ALTER TABLE `@__ef_val` 
                    ADD COLUMN `disabled` tinyint(1) NOT NULL DEFAULT '0';");
        }
        static public function update() {
            $currentVersion = get_option('re_db_version', 0);
            $installed = (int) get_option('re_db_installed', 0);
            if($installed && version_compare(S_VERSION, $currentVersion, '>')) {
                self::init();
                update_option('re_db_version', S_VERSION);
                //db::query("UPDATED `@__options` SET value = '". S_VERSION. "' WHERE code = 'version' LIMIT 1");
            }
        }
		/**
		 * This is public alias for self::_insertStates() method
		 */
		static public function insertStates() {
			return self::_insertStates();
		}
        static protected function _insertStates() {
             db::query("INSERT INTO `@__states` VALUES
              (1,'Alabama','AL',225),
              (2,'Alaska','AK',225),
              (3,'Arizona','AZ',225),
              (4,'Arkansas','AR',225),
              (5,'California','CA',225),
              (6,'Colorado','CO',225),
              (7,'Connecticut','CT',225),
              (8,'Delaware','DE',225),
              (9,'District Of Columbia','DC',225),
              (10,'Florida','FL',225),
              (11,'Georgia','GA',225),
              (12,'Hawaii','HI',225),
              (13,'Idaho','ID',225),
              (14,'Illinois','IL',225),
              (15,'Indiana','IN',225),
              (16,'Iowa','IA',225),
              (17,'Kansas','KS',225),
              (18,'Kentucky','KY',225),
              (19,'Louisiana','LA',225),
              (20,'Maine','ME',225),
              (21,'Maryland','MD',225),
              (22,'Massachusetts','MA',225),
              (23,'Michigan','MI',225),
              (24,'Minnesota','MN',225),
              (25,'Mississippi','MS',225),
              (26,'Missouri','MO',225),
              (27,'Montana','MT',225),
              (28,'Nebraska','NE',225),
              (29,'Nevada','NV',225),
              (30,'New Hampshire','NH',225),
              (31,'New Jersey','NJ',225),
              (32,'New Mexico','NM',225),
              (33,'New York','NY',225),
              (34,'North Carolina','NC',225),
              (35,'North Dakota','ND',225),
              (36,'Ohio','OH',225),
              (37,'Oklahoma','OK',225),
              (38,'Oregon','OR',225),
              (39,'Pennsylvania','PA',225),
              (40,'Rhode Island','RI',225),
              (41,'South Carolina','SC',225),
              (42,'South Dakota','SD',225),
              (43,'Tennessee','TN',225),
              (44,'Texas','TX',225),
              (45,'Utah','UT',225),
              (46,'Vermont','VT',225),
              (47,'Virginia','VA',225),
              (48,'Washington','WA',225),
              (49,'West Virginia','WV',225),
              (50,'Wisconsin','WI',225),
              (51,'Wyoming','WY',225),
              (52,'British Columbia','BC',38),
              (53,'Ontario','ON',38),
              (54,'Newfoundland and Labrador','NL',38),
              (55,'Nova Scotia','NS',38),
              (56,'Prince Edward Island','PE',38),
              (57,'New Brunswick','NB',38),
              (58,'Quebec','QC',38),
              (59,'Manitoba','MB',38),
              (60,'Saskatchewan','SK',38),
              (61,'Alberta','AB',38),
              (62,'Northwest Territories','NT',38),
              (63,'Nunavut','NU',38),
              (64,'Yukon Territory','YT',38),
              (65,'South Australia','SA',13),
              (66,'Tasmania','TAS',13),
              (67,'New South Wales','NSW',13),
              (68,'Victoria','VIC',13),
              (69,'Western Australia','WA',13),
              (70,'Queensland','QLD',13)");
             
        }
        static protected function _insertRows($query, $debug = false) {
            $queryParts = explode('VALUES', $query); 
            $linbeBreakerAscii = ord($queryParts[1][0]);
            $queryParts = explode('VALUES'. chr($linbeBreakerAscii), $query); 
            $qArr = explode('),'. chr($linbeBreakerAscii), $queryParts[1]);     //chr(13) - line break
            $smallQueriesCount = count($qArr);
            for($i = 0; $i < $smallQueriesCount; $i++) {
                $smallQuery = $queryParts[0]. ' VALUES '. $qArr[$i];
                if($i < ($smallQueriesCount-1))
                    $smallQuery .= ')';
                if($debug)
                    var_dump($smallQuery);
                db::query( $smallQuery );
            }
            if($debug)
                exit();
        }
		/**
		 * Public alias for self::_insertCountries()
		 */
		static public function insertCountries() {
			return self::_insertCountries();
		}
        static protected function _insertCountries() {
            db::query('INSERT INTO @__countries VALUES 
                (1, "Afghanistan", "AF", "AFG"),
                (2, "Albania", "AL", "ALB"),
                (3, "Algeria", "DZ", "DZA"),
                (4, "American Samoa", "AS", "ASM"),
                (5, "Andorra", "AD", "AND"),
                (6, "Angola", "AO", "AGO"),
                (7, "Anguilla", "AI", "AIA"),
                (8, "Antarctica", "AQ", "ATA"),
                (9, "Antigua and Barbuda", "AG", "ATG"),
                (10, "Argentina", "AR", "ARG"),
                (11, "Armenia", "AM", "ARM"),
                (12, "Aruba", "AW", "ABW"),
                (13, "Australia", "AU", "AUS"),
                (14, "Austria", "AT", "AUT"),
                (15, "Azerbaijan", "AZ", "AZE"),
                (16, "Bahamas", "BS", "BHS"),
                (17, "Bahrain", "BH", "BHR"),
                (18, "Bangladesh", "BD", "BGD"),
                (19, "Barbados", "BB", "BRB"),
                (20, "Belarus", "BY", "BLR"),
                (21, "Belgium", "BE", "BEL"),
                (22, "Belize", "BZ", "BLZ"),
                (23, "Benin", "BJ", "BEN"),
                (24, "Bermuda", "BM", "BMU"),
                (25, "Bhutan", "BT", "BTN"),
                (26, "Bolivia", "BO", "BOL"),
                (27, "Bosnia and Herzegowina", "BA", "BIH"),
                (28, "Botswana", "BW", "BWA"),
                (29, "Bouvet Island", "BV", "BVT"),
                (30, "Brazil", "BR", "BRA"),
                (31, "British Indian Ocean Territory", "IO", "IOT"),
                (32, "Brunei Darussalam", "BN", "BRN"),
                (33, "Bulgaria", "BG", "BGR"),
                (34, "Burkina Faso", "BF", "BFA"),
                (35, "Burundi", "BI", "BDI"),
                (36, "Cambodia", "KH", "KHM"),
                (37, "Cameroon", "CM", "CMR"),
                (38, "Canada", "CA", "CAN"),
                (39, "Cape Verde", "CV", "CPV"),
                (40, "Cayman Islands", "KY", "CYM"),
                (41, "Central African Republic", "CF", "CAF"),
                (42, "Chad", "TD", "TCD"),
                (43, "Chile", "CL", "CHL"),
                (44, "China", "CN", "CHN"),
                (45, "Christmas Island", "CX", "CXR"),
                (46, "Cocos (Keeling) Islands", "CC", "CCK"),
                (47, "Colombia", "CO", "COL"),
                (48, "Comoros", "KM", "COM"),
                (49, "Congo", "CG", "COG"),
                (50, "Cook Islands", "CK", "COK"),
                (51, "Costa Rica", "CR", "CRI"),
                (52, "Cote D\'Ivoire", "CI", "CIV"),
                (53, "Croatia", "HR", "HRV"),
                (54, "Cuba", "CU", "CUB"),
                (55, "Cyprus", "CY", "CYP"),
                (56, "Czech Republic", "CZ", "CZE"),
                (57, "Denmark", "DK", "DNK"),
                (58, "Djibouti", "DJ", "DJI"),
                (59, "Dominica", "DM", "DMA"),
                (60, "Dominican Republic", "DO", "DOM"),
                (61, "East Timor", "TP", "TMP"),
                (62, "Ecuador", "EC", "ECU"),
                (63, "Egypt", "EG", "EGY"),
                (64, "El Salvador", "SV", "SLV"),
                (65, "Equatorial Guinea", "GQ", "GNQ"),
                (66, "Eritrea", "ER", "ERI"),
                (67, "Estonia", "EE", "EST"),
                (68, "Ethiopia", "ET", "ETH"),
                (69, "Falkland Islands (Malvinas)", "FK", "FLK"),
                (70, "Faroe Islands", "FO", "FRO"),
                (71, "Fiji", "FJ", "FJI"),
                (72, "Finland", "FI", "FIN"),
                (73, "France", "FR", "FRA"),
                (74, "France, Metropolitan", "FX", "FXX"),
                (75, "French Guiana", "GF", "GUF"),
                (76, "French Polynesia", "PF", "PYF"),
                (77, "French Southern Territories", "TF", "ATF"),
                (78, "Gabon", "GA", "GAB"),
                (79, "Gambia", "GM", "GMB"),
                (80, "Georgia", "GE", "GEO"),
                (81, "Germany", "DE", "DEU"),
                (82, "Ghana", "GH", "GHA"),
                (83, "Gibraltar", "GI", "GIB"),
                (84, "Greece", "GR", "GRC"),
                (85, "Greenland", "GL", "GRL"),
                (86, "Grenada", "GD", "GRD"),
                (87, "Guadeloupe", "GP", "GLP"),
                (88, "Guam", "GU", "GUM"),
                (89, "Guatemala", "GT", "GTM"),
                (90, "Guinea", "GN", "GIN"),
                (91, "Guinea-bissau", "GW", "GNB"),
                (92, "Guyana", "GY", "GUY"),
                (93, "Haiti", "HT", "HTI"),
                (94, "Heard and Mc Donald Islands", "HM", "HMD"),
                (95, "Honduras", "HN", "HND"),
                (96, "Hong Kong", "HK", "HKG"),
                (97, "Hungary", "HU", "HUN"),
                (98, "Iceland", "IS", "ISL"),
                (99, "India", "IN", "IND"),
                (100, "Indonesia", "ID", "IDN"),
                (101, "Iran (Islamic Republic of)", "IR", "IRN"),
                (102, "Iraq", "IQ", "IRQ"),
                (103, "Ireland", "IE", "IRL"),
                (104, "Israel", "IL", "ISR"),
                (105, "Italy", "IT", "ITA"),
                (106, "Jamaica", "JM", "JAM"),
                (107, "Japan", "JP", "JPN"),
                (108, "Jordan", "JO", "JOR"),
                (109, "Kazakhstan", "KZ", "KAZ"),
                (110, "Kenya", "KE", "KEN"),
                (111, "Kiribati", "KI", "KIR"),
                (112, "Korea, Democratic People\'s Republic of", "KP", "PRK"),
                (113, "Korea, Republic of", "KR", "KOR"),
                (114, "Kuwait", "KW", "KWT"),
                (115, "Kyrgyzstan", "KG", "KGZ"),
                (116, "Lao People\'s Democratic Republic", "LA", "LAO"),
                (117, "Latvia", "LV", "LVA"),
                (118, "Lebanon", "LB", "LBN"),
                (119, "Lesotho", "LS", "LSO"),
                (120, "Liberia", "LR", "LBR"),
                (121, "Libyan Arab Jamahiriya", "LY", "LBY"),
                (122, "Liechtenstein", "LI", "LIE"),
                (123, "Lithuania", "LT", "LTU"),
                (124, "Luxembourg", "LU", "LUX"),
                (125, "Macau", "MO", "MAC"),
                (126, "Macedonia, The Former Yugoslav Republic of", "MK", "MKD"),
                (127, "Madagascar", "MG", "MDG"),
                (128, "Malawi", "MW", "MWI"),
                (129, "Malaysia", "MY", "MYS"),
                (130, "Maldives", "MV", "MDV"),
                (131, "Mali", "ML", "MLI"),
                (132, "Malta", "MT", "MLT"),
                (133, "Marshall Islands", "MH", "MHL"),
                (134, "Martinique", "MQ", "MTQ"),
                (135, "Mauritania", "MR", "MRT"),
                (136, "Mauritius", "MU", "MUS"),
                (137, "Mayotte", "YT", "MYT"),
                (138, "Mexico", "MX", "MEX"),
                (139, "Micronesia, Federated States of", "FM", "FSM"),
                (140, "Moldova, Republic of", "MD", "MDA"),
                (141, "Monaco", "MC", "MCO"),
                (142, "Mongolia", "MN", "MNG"),
				(143, "Montenegro", "ME", "MNE"),
                (144, "Montserrat", "MS", "MSR"),
                (145, "Morocco", "MA", "MAR"),
                (146, "Mozambique", "MZ", "MOZ"),
                (147, "Myanmar", "MM", "MMR"),
                (148, "Namibia", "NA", "NAM"),
                (149, "Nauru", "NR", "NRU"),
                (150, "Nepal", "NP", "NPL"),
                (151, "Netherlands", "NL", "NLD"),
                (152, "Netherlands Antilles", "AN", "ANT"),
                (153, "New Caledonia", "NC", "NCL"),
                (154, "New Zealand", "NZ", "NZL"),
                (155, "Nicaragua", "NI", "NIC"),
                (156, "Niger", "NE", "NER"),
                (157, "Nigeria", "NG", "NGA"),
                (158, "Niue", "NU", "NIU"),
                (159, "Norfolk Island", "NF", "NFK"),
                (160, "Northern Mariana Islands", "MP", "MNP"),
                (161, "Norway", "NO", "NOR"),
                (162, "Oman", "OM", "OMN"),
                (163, "Pakistan", "PK", "PAK"),
                (164, "Palau", "PW", "PLW"),
                (165, "Panama", "PA", "PAN"),
                (166, "Papua New Guinea", "PG", "PNG"),
                (167, "Paraguay", "PY", "PRY"),
                (168, "Peru", "PE", "PER"),
                (169, "Philippines", "PH", "PHL"),
                (170, "Pitcairn", "PN", "PCN"),
                (171, "Poland", "PL", "POL"),
                (172, "Portugal", "PT", "PRT"),
                (173, "Puerto Rico", "PR", "PRI"),
                (174, "Qatar", "QA", "QAT"),
                (175, "Reunion", "RE", "REU"),
                (176, "Romania", "RO", "ROM"),
                (177, "Russian Federation", "RU", "RUS"),
                (178, "Rwanda", "RW", "RWA"),
                (179, "Saint Kitts and Nevis", "KN", "KNA"),
                (180, "Saint Lucia", "LC", "LCA"),
                (181, "Saint Vincent and the Grenadines", "VC", "VCT"),
                (182, "Samoa", "WS", "WSM"),
                (183, "San Marino", "SM", "SMR"),
                (184, "Sao Tome and Principe", "ST", "STP"),
                (185, "Saudi Arabia", "SA", "SAU"),
                (186, "Senegal", "SN", "SEN"),
				(187, "Serbia", "RS", "SRB"),
                (188, "Seychelles", "SC", "SYC"),
                (189, "Sierra Leone", "SL", "SLE"),
                (190, "Singapore", "SG", "SGP"),
                (191, "Slovakia (Slovak Republic)", "SK", "SVK"),
                (192, "Slovenia", "SI", "SVN"),
                (193, "Solomon Islands", "SB", "SLB"),
                (194, "Somalia", "SO", "SOM"),
                (195, "South Africa", "ZA", "ZAF"),
                (196, "South Georgia and the South Sandwich Islands", "GS", "SGS"),
                (197, "Spain", "ES", "ESP"),
                (198, "Sri Lanka", "LK", "LKA"),
                (199, "St. Helena", "SH", "SHN"),
                (200, "St. Pierre and Miquelon", "PM", "SPM"),
                (201, "Sudan", "SD", "SDN"),
                (202, "Suriname", "SR", "SUR"),
                (203, "Svalbard and Jan Mayen Islands", "SJ", "SJM"),
                (204, "Swaziland", "SZ", "SWZ"),
                (205, "Sweden", "SE", "SWE"),
                (206, "Switzerland", "CH", "CHE"),
                (207, "Syrian Arab Republic", "SY", "SYR"),
                (208, "Taiwan", "TW", "TWN"),
                (209, "Tajikistan", "TJ", "TJK"),
                (210, "Tanzania, United Republic of", "TZ", "TZA"),
                (211, "Thailand", "TH", "THA"),
                (212, "Togo", "TG", "TGO"),
                (213, "Tokelau", "TK", "TKL"),
                (214, "Tonga", "TO", "TON"),
                (215, "Trinidad and Tobago", "TT", "TTO"),
                (216, "Tunisia", "TN", "TUN"),
                (217, "Turkey", "TR", "TUR"),
                (218, "Turkmenistan", "TM", "TKM"),
                (219, "Turks and Caicos Islands", "TC", "TCA"),
                (220, "Tuvalu", "TV", "TUV"),
                (221, "Uganda", "UG", "UGA"),
                (222, "Ukraine", "UA", "UKR"),
                (223, "United Arab Emirates", "AE", "ARE"),
                (224, "United Kingdom", "GB", "GBR"),
                (225, "United States", "US", "USA"),
                (226, "United States Minor Outlying Islands", "UM", "UMI"),
                (227, "Uruguay", "UY", "URY"),
                (228, "Uzbekistan", "UZ", "UZB"),
                (229, "Vanuatu", "VU", "VUT"),
                (230, "Vatican City State (Holy See)", "VA", "VAT"),
                (231, "Venezuela", "VE", "VEN"),
                (232, "Viet Nam", "VN", "VNM"),
                (233, "Virgin Islands (British)", "VG", "VGB"),
                (234, "Virgin Islands (U.S.)", "VI", "VIR"),
                (235, "Wallis and Futuna Islands", "WF", "WLF"),
                (236, "Western Sahara", "EH", "ESH"),
                (237, "Yemen", "YE", "YEM"),
                (238, "Zaire", "ZR", "ZAR"),
                (239, "Zambia", "ZM", "ZMB"),
                (240, "Zimbabwe", "ZW", "ZWE")');
        }
    }
?>