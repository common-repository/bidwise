<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.bidwise.com
 * @since      1.0.0
 *
 * @package    Bidwise
 * @subpackage Bidwise/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bidwise
 * @subpackage Bidwise/includes
 * @author     Ariel Rodriguez Romero <ariel@bidwise.com>
 */
class Bidwise_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
    // https://premium.wpmudev.org/blog/activate-deactivate-uninstall-hooks
    add_option('bidwise-plugin-activation', 'just-activated');
	}

}
