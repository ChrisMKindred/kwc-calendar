<?php
/**
 * This class handles most of the lifting for the plugin.
 *
 * @package gci
 */

/**
 * Sets up the WP Cron for the plugin
 *
 * @package GCI_Goolge_Calendar_Importer
 */
class GCI_ICAL_Sync {

	/**
	 * An Instance of the background process class.
	 *
	 * @var BackgroundProcess
	 * @since 0.0.0
	 */
	protected $background_process;

	/**
	 * Hooks and filters for the plugin.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'current_screen', array( $this, 'show_event_schedule' ) );
		add_filter( 'cron_schedules', array( $this, 'add_custom_cron_intervals' ), 10, 1 );
		add_action( 'acf/save_post', array( $this, 'run_first_sync' ), 20 );
		add_action( 'run_ical_sync', array( $this, 'run_ical_sync' ) );
		add_action( 'init', array( $this, 'is_events_calendar_activated' ) );

	}

	/**
	 * Checks if the events calendar plugin is activated.
	 */
	public function is_events_calendar_activated() {
		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			if ( current_user_can( 'activate_plugins' ) ) {
				add_action( 'admin_init', array( $this, 'plugin_deactivate' ) );
				add_action( 'admin_notices', array( $this, 'plugin_admin_notice' ) );
			}
			add_action( 'admin_init', array( $this, 'plugin_deactivate' ) );
		}
	}


	/**
	 * Deactivates the plugin.
	 */
	public function plugin_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	/**
	 * Shows an error message if events calendar plugin is not activated.
	 */
	function plugin_admin_notice() {
		echo '<div class="error"><p>The Events Calendar must be installed and activated before you can use <strong> Google Calendar Importer for The Events Calendar</strong>.</p></div>';
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	/**
	 * Instanciates the background processing class.
	 *
	 * @return void
	 */
	public function init() {

		if ( ! class_exists( 'gci_acf_integrate ' ) ) {
			error_log( print_r( 'calling acf', true ) );
			require_once  GCI_DIR_PATH . '/inc/class-gci-acf-integrate.php';
			new GCI_Acf_Integrate( 'gci', '1.0' );
		}
		require_once GCI_DIR_PATH . 'vendor/a5hleyrich/wp-background-processing/classes/wp-background-process.php';
		require_once GCI_DIR_PATH . 'vendor/a5hleyrich/wp-background-processing/classes/wp-async-request.php';

		$this->background_process = new GCI_BackgroundProcess();

	}

	/**
	 * Sets the cron job for running the sync.
	 *
	 * @return void
	 */
	public function set_cron_job() {
		$frequency = get_field( 'syncing_fequency', 'options' );
		if ( $frequency ) {
			if ( ! wp_next_scheduled( 'run_ical_sync' ) ) {
				error_log( print_r( 'Setting event:' . __FUNCTION__, true ) );
				wp_schedule_event( time(), $frequency, 'run_ical_sync' );
			}
		}
	}

	/**
	 * Run the inital ical sync and schedule the cron.
	 */
	public function run_first_sync() {
		$screen = get_current_screen();
		error_log( print_r( __FUNCTION__, true ) );
		if ( strpos( $screen->id, '-g-cal-importer' ) ) {
			if ( wp_next_scheduled( 'run_ical_sync' ) ) {
				$timestamp = wp_next_scheduled( 'run_ical_sync' );
				wp_unschedule_event( $timestamp, 'run_ical_sync' );
				error_log( print_r( 'removing event:' . __FUNCTION__, true ) );
			}
			$this->run_ical_sync();
		} else {
			error_log( print_r( $screen, true ) );
		}
	}

	/**
	 * Handles pushing the feeds background process. This method is ran when
	 * the feeds are saved as well as when the cron is called.
	 */
	public function run_ical_sync() {
		$feeds = $this->get_feeds();
		$this->delete_all_events();
		$this->queue_feeds( $feeds );
		$this->set_cron_job();
	}

	/**
	 * This is for testing purposes only.  The actually run time should be daily.
	 *
	 * @param array $schedules The Interval for recurring events.
	 * @return array
	 */
	public function add_custom_cron_intervals( $schedules ) {
		$five_minutes = 5 * MINUTE_IN_SECONDS;
		$two_minutes  = 2 * MINUTE_IN_SECONDS;

		$schedules['five_minutes'] = array(
			'interval' => $five_minutes,
			'display'  => 'Once Every 5 Minutes',
		);

		$schedules['two_minutes'] = array(
			'interval' => $two_minutes,
			'display'  => 'Once Every 2 Minutes',
		);

		return (array) $schedules;
	}


	/**
	 * Enqueues the action for showing the next schedule notice
	 *
	 * @return void
	 */
	public function show_event_schedule() {
		if ( is_admin() ) {
			$screen = get_current_screen();
			if ( strpos( $screen->id, '-g-cal-importer' ) ) {
				add_action( 'admin_notices', array( $this, 'plugin_scheduled_notice' ) );
			}
		}
	}

	/**
	 * Outputs the next run time in a notification box at the top of the screen.
	 */
	public function plugin_scheduled_notice() {
		$timestamp = wp_next_scheduled( 'run_ical_sync' );
		if ( $timestamp ) {
			$run_time = gmdate( 'm/d/Y h:i a', $timestamp );
			$wp_time  = gmdate( 'm/d/Y h:i a', $timestamp + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

			echo "<div class='notice notice-success is-dismissible'>
					<p>Next Scheduled Sync Time: {$run_time} - {$wp_time}</p>
				</div>";
		}
	}

	/**
	 * Loads the feeds from ACF Fields into an array to be used in the
	 * background processing.
	 *
	 * @return array
	 */
	protected function get_feeds() {
		$feeds = array();
		if ( have_rows( 'feeds', 'options' ) ) {
			while ( have_rows( 'feeds', 'options' ) ) {
				the_row();
				$feeds[] = array(
					'feed_url'      => get_sub_field( 'feed_url' ),
					'category_id'   => get_sub_field( 'category' ),
					'current_week'  => 0,
					'weeks_to_sync' => get_sub_field( 'weeks_to_sync_with_calendar' ),
					'user_id'       => '',
				);
			}
		}
		return (array) $feeds;
	}

	/**
	 * Queues out the feeds into the background process.
	 *
	 * @param array $feeds An array of the feeds from ACF Settins Page.
	 * @return void
	 */
	protected function queue_feeds( $feeds ) {
		foreach ( $feeds as $feed ) {
			$this->background_process->push_to_queue( $feed );
		}
		$this->background_process->save()->dispatch();
	}


	/**
	 * Deletes all the current events, runs before the first sync.
	 *
	 * @return void
	 */
	protected function delete_all_events() {

		global $wpdb;

		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			$wpdb->show_errors();
		}

		$result = $wpdb->query(
			$wpdb->prepare(
				"
				DELETE `{$wpdb->posts}`, `{$wpdb->postmeta}`, `{$wpdb->term_relationships}`
				FROM
				( `{$wpdb->posts}` LEFT JOIN `{$wpdb->postmeta}` ON `{$wpdb->postmeta}`.`post_id` = `{$wpdb->posts}`.`ID` )
				LEFT JOIN `{$wpdb->term_relationships}` on `{$wpdb->posts}`.`ID` = `{$wpdb->term_relationships}`.`object_id`
				WHERE `{$wpdb->posts}`.`post_type` = %s
				",
				'tribe_events'
			)
		);
	}
}
