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

require_once 'general/general.php';
require_once 'direct-debit-pdf.php';
