<?php

declare(strict_types=1);

/**
 * Tests against a registered page.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Modules\Settings_Page
 */

namespace PinkCrab\Modules\Settings_Page\Tests;

use WP_UnitTestCase;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Modules\Settings_Page\Settings_Page;
use PinkCrab\Modules\Settings_Page\Tests\Mocks\Single_Group_Settings;

require_once 'Mocks/Single_Group_Settings.php';

class Test_Single_Group_Settings extends WP_UnitTestCase {

	protected static $page;
	protected static $rendered_page;
	protected static $initialized = false;

	public function setup(): void {
		parent::setup();

		// Only run once.
		if ( ! self::$initialized ) {
			$this->construct_fixtures();
			self::$initialized = true;
		}
	}

	protected function construct_fixtures(): void {

		// Mock admin user in wp-admin.
		global $submenu, $menu;
		$current_user = get_current_user_id();
		$admin_user   = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );

		// Setup/Register group and loader
		self::$page = new Single_Group_Settings();
		$loader     = new Loader();
		self::$page->register( $loader );
		set_current_screen( self::$page->_get_key() );

		$loader->register_hooks();

		// Trigger settings page registration.
		self::$page->register_settings();
		do_action( 'admin_menu' );
		do_action( 'admin_enqueue_scripts', 'settings_page_' . self::$page->_get_key() );

		$this->render_page();
	}

	/**
	 * Renders the page.
	 *
	 * @return void
	 */
	public function render_page() {
		ob_start();
		self::$page->render_page();
		self::$rendered_page = ob_get_contents();
		ob_end_clean();
	}

	/**
	 * Main runner of all tests.
	 * Due to the way phpunit works, these are all done in one test.
	 * Otherwise we need to register the page multiple times.
	 *
	 * @return void
	 */
	public function test_runner(): void {
		$this->_test_page_exists();
		$this->_test_page_created();
		$this->_test_enqueue_was_called();
		$this->_test_additional_hook_loader();
		$this->_test_renders_form();
	}

	/**
	 * Test the class has been constructed.
	 *
	 * @return void
	 */
	public function _test_page_exists(): void {
		$this->assertInstanceOf( Settings_Page::class, self::$page );
	}

	/**
	 * Tests that when registered the page is added to the submeny list.
	 *
	 * @return void
	 */
	public function _test_page_created(): void {
		global $submenu, $menu;

		// Check valid page url generated.
		$this->assertNotEmpty( menu_page_url( self::$page->_get_key(), false ) );
		// Check general settings has a page defined as a sub menu.
		$this->assertArrayHasKey( 'options-general.php', $submenu );

		// Get the menu details.
		$page = array_filter(
			$submenu['options-general.php'],
			function( $e ) {
				return $e[2] === self::$page->_get_key();
			}
		);

		// Check sub meny has page.
		$this->assertNotEmpty( $page );
		$this->assertEquals( 'My Settings(MENU)', $page[0][0] );
		$this->assertEquals( 'manage_options', $page[0][1] );
		$this->assertEquals( 'my_settings_key', $page[0][2] );
		$this->assertEquals( 'My Settings(PAGE)', $page[0][3] );
	}

	/**
	 * Test the script is enqueued.
	 *
	 * @return void
	 */
	public function _test_enqueue_was_called(): void {
		$this->assertArrayHasKey( 'my_settings_ENQUEUE', $GLOBALS['wp_scripts']->registered );
	}

	/**
	 * Test the additional hooks are fired.
	 *
	 * @return void
	 */
	public function _test_additional_hook_loader(): void {
		$this->assertArrayHasKey( 'my_settings_action', $GLOBALS['wp_filter'] );
	}

	/**
	 * Tests that the rendered form has the values expected.
	 *
	 * @return void
	 */
	public function _test_renders_form(): void {
		// Page Details
		$this->assertTrue( \str_contains( self::$rendered_page, 'My Settings(PAGE)' ) );
		$this->assertTrue( \str_contains( self::$rendered_page, 'BEFORE_TEXT' ) );
		$this->assertTrue( \str_contains( self::$rendered_page, 'AFTER_TEXT' ) );
		$this->assertTrue( \str_contains( self::$rendered_page, 'my_settings_key' ) );

		// Group Details
		$this->assertTrue( \str_contains( self::$rendered_page, 'Single_Group_Settings GROUP' ) );
		$this->assertTrue( \str_contains( self::$rendered_page, 'GROUP DESCRIPTION' ) );

		// Input
		$this->assertTrue( \str_contains( self::$rendered_page, 'STRING INPUT' ) );
		$this->assertTrue( \str_contains( self::$rendered_page, 'single_test_string' ) );
		$this->assertTrue( \str_contains( self::$rendered_page, 'INT INPUT' ) );
		$this->assertTrue( \str_contains( self::$rendered_page, 'single_test_int' ) );
	}
}
