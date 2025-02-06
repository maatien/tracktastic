<?php
defined('ABSPATH') || die();

/**
 * Plugin Name: Tracktastic (mamo.solutions)
 * Description: Tracktastic is a free plugin to integrate Matomo eCommerce tracking with WooCommerce. Optimize your sales with powerful analytics!
 * Version: 1.0.1
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Killian Santos, Martin molle
 * Author URI: https://killian-santos.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tracktastic
 * Domain Path: /languages
 */

/**
 * Load translations
 */
function tracktastic_load_textdomain()
{
    load_plugin_textdomain('tracktastic', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'tracktastic_load_textdomain');

/**
 * Check if WooCommerce is installed and activated
 */
function tracktastic_is_woocommerce_installed()
{
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    return is_plugin_active('woocommerce/woocommerce.php');
}

if (tracktastic_is_woocommerce_installed()) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-matomo.php'; // Include Matomo class
} else {
    /**
     * Display an admin notice if WooCommerce is not installed or activated
     */
    function tracktastic_woocommerce_not_detected()
    {
        echo '<div class="notice notice-error">';
        echo '<p>';
        echo 'Tracktastic ';
        esc_html_e('requires', 'tracktastic');
        echo ' <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> ';
        esc_html_e('to be installed and active.', 'tracktastic');
        echo '</p>';
        echo '</div>';
    }
    add_action('admin_notices', 'tracktastic_woocommerce_not_detected');
}
