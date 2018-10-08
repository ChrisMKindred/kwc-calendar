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
	}
	/**
	 * Tests for plugin slug.
	 * @covers kwc_calendar::get_plugin_slug
	 */
	public function test_plugin_slug() {
		// Replace this with some actual testing code.
		$plugin_slug = kwc_calendar::get_plugin_slug();
		$this->assertEquals( 'kwc-calendar', $plugin_slug );
	}
}
