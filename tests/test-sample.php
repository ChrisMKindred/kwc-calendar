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
	protected $plugin;

	public function setUp(){
		parent::setUp();
		$this->plugin = new kwc_calendar();
		$this->plugin->run();
	}

	/**
	 * Tests for plugin slug.
	 * @covers kwc_calendar::get_kwc_calendar
	 */
	public function test_plugin_slug() {
		// Replace this with some actual testing code.
		$plugin_slug = $this->plugin->get_kwc_calendar();
		$this->assertEquals( 'kwc-calendar', $plugin_slug );
	}
	
	/**
	 * Tests for plugin slug.
	 * @covers kwc_calendar_Admin::__construct
	 * @covers kwc_calendar_Admin::enqueue_scripts
	 */
	public function test_enqueue_scripts() {
		// Replace this with some actual testing code.
		$this->assertGreaterThan(0, has_action( 'enqueue_scripts', array( $this->plugin->get_kwc_calendar(), 'enqueue_admin_scripts' ) ) );
	}
	
	/**
	 * Tests for plugin slug.
	 * @covers kwc_calendar_Admin::enqueue_styles
	 */
	public function test_enqueue_styles() {
		// Replace this with some actual testing code.
		$this->assertGreaterThan(0, has_action( 'enqueue_styles', array( $this->plugin->get_kwc_calendar(), 'enqueue_admin_styles' ) ) );
	}
}
