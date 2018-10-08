<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    kwc_calendar
 * @subpackage kwc_calendar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    kwc_calendar
 * @subpackage kwc_calendar/admin
 * @author     Your Name <email@example.com>
 */
class kwc_calendar_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $kwc_calendar    The ID of this plugin.
	 */
	private $kwc_calendar;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $kwc_calendar       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $kwc_calendar, $version ) {

		$this->kwc_calendar = $kwc_calendar;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in kwc_calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The kwc_calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->kwc_calendar, plugin_dir_url( __FILE__ ) . 'css/kwc-calendar-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in kwc_calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The kwc_calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->kwc_calendar, plugin_dir_url( __FILE__ ) . 'js/kwc-calendar-admin.js', array( 'jquery' ), $this->version, false );

	}

}
