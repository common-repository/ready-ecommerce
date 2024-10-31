<?php
class tablerelated_widget extends table {
    public function __construct() {
        $this->_table = '@__related_widget';
        $this->_id = 'id';
        $this->_alias = 'toe_relw';
        $this->_addField('id', 'hidden', 'int', 0, lang::_('ID'))
                ->_addField('rel_from', 'hidden', 'int', 0, "Product from")
                ->_addField('rel_to', 'hidden', 'int', 0, "Product to");
    }
    public function install($d = array()) {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        /* googlecheckout */
        $q = "CREATE TABLE IF NOT EXISTS `".S_WPDB_PREF.S_DB_PREF."related_widget` (
          `id` bigint(15) NOT NULL AUTO_INCREMENT,
          `rel_from` int(11) NOT NULL,
          `rel_to` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;";
        dbDelta($q);
    }
    public function uninstall($d = array()) {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS `".S_WPDB_PREF.S_DB_PREF."related_widget`");
    }
}