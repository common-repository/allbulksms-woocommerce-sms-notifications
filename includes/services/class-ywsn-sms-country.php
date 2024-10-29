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

if ( ! class_exists( 'YWSN_SMS_Country' ) ) {

	/**
	 * Implements SMS_Country API for YWSN plugin
	 *
	 * @class   YWSN_SMS_Country
	 * @package Yithemes
	 * @since   1.0.5
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_SMS_Country extends YWSN_SMS_Gateway {

		/** @var string sms_country Username */
		private $_sms_country_user;

		/** @var string sms_country Password */
		private $_sms_country_pwd;

		/**
		 * Constructor
		 *
		 * @since   1.0.5
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_sms_country_user = get_option( 'ywsn_sms_country_user' );
			$this->_sms_country_pwd  = get_option( 'ywsn_sms_country_pwd' );

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
		 * @throws  Exception for SMS_Country Error code
		 * @author  Alberto Ruggiero
		 */
		public function send( $to_phone, $message, $country_code ) {

			//$to_phone = ( '+' != substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );

			if ( '' != $this->_from_asid ) {

				$from = $this->_from_asid;

			} else {

				$from = ( '+' != substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );

			}

			$args = http_build_query( array(
				                          'User'         => $this->_sms_country_user,
				                          'Passwd'       => $this->_sms_country_pwd,
				                          'Sid'          => $from,
				                          'mobilenumber' => $to_phone,
				                          'message'      => $message,
				                          'mtype'        => 'N',
				                          'DR'           => 'Y'
			                          ) );

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				            "Content-Length: " . strlen( $args ) . "\r\n"
			);

			$endpoint = 'http://api.smscountry.com/SMSCwebservice_bulk.aspx';

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

			if ( strpos( $response['body'], 'OK' ) === false ) {

				throw new Exception( sprintf( __( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $response['body'] ) );

			}

			return;

		}

	}

}

