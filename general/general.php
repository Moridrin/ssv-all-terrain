<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('SSV_General')) {
    require_once 'models/custom-fields/Field.php';

    #region Register Scripts
    function mp_ssv_general_admin_scripts()
    {
        wp_enqueue_script('mp-ssv-input-field-selector', SSV_General::URL . '/js/mp-ssv-input-field-selector.js', array('jquery'));
        wp_localize_script(
            'mp-ssv-input-field-selector',
            'settings',
            array('custom_field_fields' => get_option(SSV_General::OPTION_CUSTOM_FIELD_FIELDS))
        );
        wp_enqueue_script('mp-ssv-sortable-tables', SSV_General::URL . '/js/mp-ssv-sortable-tables.js', array('jquery', 'jquery-ui-sortable'));
        wp_enqueue_script('mp-ssv-field-filters', SSV_General::URL . '/js/mp-ssv-field-filters.js', array('jquery'));
    }

    add_action('admin_enqueue_scripts', 'mp_ssv_general_admin_scripts');
    #endregion

    global $wpdb;
    define('SSV_GENERAL_PATH', plugin_dir_path(__FILE__));
    define('SSV_GENERAL_URL', plugins_url() . '/' . plugin_basename(__DIR__));
    define('SSV_GENERAL_BASE_URL', (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
    define('SSV_GENERAL_CUSTOM_FIELDS_TABLE', $wpdb->prefix . "ssv_general_custom_fields");
    require_once 'SSV_General.php';

    SSV_General::_init();

    #region Register
    function mp_ssv_general_register_plugin()
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        $table_name = SSV_General::CUSTOM_FIELDS_TABLE;
        $sql
                    = "
		CREATE TABLE IF NOT EXISTS $table_name (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			postID bigint(20) NOT NULL,
			customField TEXT NOT NULL,
			PRIMARY KEY (ID)
		) $charset_collate;";
        $wpdb->query($sql);
    }
    #endregion
}
