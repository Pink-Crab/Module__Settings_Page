<?php

declare(strict_types=1);

/**
 * A settings field data.
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

use Exception;
use TypeError;
use PinkCrab\Form_Fields\Abstract_Field;

class Settings_Field {

	/**
	 * The field/option key.
	 *
	 * @var string
	 */
	protected $option_key;

	/**
	 * The input field instance.
	 *
	 * @var Abstract_Field
	 */
	protected $input_field;

	/**
	 * The sanitization callback
	 *
	 * @var string|callable
	 */
	protected $santization_callback = '';

	/**
	 * Defines if shown in rest or not.
	 *
	 * @var bool|array
	 */
	protected $show_in_rest = true;

	/**
	 * Denotes the option stype
	 * Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'
	 *
	 * @var string
	 */
	protected $type = 'string';

	/**
	 * Construct instance with defiend key.
	 *
	 * @param string $option_key
	 */
	public function __construct( string $option_key ) {
		$this->option_key = $option_key;
	}

	/**
	 * Creates a setting field from a Input Field object.
	 *
	 * @param \PinkCrab\Form_Fields\Abstract_Field $field
	 * @return self
	 */
	public static function from_field( Abstract_Field $field ): self {
		$field_key = $field->get_key();
		$instance  = new self( $field_key );
		$instance->input_field( $field );
		return $instance;
	}

	/**
	 * Set the input field instance.
	 *
	 * @param Abstract_Field $input_field  The input field instance.
	 *
	 * @return self
	 */
	public function input_field( Abstract_Field $input_field ): self {
		$this->input_field = $input_field;
		return $this;
	}

	/**
	 * Sets the field with the current options table value.
	 *
	 * @param mixed $value
	 * @return self
	 */
	public function set_current_input_value( $value ): self {
		$this->input_field->current( $value );
		return $this;
	}


	/**
	 * Set valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'
	 *
	 * @param string $type  Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'
	 * @return self
	 * @throws Exception
	 */
	public function type( string $type ): self {
		if ( ! in_array(
			$type,
			array( 'string', 'boolean', 'integer', 'number', 'array', 'object' ),
			true
		)
		) {
			throw new Exception( $type . ' is an invalid type.' );

		}

		$this->type = $type;
		return $this;
	}


	/**
	 * Set the sanitization callback
	 *
	 * @param string|callable|void $santization_callback  The sanitization callback
	 * @return self
	 * @throws TypeError.
	 */
	public function santization_callback( $santization_callback ): self {
		if ( is_string( $santization_callback )
			|| \is_callable( $santization_callback )
		) {
			$this->santization_callback = $santization_callback;
			return $this;
		} else {
			throw new TypeError( 'Only strings or callables allowed to be used for sanitization callbacks.' );
		}
	}


	/**
	 * Set defines if shown in rest or not.
	 *
	 * @param bool|array $show_in_rest  Defines if shown in rest or not.
	 * @return self
	 * @throws TypeError.
	 */
	public function show_in_rest( $show_in_rest ): self {
		if ( ! in_array(
			gettype( $show_in_rest ),
			array( 'boolean', 'array' ),
			true
		) ) {
			throw new TypeError( 'Only bool or arrays allowed for show_in_rest' );
		}

		$this->show_in_rest = $show_in_rest;
		return $this;
	}


	/**
	 * Get the sanitization callback
	 *
	 * @return string|callable
	 */
	public function get_santization_callback() {
		return $this->santization_callback;
	}


	/**
	 * Get valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}


	/**
	 * Get defines if shown in rest or not.
	 *
	 * @return bool|array
	 */
	public function get_show_in_rest() {
		return $this->show_in_rest;
	}

	/**
	 * Get the input field instance.
	 *
	 * @return Abstract_Field
	 */
	public function get_input_field(): Abstract_Field {
		return $this->input_field;
	}

	/**
	 * Get the field/option key.
	 *
	 * @return string
	 */
	public function get_option_key(): string {
		return $this->option_key;
	}

	/**
	 * Gets the label from the input field.
	 *
	 * @return string
	 */
	public function get_input_label(): string {
		return $this->input_field->get_label();
	}

	/**
	 * Returns the defined inputs, defualt value.
	 *
	 * @return void
	 */
	public function get_input_default() {
		return $this->input_field->get_default();
	}
}
