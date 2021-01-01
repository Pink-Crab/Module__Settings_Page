<?php

declare(strict_types=1);

/**
 * Loader tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Modules\Settings_Page
 */

namespace PinkCrab\Modules\Settings_Page\Tests;

use WP_UnitTestCase;
use PinkCrab\Core\Collection\Collection;
use PinkCrab\Modules\Settings_Page\Settings_Group;
use PinkCrab\Modules\Settings_Page\Settings_Collection;

class Test_Settings_Collection extends WP_UnitTestCase {

	/**
	 * Tests the Settings Collection is an instnce of PinkCrab Collection.
	 *
	 * @return void
	 */
	public function test_is_instance_of_collection(): void {
		$collection = new Settings_Collection( array() );
		$this->assertInstanceOf( Collection::class, $collection );
	}

	/**
	 * Ensure that only collection groups can be added.
	 *
	 * @return void
	 */
	public function test_can_add_settings_group(): void {

		$group = Settings_Group::create( 'key', 'label', 'slug' );

		$collection = new Settings_Collection( array() );
		$collection->add( $group );

		$this->assertFalse( $collection->is_empty() );
		$this->assertInstanceOf( Settings_Group::class, $collection->pop() );
	}

	/**
	 * Test group/section is registered.
	 *
	 * @return void
	 */
	public function test_register_groups(): void {

		$group      = Settings_Group::create( 'section_key', 'label', 'page_slug' );
		$collection = new Settings_Collection( array() );
		$collection->add( $group );
		$collection->register();

		// Check group added for page & section..
		global $wp_settings_sections;
		$this->assertArrayHasKey( 'page_slug', $wp_settings_sections );
		$this->assertArrayHasKey( 'section_key', $wp_settings_sections['page_slug'] );
	}
}
