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
	exit;
} // Exit if accessed directly

/**
 * Outputs a custom template for send test sms in plugin options panel
 *
 * @class   YWSN_SMS_Send
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YWSN_SMS_Send {

	/**
	 * Single instance of the class
	 *
	 * @var \YWSN_SMS_Send
	 * @since 1.0.0
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YWSN_SMS_Send
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

		add_action( 'woocommerce_admin_field_ywsn-sms-send', array( $this, 'output' ) );

	}

	/**
	 * Outputs a custom template for send test sms in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output( $option ) {

		$ext_charset = apply_filters( 'ywsn_additional_charsets', array() );
		$sms_length  = empty( $ext_charset ) ? 160 : 70;

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="ywsn_sms_test"><?php _e( 'Test message', 'yith-woocommerce-sms-notifications' ) ?></label>
			</th>
			<td class="forminp forminp-custom-send">

				<select name="ywsn_sms_test_message" id="ywsn_sms_test_message" class="">
					<option value=""><?php _e( 'Choose message', 'yith-woocommerce-sms-notifications' ) ?></option>
					<option value="write-sms"><?php _e( 'Type message', 'yith-woocommerce-sms-notifications' ) ?></option>
					<option value="admin"><?php _e( 'Admin message', 'yith-woocommerce-sms-notifications' ) ?></option>
					<option value="generic"><?php _e( 'Default message', 'yith-woocommerce-sms-notifications' ) ?></option>
					<?php foreach ( wc_get_order_statuses() as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo $label ?></option>
					<?php endforeach; ?>
				</select>

				<select name="ywsn_sms_test_message_country" id="ywsn_sms_test_message_country" class="">

					<option value="IN" selected="selected">India</option>

				</select>

				<input name="ywsn_sms_test" id="ywsn_sms_test" type="text" class="ywsn-test-sms" placeholder="<?php _e( 'Type a phone number to send a test SMS text message', 'yith-woocommerce-sms-notifications' ) ?>" />

				<button type="button" class="button-secondary ywsn-send-test-sms"><?php _e( 'Send Test SMS', 'yith-woocommerce-sms-notifications' ); ?></button>
				<div class="ywsn-write-sms">
					<textarea class="ywsn-custom-message"></textarea>
					<div class="ywsn-char-count"><?php _e( 'Remaining characters', 'yith-woocommerce-sms-notifications' ); ?>: <span><?php echo apply_filters( 'ywsn_sms_limit', $sms_length ) ?></span></div>
				</div>
				<div class="ywsn-send-result send-progress"><?php _e( 'Sending...', 'yith-woocommerce-sms-notifications' ); ?></div>
			</td>
		</tr>
		<?php
	}

}

/**
 * Unique access to instance of YWSN_SMS_Send class
 *
 * @return \YWSN_SMS_Send
 */
function YWSN_SMS_Send() {

	return YWSN_SMS_Send::get_instance();

}

new YWSN_SMS_Send();