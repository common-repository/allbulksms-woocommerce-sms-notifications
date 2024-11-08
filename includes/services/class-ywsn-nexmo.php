<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWSN_Nexmo' ) ) {

	/**
	 * Implements Nexmo API for YWSN plugin
	 *
	 * @class   YWSN_Nexmo
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Nexmo extends YWSN_SMS_Gateway {

		/** @var string nexmo api key */
		private $_nexmo_api_key;

		/** @var string nexmo api secret */
		private $_nexmo_api_secret;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_nexmo_api_key    = get_option( 'ywsn_nexmo_api_key' );
			$this->_nexmo_api_secret = get_option( 'ywsn_nexmo_api_secret' );

			parent::__construct();

		}

		/**
		 * Send SMS
		 *
		 * @since   1.0.0
		 *
		 * @param   $to_phone
		 * @param   $message
		 * @param   $country_code
		 *
		 * @return  array
		 * @throws  Exception for WP HTTP API error, no response, HTTP status code is not 201 or if HTTP status code not set
		 * @author  Alberto Ruggiero
		 */
		public function send( $to_phone, $message, $country_code ) {

			if ( '' != $this->_from_asid ) {

				$from = $this->_from_asid;

			} else {

				$from = $this->_from_number;

			}

			$type = empty( apply_filters( 'ywsn_additional_charsets', array() ) ) ? 'text' : 'unicode';


			$args = http_build_query( array(
				                          'from'       => $from,
				                          'to'         => $to_phone,
				                          'type'       => $type,
				                          'text'       => $message,
				                          'api_key'    => $this->_nexmo_api_key,
				                          'api_secret' => $this->_nexmo_api_secret,

			                          ) );


			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				            "Content-Length: " . strlen( $args ) . "\r\n"
			);

			$endpoint = 'https://rest.nexmo.com/sms/json';

			// perform HTTP request with endpoint / args
			$response = wp_safe_remote_request( esc_url_raw( $endpoint ), $wp_remote_http_args );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {

				throw new Exception( $response->get_error_message() );

			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['response'] ) || ! isset( $response['body'] ) ) {

				throw new Exception( __( 'No answer', 'yith-woocommerce-sms-notifications' ) );

			}

			$result = json_decode( $response['body'], true );

			if ( $result['messages'][0]['status'] != 0 ) {

				throw new Exception( sprintf( __( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $result['messages'][0]['error-text'] ) );

			}

			return;

		}

	}

}