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

if ( ! class_exists( 'YWSN_Ajax' ) ) {

	/**
	 * Implements AJAX for YWSN plugin
	 *
	 * @class   YWSN_Ajax
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Ajax {

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'wp_ajax_ywsn_send_sms', array( $this, 'send_manual_sms' ) );

		}

		/**
		 * Send a manual SMS
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function send_manual_sms() {

			try {

				if ( 'none' == get_option( 'ywsn_sms_gateway' ) ) {

					throw new Exception( __( 'No SMS service provider configured', 'yith-woocommerce-sms-notifications' ) );

				}

				$order_note   = '';
				$order        = ( isset( $_POST['order_id'] ) ? wc_get_order( $_POST['order_id'] ) : null );
				$phone        = ( isset( $_POST['phone'] ) ? $_POST['phone'] : null );
				$country      = ( isset( $_POST['country'] ) ? $_POST['country'] : '' );
				$customer     = ( isset( $_POST['order_id'] ) ? true : false );
				$manual_sms   = new YWSN_Messages( $order, $customer, $phone, $country );
				$email_result = $manual_sms->single_sms( $_POST['message'] );

				if ( $order ) {

					ob_start();

					?>

					<li rel="<?php echo esc_attr( $manual_sms->order_note['id'] ) ?>" class="note">
						<div class="note_content"> <?php echo wpautop( wptexturize( $manual_sms->order_note['text'] ) ) ?></div>
						<p class="meta"><a href="#" class="delete_note"><?php _e( 'Delete note', 'woocommerce' ) ?></a></p>
					</li>

					<?php

					$order_note = ob_get_clean();

				}

				$result = ( $order ? $order_note : '' );

				if ( ! $email_result ) {

						wp_send_json( array( 'success' => true, 'note' => $result ) );


				} else {

					wp_send_json( array( 'success' => true, 'note' => $result ) );

				}

			} catch ( Exception $e ) {

				wp_send_json( array( 'success' => false, 'error' => $e->getMessage(), 'note' => '' ) );

			}


		}

	}

	new YWSN_Ajax();

}

