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

if ( ! class_exists( 'YWSN_SMS_Gateway' ) ) {

	/**
	 * SMS Gateway abstract class
	 *
	 * @class   YWSN_SMS_Gateway
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	abstract class YWSN_SMS_Gateway {

		/**
		 * @var string the number SMS messages will be sent from
		 */
		protected $_from_number;

		/**
		 * @var string using Alphanumeric Sender ID
		 */
		protected $_from_asid;

		/**
		 * @var array the response of the SMS service
		 */
		protected $_log;

		/**
		 * @var array the response of the SMS service
		 */
		protected $_logger;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->_from_asid   = substr( get_option( 'ywsn_from_asid' ), 0, 11 );
			$this->_from_number = preg_replace( '[\D]', '', get_option( 'ywsn_from_number' ) );
			$this->_logger      = new WC_Logger();

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
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function send( $to_phone, $message, $country_code ) {

			die( 'function YWSN_SMS_Gateway->send() must be over-ridden in a sub-class.' );

		}

		/**
		 * Print send log
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function print_log() {

			error_log( print_r( $this->_log, true ) );
			update_option( 'ywsn_debug_log', print_r( $this->_log, true ) );

		}

		/**
		 * Write send log
		 *
		 * @since   1.0.0
		 *
		 * @param   $args
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function write_log( $args ) {

			$log = strtoupper( ( $args['type'] != 'test' ? 'Order #' . $args['order'] . ' - ' : '' ) . $args['type'] . ' MESSAGE' ) . "\r\n";
			$log .= 'Status: ' . ( $args['success'] ? '' : '' . $args['status_message'] ) . "\r\n";
			$log .= 'Phone: ' . $args['phone'] . "\r\n";
			$log .= 'Message: ' . $args['message'] . "\r\n";

			$this->_logger->add( 'ywsn-' . current_time( 'Y-m' ), $log );

		}

	}

}