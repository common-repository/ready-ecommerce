<?php
class installerDbUpdater {
	static public function update_0312() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
		  (NULL,'ssl_on_checkout','','Use SSL on checkout','If you have already setup correct SSL for your site and want always open checkout with SSL protocol - enable this option',4,NULL,2,0),
		  (NULL,'ssl_on_account','','Use SSL on user account','If you have already setup correct SSL for your site and want always open registration, login and user account pages with SSL protocol - enable this option',4,NULL,2,0),
		  (NULL,'ssl_on_ajax','','Use SSL on AJAX requests','If you have already setup correct SSL for your site and want always use SSL protocol for AJAX requests - enable this option',4,NULL,2,0);");
	}
	static public function update_0313() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
			(NULL,'countries',1,1,'',1,'Countries','Countries for your store'),
			(NULL,'states',1,1,'',1,'States','States for your store');");
	}
	static public function update_0314() {	
		// Empty, but we need this - at least for history)
	}
	static public function update_0315() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
		  (NULL,'show_subcategories_on_categories_page','','Show sub categories on categories page','We install by default page with all categories. If this option is enables - on that page you will see sub-categories too.',4,NULL,4,0);");
	}
	static public function update_0316() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."email_templates` VALUES
		(NULL,'New user registration', 'New user registration at :store_name', 'New user registered at :store_name. <br />\r\nUser info: <br />\r\nUsername: :username<br />\r\nPassword: :password<br />\r\nThanks for using our online store!', '[\"username\", \"store_name\", \"password\"]', 1, 'admin_notify', 'user');");
	}
	static public function update_0317() {
		db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."currency` MODIFY value float(9,3);");
		db::query("INSERT INTO  `".S_WPDB_PREF.S_DB_PREF."options` VALUES (NULL ,  'terms', '' ,  'Terms and conditions',  'Terms and conditions',  3, NULL ,  2,  0);");
		db::query("INSERT INTO  `".S_WPDB_PREF.S_DB_PREF."options` VALUES (NULL ,  'notify_on_reg', '0' ,  'Notify on user registration',  'Notify on user registration',  4, NULL ,  1,  0);");
	}
	static public function update_0318() {
		db::query("UPDATE `". S_WPDB_PREF.S_DB_PREF. "options` SET label='Skip confirmation step on checkout' WHERE code='checkout_skip_confirm_step'");	// Typo error in database - this can be removed after month maybe, now is April 2013
	}
	static public function update_0319() {
		// Related widget beta
		db::query("CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."related_widget` (`id` bigint(15) NOT NULL AUTO_INCREMENT,`rel_from` int(11) NOT NULL,`rel_to` int(11) NOT NULL,PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8;");
		
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
              (NULL, 'related_widget', 1, 4, '', 0, 'Related Products Widget', 'Related Products Widget');");
			  
	}
	static public function update_0320() {
		db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."products` ADD COLUMN `sort_order` INT(11) NOT NULL DEFAULT 0;");
		db::query("UPDATE `".S_WPDB_PREF.S_DB_PREF."options` SET
			params = '". utils::serialize(array('options' => array('lb' => lang::_('Pounds'), 'kg' => lang::_('Kilogram'), 'oz' => lang::_('Ounce'), 'g' => lang::_('Gram')))). "'
			WHERE code = 'weight_units' LIMIT 1");
        db::query("UPDATE `".S_WPDB_PREF.S_DB_PREF."options` SET
			params = '". utils::serialize(array('options' => array('inch' => lang::_('Inches'), 'm' => lang::_('Metre'), 'cm' => lang::_('Centimetre'), 'mm' => lang::_('Millimetre')))). "'
			WHERE code = 'size_units' LIMIT 1");
	}
	static public function update_0321() {
		db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_val` ADD COLUMN `sort_order` INT(11) NOT NULL DEFAULT 0;");
	}
	static public function update_0322() {
		// Empty, but we need this - at least for history)
	}
	static public function update_0323() {
		$usaId = (int) db::get('SELECT id FROM @__countries WHERE iso_code_3 = "USA"', 'one');
		if($usaId) {
			db::query("UPDATE `". S_WPDB_PREF.S_DB_PREF. "states` SET country_id='". $usaId. "' WHERE country_id='223' OR country_id='225'");	// We had some offset in countries table when added 2 countries
		}
	}
	static public function update_0324() {
		db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_options` ADD COLUMN `price` decimal(10,2) NOT NULL DEFAULT '0.00';");
		db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_options` ADD COLUMN `price_absolute` tinyint(1) NOT NULL DEFAULT '0';");
		db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."ef_options` ADD COLUMN `sort_order` INT(11) NOT NULL DEFAULT 0;");
	}
	static public function update_0325() {
		db::query("CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."variations` (`id` int(15) NOT NULL AUTO_INCREMENT,`product_id` int(11) NOT NULL,`variation_id` int(11) NOT NULL,`var_name` varchar(128) NOT NULL,PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8;");
		
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
              (NULL, 'categorybrands_products', 1, 4, '', 0, 'Category/Brands Products', 'Category/Brands Products module');");
	}
	static public function update_0327() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
		  (NULL,'ignore_payments_on_zero_total','','Ignore payments if order total amount is 0','Ignore payments if order total amount is 0 even if payment module selection is mandatory',4,NULL,2,0);");
	}
	static public function update_0331() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
		(NULL,'use_only_one_country','','Use only one country for users','If your store will use only one country - select it here. In this case all country select lists for shipping or billing user fields will be hidden and countryy will be set to selected automaticaly.',11,NULL,2,0);");
	}
	static public function update_0336() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
		(NULL,'show_subcategories_if_exist','','Show sub-categories if possible','If category have sub-categories - they will be displayed when user go to category. In normal way there are always products list displayed.',4,NULL,4,0);");
	}
	static public function update_0338() {
		db::query("ALTER TABLE `".S_WPDB_PREF.S_DB_PREF."variations` ADD COLUMN `var_sort_order` INT(11) NOT NULL DEFAULT 0;");
	}
	static public function update_0339() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
		(NULL,'hide_unavailable_shipping','1','Hide unavailable shipping methods','If this is checked - then on checkout after user select country/state if some shipping module is unavailable - they will be hidden.',4,NULL,2,0);");
	}
	static public function update_0340() {
		$userRegToAdmMail = db::get('SELECT * FROM @__email_templates WHERE id = 6', 'row');
		if(!empty($userRegToAdmMail) 
			&& is_array($userRegToAdmMail)
		) {
			// Update message in database only if was not changed by user
			if($userRegToAdmMail['body'] == "New user registered at :store_name. <br />\r\nUser info: <br />\r\nUsername: :username<br />\r\nPassword: :password<br />\r\nThanks for using our online store!") {
				db::query("UPDATE @__email_templates SET 
						body = 'New user registered at :store_name. <br />\r\nUser info: <br />\r\nUsername: :username<br />\r\nPassword: :password<br />\r\nFirst Name: :first_name<br />\r\nLast Name: :last_name<br />\r\nEmail: :email<br />\r\nThanks for using our online store!'
					WHERE id = 6 LIMIT 1");
			}
			db::query("UPDATE @__email_templates SET 
					variables = '[\"username\", \"store_name\", \"password\", \"first_name\", \"last_name\", \"email\"]'
				WHERE id = 6 LIMIT 1");
		}
	}
	static public function update_0346() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
		(NULL,'show_qty_coming_soon','0','Show Coming Soon message instead of Buy Now if QTY for product is 0 ','If checked - there will be no Buy now or Add to cart buttons for products, that have 0 QTY in stock.',4,NULL,4,0);");
	}
	static public function update_0347() {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
              (NULL, 'promo_ready', 1, 1, '', 0, 'Promo Ready', 'Promo Ready');");
			  
	}
	static public function update_0349($current_version) {
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."options` VALUES
			(NULL,'use_new_products_fields','". ($current_version ? '0' : '1')."','Use new products fields functionality','It is better to use our new products fields functionality, but if like old functionality and alredy have your store with old product fields - you can continue use old product fields.',4,NULL,1,0);");
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
            (NULL, 'dummy_data', 1, 1, '', 0, 'Dummy Data', 'Dummy Data');");
		db::query("INSERT INTO `".S_WPDB_PREF.S_DB_PREF."modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
            (NULL, 'products_fields', 1, 1, '', 1, 'Products fields', 'Products fields');");
	}
	static public function runUpdate($current_version) {
		// This comented code contain some bug, if you find it - un-comment it and be happy)
		/*$fromVersion = $current_version ? $current_version : '0.3.1.2';
		$fromVersionNum = self::_versionToNumber( $fromVersion );
		$toVersionNum = self::_versionToNumber( S_VERSION );
		for($i = $fromVersionNum; $i <= $toVersionNum; $i += 0.001) {
			$methodName = 'update_'. str_replace('.', '', (string) $i);
			if(method_exists('installerDbUpdater', $methodName)) {
				self::$methodName( $current_version );
			}
		}*/
		self::update_0312();
		self::update_0313();
		self::update_0314();
		self::update_0315();
		self::update_0316();
		self::update_0317();
		self::update_0318();
		self::update_0319();
		self::update_0320();
		self::update_0321();
		self::update_0322();
		self::update_0323();
		self::update_0324();
		self::update_0325();
		self::update_0327();
		self::update_0331();
		self::update_0336();
		self::update_0338();
		self::update_0339();
		self::update_0340();
		self::update_0346();
		self::update_0347();
		self::update_0349($current_version);
	}
	static private function _versionToNumber($version) {
		return (float)preg_replace('/^(\d)\.(\d)\.(\d)\.(\d)$/', '\1.\2\3\4', $version);
	}
}