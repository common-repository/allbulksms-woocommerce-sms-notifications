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

if ( ! class_exists( 'YWSN_Jazz' ) ) {

	/**
	 * Implements Jazz API for YWSN plugin
	 *
	 * @class   YWSN_Jazz
	 * @package Yithemes
	 * @since   1.0.8
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Jazz extends YWSN_SMS_Gateway {

		/** @var string jazz Username */
		private $_jazz_username;

		/** @var string jazz password */
		private $_jazz_password;

		/** @var string jazz password */
		private $_jazz_mask;

		/**
		 * Constructor
		 *
		 * @since   1.0.8
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_jazz_username = get_option( 'ywsn_jazz_username' );
			$this->_jazz_password = get_option( 'ywsn_jazz_password' );
			$this->_jazz_mask     = get_option( 'ywsn_jazz_mask' );

			parent::__construct();

		}

		/**
		 * Send SMS
		 *
		 * @since   1.0.8
		 *
		 * @param   $to_phone
		 * @param   $message
		 * @param   $country_code
		 *
		 * @return  void
		 * @throws  Exception for Jazz Error code
		 * @author  Alberto Ruggiero
		 */
		public function send( $to_phone, $message, $country_code ) {

			$to_phone = ( '00' != substr( $to_phone, 0, 1 ) ? '00' . $to_phone : $to_phone );
			$args     = http_build_query( array(
				                              'Username' => $this->_jazz_username,
				                              'Password' => $this->_jazz_password,
				                              'From'     => $this->_jazz_mask,
				                              'To'       => $to_phone,
				                              'Message'  => $message,
			                              ) );

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				            "Content-Length: " . strlen( $args ) . "\r\n"
			);

			$endpoint = 'http://221.132.117.58:7700/sendsms_url.html';

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

			if ( strpos( $response['body'], 'Message Sent Successfully!' ) === false ) {

				throw new Exception( sprintf( __( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response['body'] ) );

			}

			return;

		}

	}

}

