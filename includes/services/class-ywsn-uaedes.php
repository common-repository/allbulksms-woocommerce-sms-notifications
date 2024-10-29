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

if ( ! class_exists( 'YWSN_Uaedes' ) ) {

	/**
	 * Implements UAEDes API for YWSN plugin
	 *
	 * @class   YWSN_Uaedes
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Uaedes extends YWSN_SMS_Gateway {

		/** @var string uaedes mobile */
		private $_uaedes_user;

		/** @var string uaedes password */
		private $_uaedes_pass;

		/** @var string uaedes sender */
		private $_uaedes_sender;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_uaedes_user   = get_option( 'ywsn_uaedes_user' );
			$this->_uaedes_pass   = get_option( 'ywsn_uaedes_pass' );
			$this->_uaedes_sender = get_option( 'ywsn_uaedes_sender' );

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

			$args = http_build_query( array(
				                          'user'        => $this->_uaedes_user,
				                          'pwd'         => $this->_uaedes_pass,
				                          'senderid'    => $this->_uaedes_sender,
				                          'mobileno'    => $to_phone,
				                          'msgtext'     => $message,
				                          'priority'    => 'High',
				                          'CountryCode' => 'ALL'
			                          ) );

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				            "Content-Length: " . strlen( $args ) . "\r\n"
			);

			$endpoint = 'http://getway.uaedes.ae/sendurlcomma.aspx';

			// perform HTTP request with endpoint / args
			$response = wp_safe_remote_request( esc_url_raw( $endpoint ), $wp_remote_http_args );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {

				throw new Exception( $response->get_error_message() );

			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['body'] ) ) {

				throw new Exception( __( 'No answer', 'yith-woocommerce-sms-notifications' ) );

			}

			if ( trim( $response['body'] ) !== 'Send Successful' ) {

				throw new Exception( sprintf( __( 'An error has occurred. Error code: %s', 'yith-woocommerce-sms-notifications' ), $response['body'] ) );

			}

			return;

		}

	}

}
