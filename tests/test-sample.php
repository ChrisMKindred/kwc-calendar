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
}
