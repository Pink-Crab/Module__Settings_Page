<?php

declare(strict_types=1);

/**
 * Tests the Settings Field object.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Settings_Pages
 */

namespace PinkCrab\Settings_Pages\Tests;

use Exception;
use TypeError;
use WP_UnitTestCase;
use PinkCrab\Settings_Pages\Settings_Field;
use PinkCrab\Settings_Pages\Settings_Group;
use PinkCrab\Modules\Form_Fields\Fields\Input_Text;

class Test_Settings_Group extends WP_UnitTestCase {

	/**
	 * Test constructor can be used to create insance with key.
	 *
	 * @uses _getPrivateProperty()
	 * @return void
	 */
	public function test_can_create_using_constructor(): void {
		$group = new Settings_Group( 'key', 'label', 'slug' );
		$this->assertInstanceOf( Settings_Group::class, $group );
		$this->assertEquals( 'key', \_getPrivateProperty( $group, 'group_key' ) );
		$this->assertEquals( 'label', \_getPrivateProperty( $group, 'group_label' ) );
		$this->assertEquals( 'slug', \_getPrivateProperty( $group, 'page_slug' ) );
	}

	/**
	 * Test static create() popules with a valid object.
	 *
	 * @uses _getPrivateProperty()
	 * @return void
	 */
	public function test_can_construct_with_static_create(): void {
		$group = Settings_Group::create( 'key', 'label', 'slug' );
		$this->assertInstanceOf( Settings_Group::class, $group );
		$this->assertEquals( 'key', \_getPrivateProperty( $group, 'group_key' ) );
		$this->assertEquals( 'label', \_getPrivateProperty( $group, 'group_label' ) );
		$this->assertEquals( 'slug', \_getPrivateProperty( $group, 'page_slug' ) );
	}

	/**
	 * Test can get group key.
	 *
	 * @return void
	 */
	public function test_can_get_group_key(): void {
		$group = Settings_Group::create( 'key', 'label', 'slug' );
		$this->assertEquals( 'key', $group->get_group_key() );
		$this->assertNotEquals( 'TEST', $group->get_group_key() );
	}

	/**
	 * Test can get group key.
	 *
	 * @return void
	 */
	public function test_can_get_page_slug(): void {
		$group = Settings_Group::create( 'key', 'label', 'slug' );
		$this->assertEquals( 'slug', $group->get_page_slug() );
		$this->assertNotEquals( 'TEST', $group->get_page_slug() );
	}

	/**
	 * Test a group can have a description assigned.
	 *
	 * @uses _getPrivateProperty()
	 * @return void
	 */
	public function test_can_set_group_description(): void {
		$group = Settings_Group::create( 'key', 'label', 'slug' );
		$group->description( 'ADDED DESCRIPTION' );
		$this->assertEquals( 'ADDED DESCRIPTION', \_getPrivateProperty( $group, 'group_description' ) );
	}

	/**
	 * Test that a setting can be added to the group.
	 *
	 * @uses _getPrivateProperty()
	 * @return void
	 */
	public function test_can_add_field_to_group(): void {
		// Create input & field
		$input = Input_Text::create( 'field_key', 'Test Field' );
		$field = Settings_Field::from_field( $input );

		$group = Settings_Group::create( 'key', 'label', 'slug' );
		$group->add_field( $field );

		$this->assertCount( 1, \_getPrivateProperty( $group, 'fields' ) );
		$this->assertInstanceOf( Settings_Field::class, \_getPrivateProperty( $group, 'fields' )['field_key'] );
	}

	/**
	 * Test that the add_field sets the current value of the input
	 * with the fields default if option is empty.
	 *
	 * @uses _getPrivateProperty()
	 * @return void
	 */
	public function test_input_default_used_if_option_unset(): void {
		$option_key = 'test_input_default_used_if_option_unset';
		// Ensure option is empty.
		\delete_option( $option_key );

		$input = Input_Text::create( $option_key, 'Test Field' )
			->default( 'DEFAULT' );
		$field = Settings_Field::from_field( $input );

		$group = Settings_Group::create( 'key', 'label', 'slug' );
		$group->add_field( $field );

		// Get field from group.
		$field_from = \_getPrivateProperty( $group, 'fields' )[ $option_key ];

		$this->assertEquals( 'DEFAULT', $field_from->get_input_field()->get_current() );
	}

	/** AN EXAMPLE OF A GROUP BEING REGISTERED CAN BE FOUND IN THE COLLECTIONS TESTS. */
}
