<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.bidwise.com
 * @since             1.0.0
 * @package           Bidwise
 *
 * @wordpress-plugin
 * Plugin Name:       Bidwise
 * Plugin URI:        http://www.bidwise.com
 * Description:       Unlock your site’s full revenue potential. Bidwise helps your site or online store generate incremental revenue with high performing native ads that don’t distract your visitors. Sign-up for Bidwise and start making money right away by installing the Bidwise Publishers plugin.
 * Version:           1.0.2
 * Author:            Bidwise, Inc
 * Author URI:        http://www.bidwise.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bidwise
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bidwise-activator.php
 */
function activate_bidwise() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bidwise-activator.php';
	Bidwise_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bidwise-deactivator.php
 */
function deactivate_bidwise() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bidwise-deactivator.php';
	Bidwise_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bidwise' );
register_deactivation_hook( __FILE__, 'deactivate_bidwise' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bidwise.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bidwise() {
	$plugin = new Bidwise();
	$plugin->run();
}
run_bidwise();
