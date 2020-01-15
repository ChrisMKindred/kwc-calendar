<?php
/**
 * Queues of the loading of ACF.
 *
 * @package GCI
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Integrate ACF if needed
 *
 * @author     André R. Kohl <code@sixtyseven.info>
 */
class GCI_Acf_Integrate {
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name = 'GCI';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version = '1.0.0';

	/**
	 * The modus of ACF: Either "installed" if found as a plugin, "bundeled" when used via include ore false if not found
	 *
	 * @since    1.0.0
	 * @access   public static
	 * @var      string    $acf_modus    The used modus.
	 */
	public static $acf_modus;

	/**
	 * The path to the bundeled ACF
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $acf_dir    The path to the folder.
	 */
	protected $acf_dir;

	/**
	 * The URL to the bundeled ACF
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $acf_url    The url to the folder.
	 */
	protected $acf_url;

	/**
	 * The path to the json files
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $acf_json    The path to the folder.
	 */
	protected $acf_json;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $name = false, $version = false ) {
		error_log( print_r( 'construct', true ) );
		if ( $name ) {
			$this->plugin_name = $name;
		}

		if ( $version ) {
			$this->plugin_version = $version;
		}

		$this->acf_dir = GCI_DIR_PATH . '/lib/acf/';
		$this->acf_url = GCI_DIR_URL . '/lib/acf/';

		if ( class_exists( 'acf' ) ) {
			self::$acf_modus = 'installed';
		} elseif ( file_exists( $this->acf_dir . 'acf.php' ) ) {
			self::$acf_modus = 'bundeled';
		} else {
			self::$acf_modus = false;
		}
		error_log( print_r( self::$acf_modus, true ) );
		$this->init();
	}

	/**
	 * Initiate the integration
	 *
	 * @since    1.0.0
	 */
	private function init() {
		if ( ! self::$acf_modus ) {
			return;
		}

		if ( 'bundeled' === self::$acf_modus ) {

			add_filter( 'acf/settings/path', array( $this, 'acf_settings_path' ) );
			add_filter( 'acf/settings/dir', array( $this, 'acf_settings_dir' ) );
			add_filter( 'site_transient_update_plugins', array( $this, 'stop_acf_update_notifications' ), 11 );
			require_once( $this->acf_dir . 'acf.php' );

		}

		if ( defined( 'SHOW_ACF' ) && false === SHOW_ACF ) {
			add_filter( 'acf/settings/show_admin', '__return_false' );
		}

		add_action( 'acf/init', array( $this, 'register_acf_options_pages' ) );
		add_action( 'acf/init', array( $this, 'add_local_field_groups' ) );
	}

	/**
	 * Filters the path to the ACF folder
	 *
	 * @since    1.0.0
	 * @param string $path The path to ACF Dir.
	 */
	public function acf_settings_path( $path ) {
		$path = $this->acf_dir;
		return $path;
	}

	/**
	 * Filters the URL to the ACF folder
	 *
	 * @since    1.0.0
	 * @param string $path The URL path to ACF URL.
	 */
	public function acf_settings_dir( $path ) {
		$path = $this->acf_url;
		return $path;
	}

	/**
	 * Stops the upgrade notifications of ACF
	 *
	 * @since    1.0.0
	 * @param string $value The update notification value.
	 */
	public function stop_acf_update_notifications( $value ) {
		unset( $value->response[ $this->acf_dir . 'acf.php' ] );
		return $value;
	}

	/**
	 * Returns the current value of acf_modus for use in plugins or themes
	 *
	 * @since    1.0.0
	 */
	public static function acf_modus() {
		return self::$acf_modus;
	}

	/**
	 * Adds the local verion of the files.
	 *
	 * @return void
	 */
	public function add_local_field_groups() {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_5d66f0d0c9204',
				'title'                 => 'Calendar Feed Options',
				'fields'                => array(
					array(
						'key'               => 'field_5d9205e7d8a4e',
						'label'             => 'Syncing Fequency',
						'name'              => 'syncing_fequency',
						'type'              => 'select',
						'instructions'      => 'Select how often you would like to sync the calendar.	You can always manually sync by selecting the sync now button.',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
							'daily'        => 'Daily',
							'twicedaily'   => 'Twice a Day',
							'hourly'       => 'Hourly',
							'two_minutes'  => 'Every 2 Minutes',
							'five_minutes' => 'Every 5 Minutes',
						),
						'default_value'     => array(
							0 => 'daily',
						),
						'allow_null'        => 0,
						'multiple'          => 0,
						'ui'                => 1,
						'ajax'              => 0,
						'return_format'     => 'value',
						'placeholder'       => '',
					),
					array(
						'key'               => 'field_5d66f0e92cfc3',
						'label'             => 'Feeds',
						'name'              => 'feeds',
						'type'              => 'repeater',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'collapsed'         => '',
						'min'               => 0,
						'max'               => 0,
						'layout'            => 'block',
						'button_label'      => 'Add Feed',
						'sub_fields'        => array(
							array(
								'key'               => 'field_5d66f0f12cfc4',
								'label'             => 'Category',
								'name'              => 'category',
								'type'              => 'taxonomy',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '50',
									'class' => '',
									'id'    => '',
								),
								'taxonomy'          => 'tribe_events_cat',
								'field_type'        => 'radio',
								'allow_null'        => 1,
								'add_term'          => 1,
								'save_terms'        => 0,
								'load_terms'        => 0,
								'return_format'     => 'id',
								'multiple'          => 0,
							),
							array(
								'key'               => 'field_5d9204c91dd82',
								'label'             => 'Weeks to Sync with Calendar',
								'name'              => 'weeks_to_sync_with_calendar',
								'type'              => 'number',
								'instructions'      => 'The number of weeks that will be pulled into the Events Calendar from the Google Calendar Feed. This defaults to 52 weeks.	The more weeks you sync the longer the sync will take to complete.',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '50',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => 52,
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'min'               => '',
								'max'               => 156,
								'step'              => '',
							),
							array(
								'key'               => 'field_5d66f1082cfc5',
								'label'             => 'Feed URL',
								'name'              => 'feed_url',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '100',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
								'maxlength'         => '',
							),
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => 'gci-g-cal-importer',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'acf_after_title',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);
	}

	/**
	 * Adds the submenu settings page for the plugin.
	 *
	 * @uses Action: admin_menu
	 */
	public function register_acf_options_pages() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title'  => 'G-Cal Importer',
					'menu_title'  => 'G-Cal Importer',
					'menu_slug'   => 'gci-g-cal-importer',
					'capability'  => 'edit_posts',
					'redirect'    => false,
					'parent_slug' => 'edit.php?post_type=tribe_events',
				)
			);
		}
	}
}
