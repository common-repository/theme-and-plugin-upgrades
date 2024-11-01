<?php

/**
 * @link              http://store.wphound.com/?plugin=theme-and-plugin-upgrades
 * @since             1.0.0
 * @package           Theme_and_Plugin_Upgrades
 *
 * @wordpress-plugin
 * Plugin Name:       Theme and Plugin Upgrades
 * Plugin URI:        http://store.wphound.com/?plugin=theme-and-plugin-upgrades
 * Description:       Upgrade themes and plugins using a zip file without having to remove them first. 
 * Version:           1.0.0
 * Author:            WP Hound
 * Author URI:        http://www.wphound.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       theme-and-plugin-upgrades
 */


if ( is_admin() ) {
	require( dirname( __FILE__ ) . '/admin.php' );
}
