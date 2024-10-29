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

if ( ! class_exists( 'YWSN_Metabox' ) ) {

	/**
	 * Shows Meta Box in order's details page
	 *
	 * @class   YWSN_Metabox
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_Metabox {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSN_Metabox
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSN_Metabox
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self( $_REQUEST );

			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			if ( get_option( 'ywsn_enable_plugin' ) == 'yes' ) {

				add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
				add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save' ) );

			}

		}

		/**
		 * Add a metabox on product page
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_metabox() {

			foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
				add_meta_box( 'ywsn-metabox', _x( 'SMS notifications', 'metabox title', 'yith-woocommerce-sms-notifications' ), array( $this, 'output' ), $type, 'side', 'high' );
			}

		}

		/**
		 * Output Meta Box
		 *
		 * The function to be called to output the meta box in product details page.
		 *
		 * @since   1.0.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function output() {

			if ( ! isset( $_GET['post'] ) ) {
				return;
			}

			$order       = wc_get_order( $_GET['post'] );
			$option      = yit_get_prop( $order, '_ywsn_receive_sms' );
			$ext_charset = apply_filters( 'ywsn_additional_charsets', array() );
			$sms_length  = empty( $ext_charset ) ? 160 : 70;

			?>
			<div class="ywsn-sms-metabox">
				<?php if ( 'requested' == get_option( 'ywsn_customer_notification' ) ): ?>

					<fieldset>
						<legend class="screen-reader-text"><span><?php _e( 'Get order notifications via SMS.', 'yith-woocommerce-sms-notifications' ); ?></span></legend>
						<label for="_ywsn_receive_sms">
							<input name="_ywsn_receive_sms" id="_ywsn_receive_sms" type="checkbox" value="1" <?php checked( $option, 'yes' ); ?> /><?php _e( 'Get order notifications via SMS.', 'yith-woocommerce-sms-notifications' ); ?>
						</label>
					</fieldset>

				<?php endif; ?>

				<p><?php _e( 'Text customer:', 'yith-woocommerce-sms-notifications' ); ?></p>
				<p class="ywsn-write-sms-order">
					<textarea class="ywsn-custom-message"></textarea>
					<span class="ywsn-char-count"><?php _e( 'Remaining characters', 'yith-woocommerce-sms-notifications' ); ?>: <span><?php echo apply_filters( 'ywsn_sms_limit', $sms_length ) ?></span></span>
				</p>
				<p>
					<button type="button" class="button-secondary ywsn-send-sms"><?php _e( 'Send', 'yith-woocommerce-sms-notifications' ); ?></button>
				</p>
				<div class="ywsn-send-result send-progress"><?php _e( 'Sending...', 'yith-woocommerce-sms-notifications' ); ?></div>
			</div>
			<?php

		}

		/**
		 * Save Meta Box
		 *
		 * The function to be called to save the meta box options.
		 *
		 * @since   1.0.1
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function save() {

			global $post;

			$order       = wc_get_order( $post->ID );
			$receive_sms = isset( $_POST['_ywsn_receive_sms'] ) ? 'yes' : 'no';

			yit_save_prop( $order, '_ywsn_receive_sms', $receive_sms, true );

		}

	}

	/**
	 * Unique access to instance of YWSN_Metabox class
	 *
	 * @return \YWSN_Metabox
	 */
	function YWSN_Metabox() {

		return YWSN_Metabox::get_instance();

	}

	new YWSN_Metabox();

}