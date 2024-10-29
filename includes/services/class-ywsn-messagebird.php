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

if ( ! class_exists( 'YWSN_MessageBird' ) ) {

	/**
	 * Implements MessageBird API for YWSN plugin
	 *
	 * @class   YWSN_MessageBird
	 * @package Yithemes
	 * @since   1.0.4
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_MessageBird extends YWSN_SMS_Gateway {

		/** @var string MessageBird API Key */
		private $_messagebird_api_key;

		/**
		 * Constructor
		 *
		 * @since   1.0.4
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_messagebird_api_key = get_option( 'ywsn_messagebird_api_key' );

			parent::__construct();

		}

		/**
		 * Send SMS
		 *
		 * @since   1.0.4
		 *
		 * @param   $to_phone
		 * @param   $message
		 * @param   $country_code
		 *
		 * @return  void
		 * @throws  Exception for MessageBird Error code
		 * @author  Alberto Ruggiero
		 */
		public function send( $to_phone, $message, $country_code ) {

			require_once( YWSN_DIR . 'includes/services/messagebird/autoload.php' );

			$to_phone = ( '+' != substr( $to_phone, 0, 1 ) ? '+' . $to_phone : $to_phone );

			try {

				if ( '' != $this->_from_asid ) {

					$from = $this->_from_asid;

				} else {

					$from = ( '+' != substr( $this->_from_number, 0, 1 ) ? '+' . $this->_from_number : $this->_from_number );

				}

				$message_bird = new \MessageBird\Client( $this->_messagebird_api_key );

				$message_obj             = new \MessageBird\Objects\Message();
				$message_obj->originator = $from;
				$message_obj->recipients = array( $to_phone );
				$message_obj->body       = $message;
				$message_obj->datacoding = 'auto';

				$response = $message_bird->messages->create( $message_obj );

				$this->_log[] = $response;

				if ( $response->type ) {

					return;

				} else {

					throw new Exception( sprintf( __( 'Error: %s', 'yith-woocommerce-sms-notifications' ), $response[0] ) );

				}

			} catch ( Exception $e ) {

				throw new Exception( sprintf( __( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $e->getMessage() ) );

			}

		}

	}

}

