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
use PinkCrab\Modules\Form_Fields\Fields\Input_Text;

class Test_Settings_Field extends WP_UnitTestCase {

	/**
	 * Test constructor can be used to create insance with key.
	 *
	 * @return void
	 */
	public function test_can_create_using_constructor(): void {
		$field = new Settings_Field( 'test_option' );
		$this->assertInstanceOf( Settings_Field::class, $field );
		$this->assertEquals( 'test_option', $field->get_option_key() );
	}

	/**
	 * Tests that an input can be set and got from the field.
	 *
	 * @uses _getPrivateProperty() found in pinkcrab test bootstrap.
	 * @return void
	 */
	public function test_can_set_and_get_input_filed(): void {
		$input = Input_Text::create( 'field_key', 'Test Field' );
		$field = new Settings_Field( 'test_option' );
		$field->input_field( $input );
		$this->assertSame( $input, \_getPrivateProperty( $field, 'input_field' ) );
		$this->assertSame( $input, $field->get_input_field() );
	}

	/**
	 * Test the static ::from_field() can be used.
	 *
	 * @return void
	 */
	public function test_can_be_created_from_field(): void {
		$input = Input_Text::create( 'field_key', 'Test Field' );
		$field = Settings_Field::from_field( $input );
		$this->assertInstanceOf( Settings_Field::class, $field );
		$this->assertEquals( 'field_key', $field->get_option_key() );
	}

	/**
	 * Test the default values.
	 *
	 * @uses _getPrivateProperty() found in pinkcrab test bootstrap.
	 * @return void
	 */
	public function test_default_values() {
		$field = new Settings_Field( 'test_option' );
		$this->assertSame( '', \_getPrivateProperty( $field, 'santization_callback' ) );
		$this->assertSame( 'string', \_getPrivateProperty( $field, 'type' ) );
		$this->assertSame( true, \_getPrivateProperty( $field, 'show_in_rest' ) );
	}

	/**
	 * Tests that a sanitization callback can be assgined.
	 *
	 * @uses _getPrivateProperty() found in pinkcrab test bootstrap.
	 * @return void
	 */
	public function test_can_set_sanitization_callable(): void {
		$field = new Settings_Field( 'test_option' );

		// With callable
		$function = function( $value ): string {
			return (string) 123;
		};
		$field->santization_callback( $function );
		$this->assertSame( $function, \_getPrivateProperty( $field, 'santization_callback' ) );
		$this->assertSame( $function, $field->get_santization_callback() );

		// With string.
		$field->santization_callback( 'test' );
		$this->assertSame( 'test', \_getPrivateProperty( $field, 'santization_callback' ) );
		$this->assertSame( 'test', $field->get_santization_callback() );
	}

	/**
	 * Test that onnly string or callable can be passed.
	 *
	 * @return void
	 */
	public function test_santization_callback_throws_for_wrong_type(): void {
		$this->expectException( TypeError::class );
		$field = new Settings_Field( 'test_option' );
		$field->santization_callback( array( 'function' ) );

	}

	/**
	 * Test can set show in rest property.
	 *
	 * @uses _getPrivateProperty() found in pinkcrab test bootstrap.
	 * @return void
	 */
	public function test_can_set_show_in_rest(): void {
		$field = new Settings_Field( 'test_option' );

		// As bool
		$field->show_in_rest( false );
		$this->assertFalse( \_getPrivateProperty( $field, 'show_in_rest' ) );
		$this->assertFalse( $field->get_show_in_rest() );

		// As array
		$field->show_in_rest( array( '1' ) );
		$this->assertTrue( is_array( \_getPrivateProperty( $field, 'show_in_rest' ) ) );
		$this->assertTrue( is_array( $field->get_show_in_rest() ) );
	}

	/**
	 * Test that onnly string or callable can be passed.
	 *
	 * @return void
	 */
	public function test_show_in_rest_throws_for_wrong_type(): void {
		$this->expectException( TypeError::class );
		$field = new Settings_Field( 'test_option' );
		$field->show_in_rest( (object) array( 'function' ) );

	}

	/**
	 * Test that the expected type can be set.
	 *
	 * @uses _getPrivateProperty() found in pinkcrab test bootstrap.
	 * @return void
	 */
	public function test_can_set_expected_type(): void {
		$field = new Settings_Field( 'test_option' );
		$field->type( 'number' );
		$this->assertEquals( 'number', \_getPrivateProperty( $field, 'type' ) );
		$this->assertEquals( 'number', $field->get_type() );

	}

	/**
	 * Test that allowed types can be set.
	 *
	 * @return void
	 */
	public function test_type_allowed_types(): void {

		$field = new Settings_Field( 'test_option' );

		// Test for allowed types.
		$types = array( 'string', 'boolean', 'integer', 'number', 'array', 'object' );
		foreach ( $types as $type ) {
			$field->type( $type );
			$this->assertEquals( $type, $field->get_type() );
		}
	}

	/**
	 * Ensure Exception thrown if incorrect type defined.
	 *
	 * @return void
	 */
	public function test_type_dissallowed_types(): void {
		$this->expectException( Exception::class );
		$field = new Settings_Field( 'test_option' );
		$field->type( 'ILLEGAL' );
	}

	/**
	 * Test that the defined field can have its current value set.
	 *
	 * @uses _getPrivateProperty() found in pinkcrab test bootstrap.
	 * @return void
	 */
	public function test_can_set_input_value(): void {
		$input = Input_Text::create( 'field_key', 'Test Field' );

		$field = Settings_Field::from_field( $input );
		$field->set_current_input_value( 'CURRENT' );

		$this->assertEquals( 'CURRENT', $field->get_input_field()->get_current() );
	}

	/**
	 * Test can get label from input field.
	 *
	 * @return void
	 */
	public function test_can_get_input_field(): void {
		$input = Input_Text::create( 'field_key', 'Test Field' );
		$field = Settings_Field::from_field( $input );
		$this->assertEquals( 'Test Field', $field->get_input_label() );
	}

	/**
	 * Test can get the option key.
	 *
	 * @return void
	 */
	public function test_can_get_option_key(): void {
		$field = new Settings_Field( 'test_option' );
		$this->assertEquals( 'test_option', $field->get_option_key() );
	}

	public function test_get_input_default(): void {
		$input = Input_Text::create( 'field_key', 'Test Field' )
			->default( 'HI' );
		$field = Settings_Field::from_field( $input );
		$this->assertEquals( 'HI', $field->get_input_default() );
	}
}
