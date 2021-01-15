<?php

declare(strict_types=1);

/**
 * Abstract class for defining settings pages.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Settings_Pages
 */

namespace PinkCrab\Settings_Pages;

use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Settings_Pages\Settings_Group;
use PinkCrab\Settings_Pages\Settings_Collection;

abstract class Settings_Page implements Registerable {

	/**
	 * The pages key/slug
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * THe pages menu title
	 *
	 * @var string
	 */
	protected $menu_title;

	/**
	 * Pages position
	 *
	 * @var int|null
	 */
	protected $position;

	/**
	 * The pages title
	 *
	 * @var string
	 */
	protected $page_title;

	/**
	 * HTML to render before fields.
	 *
	 * @var string
	 */
	protected $before_fields = '';

	/**
	 * HTML to render after fields.
	 *
	 * @var string
	 */
	protected $after_fields = '';

	/**
	 * Sets the pages capability (role) requirements.
	 *
	 * @var string
	 */
	protected $capability = 'manage_options';

	/**
	 * Holds all predefined settings
	 *
	 * @var Settings_Collection
	 */
	protected $setting_groups;

	/**
	 * Registers page and options.
	 *
	 * @param \PinkCrab\Core\Services\Registration\Loader $loader
	 * @return void
	 */
	final public function register( Loader $loader ): void {
		// Create the settings collection.
		$this->setting_groups = Settings_Collection::from( array() );
		$this->add_settings( $this->setting_groups );

		// Allow for hooking in of more actions.
		$this->setup( $loader );

		// Registers the admin page.
		$loader->admin_action( 'admin_init', array( $this, 'register_settings' ) );

		// Registers any settings.
		$loader->admin_action( 'admin_menu', array( $this, 'register_page' ) );

		// Enqueues and custom css or js files.
		$loader->action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue' ) );
	}

	/**
	 * Allows the adding of settings.
	 *
	 * @param \PinkCrab\Settings_Pages\Settings_Collection $settings
	 * @return void
	 */
	protected function add_settings( Settings_Collection $settings ): void{}

	/**
	 * Runs the extendable enqueue function in child pages.
	 * If current viewing this page.
	 *
	 * @param string $page_slug Current page.
	 * @return void
	 */
	final public function maybe_enqueue( string $page_slug ): void {
		if ( 'settings_page_' . $this->key === $page_slug ) {
			$this->enqueue();
		}
	}

	/**
	 * Enqueue additional css and js files.
	 *
	 * @return void
	 */
	protected function enqueue(): void {}

	/**
	 * Allows for hooking in additional hooks for the page.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	protected function setup( Loader $loader ): void{}

	/**
	 * Renders the options page view.
	 *
	 * @return void
	 */
	final public function render_page(): void {
		printf( "<div class='wrap'><h1>%s</h1><form method='post' action='options.php'>%s", \esc_attr( $this->page_title ), wp_kses_post( $this->before_fields ) );
		settings_fields( $this->key );
		do_settings_sections( $this->key );
		submit_button();
		printf( '%s</form></div>', wp_kses_post( $this->after_fields ) );
	}

	/**
	 * Registers all the settings defined.
	 *
	 * @return void
	 */
	final public function register_settings(): void {
		$this->setting_groups->register();
	}

	/**
	 * Registers all the page..
	 *
	 * @return void
	 */
	final public function register_page(): void {
		\add_options_page(
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->key,
			array( $this, 'render_page' ),
			$this->position
		);
	}

}
