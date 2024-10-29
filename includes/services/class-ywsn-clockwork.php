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

if ( ! class_exists( 'YWSN_Clockwork' ) ) {

	/**
	 * Implements Clockwork API for YWSN plugin
	 *
	 * @class   YWSN_Clockwork
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Clockwork extends YWSN_SMS_Gateway {

		/** @var string clockwork API Key */
		private $_clockwork_api_key;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_clockwork_api_key = get_option( 'ywsn_clockwork_api_key' );

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
		 * @return  void
		 * @throws  Exception for Clockwork Error code
		 * @author  Alberto Ruggiero
		 */
		public function send( $to_phone, $message, $country_code ) {

			require_once( YWSN_DIR . 'includes/services/clockwork/class-Clockwork.php' );

			try {

				if ( '' != $this->_from_asid ) {

					$from = $this->_from_asid;

				} else {

					$from = $this->_from_number;

				}

				$clockwork    = new Clockwork( $this->_clockwork_api_key );
				$message      = array(
					'from'    => $from,
					'to'      => $to_phone,
					'message' => $message
				);
				$response     = $clockwork->send( $message );
				$this->_log[] = $response;

				if ( $response['success'] ) {

					return;

				} else {

					throw new Exception( sprintf( _x( 'Error %1$s: %2$s', '[ %1$s: error code - %2$s: error message; Ex. Error 10: Invalid Parameter ]', 'yith-woocommerce-sms-notifications' ), $response['error_code'], $response['error_message'] ) );

				}

			} catch ( ClockworkException $e ) {

				throw new Exception( sprintf( __( 'An error has occurred: %s', 'yith-woocommerce-sms-notifications' ), $e->getMessage() ) );

			}

		}

	}

}

