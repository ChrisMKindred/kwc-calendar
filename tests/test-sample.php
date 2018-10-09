<?php
/**
 * Class SampleTest
 *
 * @package Plugin_Test
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {
	/**
	 * The main plugin variable
	 *
	 * @var KWC_CALENDAR
	 */
	protected $plugin;

	/**
	 * Setup the testing environment
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->plugin = new kwc_calendar();
		$this->plugin->run();
	}

	/**
	 * Tests for plugin slug.
	 *
	 * @covers kwc_calendar::get_kwc_calendar
	 */
	public function test_plugin_slug() {
		$plugin_slug = $this->plugin->get_kwc_calendar();
		$this->assertEquals( 'kwc-calendar', $plugin_slug );
	}

	/**
	 * Tests for plugin slug.
	 *
	 * @covers kwc_calendar_Admin::__construct
	 * @covers kwc_calendar_Admin::enqueue_scripts
	 */
	public function test_enqueue_scripts() {
		$plugin_admin = new kwc_calendar_Admin( $this->plugin->get_kwc_calendar(), $this->plugin->get_version() );
		var_dump( has_action( 'enqueue_scripts', 'admin_enqueue_scripts' ) );
		$this->assertGreaterThan( 0, has_action( 'enqueue_scripts', array( $plugin_admin, 'admin_enqueue_scripts' ) ) );
	}
}
