<?php

declare(strict_types=1);

namespace PinkCrab\Settings_Pages\Tests\Mocks;

use PinkCrab\Settings_Pages\Settings_Page;
use PinkCrab\Form_Fields\Fields\Input_Text;
use PinkCrab\Settings_Pages\Settings_Field;
use PinkCrab\Settings_Pages\Settings_Group;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Settings_Pages\Settings_Collection;

class Single_Group_Settings extends Settings_Page {

	// Set page values.
	protected $key           = 'my_settings_key';
	protected $menu_title    = 'My Settings(MENU)';
	protected $page_title    = 'My Settings(PAGE)';
	protected $before_fields = 'BEFORE_TEXT';
	protected $after_fields  = 'AFTER_TEXT';

	/**
	 * Register our groups of settings.
	 *
	 * @param \PinkCrab\Settings_Pages\Settings_Collection $settings
	 * @return void
	 */
	protected function add_settings( Settings_Collection $settings ): void {
		$settings->add(
			Settings_Group::create(
				'single_group_settings',
				'Single_Group_Settings GROUP',
				$this->key
			)
			->description( 'GROUP DESCRIPTION' )
			->add_field(
				Settings_Field::from_field(
					Input_Text::create( 'single_test_string' )->label( 'STRING INPUT' )
				)
				->type( 'string' )
				->santization_callback( 'sanitize_text_field' )
			)
			->add_field(
				Settings_Field::from_field(
					Input_Text::create( 'single_test_int' )->label( 'INT INPUT' )
				)
				->type( 'number' )
				->santization_callback( 'intval' )
			)
		);
	}

	/**
	 * Enqueues a fake file.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		wp_enqueue_script(
			'my_settings_ENQUEUE',
			get_stylesheet_directory_uri() . '/js/custom_script.js',
			array( 'jquery' )
		);
	}

	/**
	 * Load fake filter
	 *
	 * @param \PinkCrab\Core\Services\Registration\Loader $loader
	 * @return void
	 */
	public function setup( Loader $loader ): void {
		$loader->action(
			'my_settings_action',
			static function( $e ) {
				dump( 'HELLO' );
			}
		);
	}

	/** HELPER TEST FUNCTIONS */

	public function _get_key(): string {
		return $this->key;
	}
}

