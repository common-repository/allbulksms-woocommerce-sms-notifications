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

$query_args = array(
	'page' => isset( $_GET['page'] ) ? $_GET['page'] : '',
	'tab'  => 'howto',
);

$howto_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );

ob_start();

$placeholders = '';

foreach ( YITH_WSN()->placeholder_reference() as $key => $label ) {

	$placeholders .= '<b>' . $key . '</b>, ';

}
$placeholders = substr( $placeholders, 0, - 2 )

?>

<?php _e( 'In this page you can configure all SMS text messages that customers will receive when any changes to the status of their order is applied.', 'yith-woocommerce-sms-notifications' ) ?><br />
<?php _e( 'Allowed placeholders', 'yith-woocommerce-sms-notifications' ) ?>: <?php echo $placeholders ?> - <a href="%<?php echo $howto_url; ?>" target="_blank"><?php _e( 'More info', 'yith-woocommerce-sms-notifications' ) ?></a><br />
<?php _e( 'When you add placeholders, keep in mind that there\'s a 160 charters limit for each message.', 'yith-woocommerce-sms-notifications' ) ?>

<?php

$description = ob_get_clean();

$messages = array(
	'ywsn_messages_section_title' => array(
		'name' => __( 'SMS text message settings', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
	),
	'ywsn_messages_desc'          => array(
		'type' => 'yith-wc-label',
		'desc' => $description,
	),
	'ywsn_message_test'           => array(
		'type' => 'ywsn-sms-send',
	),
	'ywsn_message_admin'          => array(
		'name'              => __( 'Text message for admin(s)', 'yith-woocommerce-sms-notifications' ),
		'desc'              => __( 'This is the text message that admin(s) will receive any time the order status is changed', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-wc-textarea',
		'id'                => 'ywsn_message_admin',
		'default'           => __( '{site_title}: Order #{order_id} switched to {order_status}.', 'yith-woocommerce-sms-notifications' ),
		'custom_attributes' => array(
			'required' => 'required'
		)
	),
	'ywsn_message_generic'        => array(
		'name'              => __( 'Default customer SMS', 'yith-woocommerce-sms-notifications' ),
		'desc'              => __( 'This is the default message that customers receive each time the status of the order changes and if no other message is specified', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-wc-textarea',
		'id'                => 'ywsn_message_generic',
		'default'           => __( 'Your order #{order_id} on {site_title} is now {order_status}.', 'yith-woocommerce-sms-notifications' ),
		'custom_attributes' => array(
			'required' => 'required'
		)
	)
);

foreach ( wc_get_order_statuses() as $key => $label ) {

	$messages[ 'ywsn_message_' . $key ] = array(
		'name'    => $label,
		'type'    => 'yith-wc-textarea',
		'id'      => 'ywsn_message_' . $key,
		'default' => sprintf( __( 'Your order #{order_id} on {site_title} is now %s.', 'yith-woocommerce-sms-notifications' ), $label ),
	);

}

$messages['ywsn_messages_section_end'] = array(
	'type' => 'sectionend',
);


return array(

	'messages' => $messages

);