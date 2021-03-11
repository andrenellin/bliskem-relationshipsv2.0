<?php
/**
 * Plugin Name: Bliksem Relationships
 * Version: 1.0.0
 * Plugin URI: https://github.com/andrenellin/bliksem-relationships
 * Description: Specify parent child relationships between users
 * Author: Andre Nell
 * Author URI: http://www.andrenell.me/
 *
 *
 * @package Bliksem
 * @author Andre Nell
 * @since 1.0.0
 */

if (! function_exists('get_option')) {
    header('HTTP/1.0 403 Forbidden');
    die;  // Silence is golden, direct call is prohibited
}

if (defined('BLIKSEM_RELATIONSHIPS_PLUGIN_URL')) {
    wp_die('It seems that other version of User Role Editor is active. Please deactivate it before use this version');
}

define('BLIKSEM_RELATIONSHIPS_VERSION', '1.0.1');
define('BLIKSEM_RELATIONSHIPS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BLIKSEM_RELATIONSHIPS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BLIKSEM_RELATIONSHIPS_PLUGIN_BASE_NAME', plugin_basename(__FILE__));
define('BLIKSEM_RELATIONSHIPS_PLUGIN_FILE', basename(__FILE__));
define('BLIKSEM_RELATIONSHIPS_PLUGIN_FULL_PATH', __FILE__);

require_once(BLIKSEM_RELATIONSHIPS_PLUGIN_DIR.'includes/classes/base-lib.php');
require_once(BLIKSEM_RELATIONSHIPS_PLUGIN_DIR.'includes/classes/lib.php');

// check PHP version
$ure_required_php_version = '5.6';
$exit_msg = 'User Role Editor requires PHP '. $ure_required_php_version .' or newer. '.
            '<a href="http://wordpress.org/about/requirements/">Please update!</a>';
BLIKSEM_RELATIONSHIPS_Lib::check_version(PHP_VERSION, $ure_required_php_version, $exit_msg, __FILE__);

// check WP version
$ure_required_wp_version = '4.0';
$exit_msg = 'User Role Editor requires WordPress '. $ure_required_wp_version .' or newer. '.
            '<a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';
BLIKSEM_RELATIONSHIPS_Lib::check_version(get_bloginfo('version'), $ure_required_wp_version, $exit_msg, __FILE__);

require_once(BLIKSEM_RELATIONSHIPS_PLUGIN_DIR .'includes/loader.php');

// Uninstall action
register_uninstall_hook(BLIKSEM_RELATIONSHIPS_PLUGIN_FULL_PATH, array('User_Role_Editor', 'uninstall'));

$GLOBALS['user_role_editor'] = User_Role_Editor::get_instance();
