<?php

declare(strict_types=1);

/**
 * Holds a settings group and its fields.
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
 * @package PinkCrab\Modules\Settings_Page
 */

namespace PinkCrab\Modules\Settings_Page;

use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Modules\Form_Fields\Fields\Abstract_Field;


final class Settings_Group {

	/**
	 * Holds the key of the group.
	 *
	 * @var string
	 */
	protected $group_key;

	/**
	 * Holds the groups label
	 *
	 * @var string
	 */
	protected $group_label;

	/**
	 * Description shown after title.
	 *
	 * @var string|null
	 */
	protected $group_description;

	/**
	 * Denotes the page slug to render.
	 *
	 * @var string
	 */
	protected $page_slug;

	/**
	 * Holds all the fields to be assigned to this group.
	 *
	 * @var array<string, Settings_Field >
	 */
	protected $fields = array();

	/**
	 * Creates an instance of a settings group.
	 *
	 * @param string $group_key
	 */
	public function __construct(
		string $group_key,
		string $group_label,
		string $page_slug
	) {
		$this->group_key   = $group_key;
		$this->group_label = $group_label;
		$this->page_slug   = $page_slug;
	}

	/**
	 * Create statically.
	 *
	 * @param string $group_key
	 * @param string $group_label
	 * @return self
	 */
	public static function create(
		string $group_key,
		string $group_label,
		string $page_slug
	): self {
		return new static( $group_key, $group_label, $page_slug );
	}

	/**
	 * Sets a field to group.
	 *
	 * @param Settings_Field $field
	 * @return self
	 */
	public function add_field( Settings_Field $field ): self {
		$field->set_current_input_value( \get_option( $field->get_option_key(), $field->get_input_default() ) );
		$this->fields[ $field->get_option_key() ] = $field;
		return $this;
	}

	/**
	 * Registers the settings group.
	 *
	 * @return void
	 */
	public function register_group() {
		// Register Section
		\add_settings_section(
			$this->group_key,
			$this->group_label,
			function( array $args ): void {
				if ( $this->group_description ) {
					print( "<p>{$this->group_description}</p>" );
				}
			},
			$this->page_slug
		);

		// Loop through, register fields and settings.
		foreach ( $this->fields as $key => $field ) {
			// Register the field.
			\add_settings_field(
				$field->get_option_key(),
				$field->get_input_label(),
				array( $field->get_input_field(), 'render' ),
				$this->page_slug,
				$this->group_key
			);
			// Register settings.
			\register_setting(
				$this->page_slug,
				$field->get_option_key(),
				array(
					'sanitize_callback' => $field->get_santization_callback() ?? '',
					'show_in_rest'      => $field->get_show_in_rest() ?? true,
					'type'              => $field->get_type() ?? 'string',
					'default'           => $field->get_input_field()->get_default() ?? '',
				)
			);
		}
	}


	/**
	 * Get holds the key of the group.
	 *
	 * @return string
	 */
	public function get_group_key(): string {
		return $this->group_key;
	}

	/**
	 * Get denotes the page slug to render.
	 *
	 * @return string
	 */
	public function get_page_slug(): string {
		return $this->page_slug;
	}

	/**
	 * Set description shown after title.
	 *
	 * @param string|null $group_description  Description shown after title.
	 * @return self
	 */
	public function description( $group_description ): self {
		$this->group_description = $group_description;
		return $this;
	}


}
