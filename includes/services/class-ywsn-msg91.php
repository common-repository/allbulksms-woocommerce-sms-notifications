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

if ( ! class_exists( 'YWSN_Msg91' ) ) {

	/**
	 * Implements Mg91 API for YWSN plugin
	 *
	 * @class   YWSN_Msg91
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Msg91 extends YWSN_SMS_Gateway {

		/** @var string msg91 user */
		private $_msg91_key;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_msg91_key = get_option( 'ywsn_msg91_key' );

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

			$to_phone = ( '+' != substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );

			if ( '' != $this->_from_asid ) {

				$from = $this->_from_asid;

			} else {

				$from = $this->_from_number;

			}

			$args = http_build_query( array(
				                          'authkey' => $this->_msg91_key,
				                          'sender'  => $from,
				                          'mobiles' => $to_phone,
				                          'message' => $message,
				                          'route'   => '4',
				                          'country' => '0',
			                          ) );


			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				            "Content-Length: " . strlen( $args ) . "\r\n"
			);

			$endpoint = 'https://control.msg91.com/api/sendhttp.php';

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

			if ( strlen( $response['body'] ) < 24 ) {

				throw new Exception( sprintf( __( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response['body'] ) );

			}

			return;

		}

	}

}