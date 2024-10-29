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

$vendor = yith_get_vendor( 'current', 'user' );

return array(
	'vendor' => array(

		'ywsn_send_section_title' => array(
			'name' => __( 'Sending settings', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
		),
		'ywsn_admin_phone'        => array(
			'name'        => __( 'Admin phone', 'yith-woocommerce-sms-notifications' ),
			'type'        => 'yith-wc-custom-checklist',
			'id'          => 'ywsn_admin_phone_vendor_' . $vendor->id,
			'css'         => 'width: 50%;',
			'desc'        => __( 'Enter here the phone numbers of the admins who will be notified via SMS. Include country calling codes. You can also
            specify more than one phone number. Type the number and press Enter to add a new one.', 'yith-woocommerce-sms-notifications' ),
			'placeholder' => __( 'Type a phone number&hellip;', 'yith-woocommerce-sms-notifications' ),
		),
		'ywsn_sms_active_send'    => array(
			'name'        => __( 'SMS notifications for the following order status changes', 'yith-woocommerce-sms-notifications' ),
			'type'        => 'yith-wc-check-matrix-table',
			'id'          => 'ywsn_sms_active_send_vendor_' . $vendor->id,
			'main_column' => array(
				'label' => __( 'Order status', 'yith-woocommerce-sms-notifications' ),
				'rows'  => wc_get_order_statuses(),
			),
			'columns'     => array(
				array(
					'id'    => 'admin',
					'label' => __( 'Receive SMS', 'yith-woocommerce-sms-notifications' ),
					'tip'   => __( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
				)
			)

		),
		'ywsn_send_section_end'   => array(
			'type' => 'sectionend',
		),

	)
);