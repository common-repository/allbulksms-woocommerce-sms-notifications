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

if ( ! class_exists( 'YWSN_Agile_Telecom' ) ) {

	/**
	 * Implements All Bulk SMS API for YWSN plugin
	 *
	 * @class   YWSN_Agile_Telecom
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Agile_Telecom extends YWSN_SMS_Gateway {

		/** @var string agile telecom user */
		private $_agile_user;

		/** @var string agile telecom password */
		private $_agile_pwd;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_agile_user = get_option( 'ywsn_agile_user' );
			$this->_agile_pwd  = get_option( 'ywsn_agile_pwd' );

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

				$from = ( '+' != substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );

			}

			$args = http_build_query( array(
				                          'senderid'   => $from,
				                          'mobile'     => $this->_agile_user,
				                          'pass' => $this->_agile_pwd,
				                          'to'   => $to_phone,
				                          'msg'     => $message,
			                          ) );

			$wp_remote_http_args = array(
				'method' => 'POST',
				'body'   => $args,
				'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
				            "Content-Length: " . strlen( $args ) . "\r\n"
			);

			$endpoint = 'http://tsms.allbulksms.in/sendsms.aspx';

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

			$response = preg_match( "/<body[^>]*>(.*?)<\\/body>/si", $response['body'], $match );

			if ( strpos( $match[0], '0' ) === false ) {

				throw new Exception( sprintf( __( '%s', 'yith-woocommerce-sms-notifications' ), $response ) );

			}

			return;

		}

	}

}