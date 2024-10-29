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

if ( ! class_exists( 'YWSN_ClickSend' ) ) {

	/**
	 * Implements ClickSend API for YWSN plugin
	 *
	 * @class   YWSN_ClickSend
	 * @package Yithemes
	 * @since   1.0.5
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_ClickSend extends YWSN_SMS_Gateway {

		/** @var string clicksend Username */
		private $_clicksend_username;

		/** @var string clicksend API Key */
		private $_clicksend_api_key;

		/**
		 * Constructor
		 *
		 * @since   1.0.5
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_clicksend_username = get_option( 'ywsn_clicksend_username' );
			$this->_clicksend_api_key  = get_option( 'ywsn_clicksend_api_key' );

			parent::__construct();

		}

		/**
		 * Send SMS
		 *
		 * @since   1.0.5
		 *
		 * @param   $to_phone
		 * @param   $message
		 * @param   $country_code
		 *
		 * @return  void
		 * @throws  Exception for ClickSend Error code
		 * @author  Alberto Ruggiero
		 */
		public function send( $to_phone, $message, $country_code ) {

			$to_phone = ( '+' != substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );

			if ( '' != $this->_from_asid ) {

				$from = $this->_from_asid;

			} else {

				$from = ( '+' != substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );

			}

			$args = http_build_query( array(
				                          'username' => $this->_clicksend_username,
				                          'key'      => $this->_clicksend_api_key,
				                          'senderid' => $from,
				                          'to'       => $to_phone,
				                          'message'  => $message,
			                          ) );


			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				            "Content-Length: " . strlen( $args ) . "\r\n"
			);

			$endpoint = 'https://api-mapper.clicksend.com/http/v2/send.php';

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

			$response_body = simplexml_load_string( $response['body'] );

			if ( $response_body->messages->message->result != '0000' ) {

				throw new Exception( sprintf( __( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response_body->messages->message->errortext ) );

			}

			return;

		}

	}

}

