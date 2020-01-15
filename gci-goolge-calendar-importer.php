<?php
/**
 * Plugin Name:     Google Calendar Importer
 * Plugin URI:
 * Description:     Adds the ability to import ical events from google to the events calendar. Clearing all events each import.
 * Author:          Chris Kindred
 * Author URI:
 * Text Domain:     gci
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         GCI
 */

define( 'GCI_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'GCI_DIR_URL', plugin_dir_url( __FILE__ ) );

require_once GCI_DIR_PATH . 'vendor/autoload.php';

include_once( GCI_DIR_PATH . '/inc/class-gci-acf-integrate.php' );
include_once( GCI_DIR_PATH . '/inc/class-gci-backgroundprocess.php' );
include_once( GCI_DIR_PATH . '/inc/class-gci-ical-sync.php' );

new GCI_ICAL_Sync();
