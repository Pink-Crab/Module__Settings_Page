<?php

declare(strict_types=1);

namespace PinkCrab\My_Plugin\Settings_Pages;

use PinkCrab\Modules\Settings_Page\Settings_Page;
use PinkCrab\Modules\Settings_Page\Settings_Field;
use PinkCrab\Modules\Settings_Page\Settings_Group;
use PinkCrab\Modules\Form_Fields\Fields\Input_Text;
use PinkCrab\Modules\Settings_Page\Settings_Collection;

class Single_Group_Settings extends Settings_Page {

	// Set page values.
	protected $key           = 'my_settings_key';
	protected $menu_title    = 'My Settings(MENU)';
	protected $page_title    = 'My Settings(PAGE)';
	protected $before_fields = 'BEFORE_TEXT';
	protected $after_fields  = 'AFTER TEXT';

	/**
	 * Register our groups of settings.
	 *
	 * @param \PinkCrab\Modules\Settings_Page\Settings_Collection $settings
	 * @return void
	 */
	protected function add_settings( Settings_Collection $settings ): void {
		$settings->add(
			Settings_Group::create(
				'my_api_settings',
				'My Group',
				$this->key
			)
			// Give the group(section) a description.
			->description( 'Please fill in the details, as required for some features' )
			// Api Key field
			->add_field(
				Settings_Field::from_field(
					Input_Text::create( 'ache_api_key', 'Api Key' )
				)
				->type( 'string' )
				->santization_callback( 'sanitize_text_field' )
			)
			// Api url
			->add_field(
				Settings_Field::from_field(
					Input_Text::create( 'ache_api_url', 'Api Url' )
				)
				->type( 'string' )
				->santization_callback( 'esc_url_raw' )
			)
		);
	}
}

