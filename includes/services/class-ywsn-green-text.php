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

if ( ! class_exists( 'YWSN_Green_Text' ) ) {

	/**
	 * Implements Green Text API for YWSN plugin
	 *
	 * @class   YWSN_Green_Text
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Green_Text extends YWSN_SMS_Gateway {

		/** @var string green_text mobile */
		private $_green_text_user;

		/** @var string green_text password */
		private $_green_text_pass;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_green_text_user = get_option( 'ywsn_green_text_user' );
			$this->_green_text_pass = get_option( 'ywsn_green_text_pass' );

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

			$to_phone       = ( '+' != substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );
			$charactersetid = empty( apply_filters( 'ywsn_additional_charsets', array() ) ) ? 2 : 1;


			if ( '' != $this->_from_asid ) {

				$from         = $this->_from_asid;
				$reply_method = 1;

			} else {

				$from         = $this->_from_number;
				$reply_method = 4;

			}

			$args = array(
				'method'                 => 'sendsms',
				'returncsvstring'        => 'true',
				'externallogin'          => $this->_green_text_user,
				'password'               => $this->_green_text_pass,
				'clientbillingreference' => '0',
				'clientmessagereference' => '0',
				'destinations'           => urlencode( $to_phone ),
				'originator'             => $from,
				'body'                   => urlencode( $message ),
				'charactersetid'         => $charactersetid,
				'replymethodid'          => $reply_method,
				'replydata'              => '',
				'statusnotificationurl'  => '',
				'validity'               => '72'
			);

			$endpoint = 'http://www.textapp.net/webservice/httpservice.aspx';

			// perform HTTP request with endpoint / args
			$url = add_query_arg( $args, $endpoint );

			// perform HTTP request with endpoint / args
			$response = wp_remote_get( $url );

			// WP HTTP API error like network timeout, etc
			if ( is_wp_error( $response ) ) {

				throw new Exception( $response->get_error_message() );

			}

			$this->_log[] = $response;

			// Check for proper response / body
			if ( ! isset( $response['body'] ) ) {

				throw new Exception( __( 'No answer', 'yith-woocommerce-sms-notifications' ) );

			}

			$code = substr( $response['body'], 1, strpos( $response['body'], '#', 1 ) - 1 );

			if ( $code != 1 ) {

				throw new Exception( sprintf( __( 'An error has occurred. Error code: %s', 'yith-woocommerce-sms-notifications' ), $response['body'] ) );

			}

			return;

		}

	}

}