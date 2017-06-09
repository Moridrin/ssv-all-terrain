<?php
/**
 * Plugin Name: SSV All Terrain
 * Plugin URI: https://bosso.nl/ssv-users/
 * Description: SSV All Terrain is a plugin that offers some custom functions for All Terrain like:
 * - Stream Direct Debit PDF from User fields
 * - Etc.
 * This plugin is fully compatible with the SSV library which can add functionality like: Users, MailChimp, Events, etc.
 * Version: 1.0.0
 * Author: moridrin
 * Author URI: http://nl.linkedin.com/in/jberkvens/
 * License: WTFPL
 * License URI: http://www.wtfpl.net/txt/copying/
 */

if (!defined('ABSPATH')) {
    exit;
}
define('SSV_ALL_TERRAIN_PATH', plugin_dir_path(__FILE__));
define('SSV_ALL_TERRAIN_URL', plugins_url() . '/' . plugin_basename(__DIR__));

require_once 'general/general.php';
require_once 'direct-debit-pdf.php';
require_once 'logout-link.php';

register_activation_hook(SSV_ALL_TERRAIN_PATH . 'ssv-all-terrain.php', 'mp_ssv_general_register_plugin');
