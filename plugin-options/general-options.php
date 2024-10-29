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

$url_shorteners = array(
	'none'   => __( 'None', 'yith-woocommerce-sms-notifications' ),
	'google' => __( 'Google', 'yith-woocommerce-sms-notifications' ),
	'bitly'  => __( 'bitly', 'yith-woocommerce-sms-notifications' ),
);

$query_args = array(
	'page' => isset( $_GET['page'] ) ? $_GET['page'] : '',
	'tab'  => 'howto',
);

$howto_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );

$debug_title = $debug_field = $debug_end = $log_file_link = '';

$logs     = WC_Admin_Status::scan_log_files();
$log_file = '';

//Check if exists a log file for current month
foreach ( $logs as $key => $value ) {
	if ( strpos( $value, 'ywsn-' . current_time( 'Y-m' ) ) !== false ) {
		$log_file = $key;
	}
}

if ( $log_file == '' ) {

	//If not found check if exists a log file for previous month
	foreach ( $logs as $key => $value ) {
		if ( strpos( $value, 'ywsn-' . date( 'Y-m', strtotime( '-1 months' ) ) ) !== false ) {
			$log_file = $key; // print key containing searched string
		}
	}

}

if ( $log_file != '' ) {

	$log_file_link = array(
		'type'  => 'yith-wc-label',
		'title' => __( 'Log File', 'yith-woocommerce-sms-notifications' ),
		'desc'  => sprintf( '<a href="%s" target="_blank">%s - %s</a>', admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . $log_file ), __( 'View Log File', 'yith-woocommerce-sms-notifications' ), $log_file ),
	);

}

if ( apply_filters( 'ywsn_save_send_log', false ) ) {

	$debug_title = array(
		'name' => __( 'SMS Debug', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
	);

	$debug_field = array(
		'name'              => __( 'Debug Log', 'yith-woocommerce-sms-notifications' ),
		'type'              => 'yith-wc-textarea',
		'id'                => 'ywsn_debug_log',
		'css'               => 'resize: vertical; width: 100%; min-height: 200px;',
		'default'           => '',
		'custom_attributes' => array(
			'readonly' => 'readonly'
		)
	);

	$debug_end = array(
		'type' => 'sectionend',
	);

}

return array(

	'general' => array(

		'ywsn_main_section_title' => array(
			'name' => __( 'SMS Notifications settings', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
		),
		'ywsn_enable_plugin'      => array(
			'name'    => __( 'Enable YITH WooCommerce SMS Notifications', 'yith-woocommerce-sms-notifications' ),
			'type'    => 'checkbox',
			'id'      => 'ywsn_enable_plugin',
			'default' => 'no',
		),
		'ywsn_log_file'           => $log_file_link,
		'ywsn_main_section_end'   => array(
			'type' => 'sectionend',
		),

		'ywsn_sms_service_title' => array(
			'name' => __( 'SMS Service settings', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
		),
		'ywsn_sms_gateway'       => array(
			'name'    => __( 'SMS service enabled', 'yith-woocommerce-sms-notifications' ),
			'type'    => 'select',
			'id'      => 'ywsn_sms_gateway',
			'options' => array(
		 	'none'                 => __( 'None', 'yith-woocommerce-sms-notifications' ),
				'YWSN_Agile_Telecom'   => __( 'All Bulk SMS', 'yith-woocommerce-sms-notifications' ),
			 
			),
			'default' => 'none'
		),
		'ywsn_sms_service_end'   => array(
			'type' => 'sectionend',
		),

	  
	 
	   

		'ywsn_agile_title' => array(
			'name' => __( 'All Bulk SMS settings', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
		),
		'ywsn_agile_desc'  => array(
			'label_id' => 'ywsn_agile_telecom_desc',
			'type'     => 'yith-wc-label',
			'desc'     => sprintf( __( 'Create your All Bulk SMS account on %s', 'yith-woocommerce-sms-notifications' ), '<a href="http://tsms.allbulksms.in/login.aspx">http://tsms.allbulksms.in</a>' ),
		),
		'ywsn_agile_user'  => array(
			'name'              => __( 'All Bulk SMS Username', 'yith-woocommerce-sms-notifications' ),
			'type'              => 'text',
			'id'                => 'ywsn_agile_user',
			'css'               => 'width: 50%',
			'custom_attributes' => array(
				'required' => 'required'
			)
		),
		'ywsn_agile_pwd'   => array(
			'name'              => __( 'All Bulk SMS Password', 'yith-woocommerce-sms-notifications' ),
			'type'              => 'text',
			'id'                => 'ywsn_agile_pwd',
			'css'               => 'width: 50%',
			'custom_attributes' => array(
				'required' => 'required'
			)
		),
		'ywsn_agile_end'   => array(
			'type' => 'sectionend',
		),
  
	 
		 
 

		'ywsn_send_section_title'      => array(
			'name' => __( 'Sending settings', 'yith-woocommerce-sms-notifications' ),
			'type' => 'title',
		),
		'ywsn_from_number'             => array(
			'name'              => __( 'Sender telephone number', 'yith-woocommerce-sms-notifications' ),
			'type'              => 'text',
			'id'                => 'ywsn_from_number',
			'desc'              => __( 'Enter the telephone number that should appear as sender', 'yith-woocommerce-sms-notifications' ),
			'custom_attributes' => array(
				'required'  => 'required',
				'maxlength' => 16
			)
		),
		'ywsn_from_asid'               => array(
			'name'              => __( 'Alphanumeric Sender ID', 'yith-woocommerce-sms-notifications' ),
			'type'              => 'text',
			'id'                => 'ywsn_from_asid',
			'desc'              => __( 'Alphanumeric sender identifier: enter the text that should appear as sender (this option might not work correctly in some countries,
            check your country with your SMS service provider you have selected)', 'yith-woocommerce-sms-notifications' ),
			'custom_attributes' => array(
				'maxlength' => 11
			)
		),
		'ywsn_admin_phone'             => array(
			'name'        => __( 'Admin phone', 'yith-woocommerce-sms-notifications' ),
			'type'        => 'yith-wc-custom-checklist',
			'id'          => 'ywsn_admin_phone',
			'css'         => 'width: 50%;',
			'desc'        => __( 'Enter here the phone numbers of the admins who will be notified via SMS. Include country calling codes. You can also
            specify more than one phone number. Type the number and press Enter to add a new one.', 'yith-woocommerce-sms-notifications' ),
			'placeholder' => __( 'Type a phone number&hellip;', 'yith-woocommerce-sms-notifications' ),
		),
		'ywsn_customer_notification'   => array(
			'name'    => __( 'Send SMS notifications to customers', 'yith-woocommerce-sms-notifications' ),
			'type'    => 'radio',
			'id'      => 'ywsn_customer_notification',
			'options' => array(
				'automatic' => __( 'All customers', 'yith-woocommerce-sms-notifications' ),
				'requested' => __( 'Only customers who ask for it in checkout', 'yith-woocommerce-sms-notifications' ),
			),
			'class'   => 'ywsn-checkout-option',
			'default' => 'automatic'
		),
		'ywsn_checkout_checkbox_value' => array(
			'name'    => '',
			'type'    => 'checkbox',
			'id'      => 'ywsn_checkout_checkbox_value',
			'default' => 'no',
			'desc'    => __( 'Show checkbox selected by default', 'yith-woocommerce-sms-notifications' ),
		),
		'ywsn_checkout_checkbox_text'  => array(
			'name'    => __( 'Checkbox text', 'yith-woocommerce-sms-notifications' ),
			'type'    => 'yith-wc-textarea',
			'id'      => 'ywsn_checkout_checkbox_text',
			'default' => __( 'I want to be notified about any changes in the order via SMS', 'yith-woocommerce-sms-notifications' ),
			'css'     => 'width: 50%'
		),
		'ywsn_sms_active_send'         => array(
			'name'        => __( 'SMS notifications for the following order status changes', 'yith-woocommerce-sms-notifications' ),
			'type'        => 'yith-wc-check-matrix-table',
			'id'          => 'ywsn_sms_active_send',
			'main_column' => array(
				'label' => __( 'Order status', 'yith-woocommerce-sms-notifications' ),
				'rows'  => wc_get_order_statuses(),
			),
			'columns'     => array(
				array(
					'id'    => 'customer',
					'label' => __( 'Customer', 'yith-woocommerce-sms-notifications' ),
					'tip'   => __( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
				),
				array(
					'id'    => 'admin',
					'label' => __( 'Admin', 'yith-woocommerce-sms-notifications' ),
					'tip'   => __( 'Select/deselect all elements', 'yith-woocommerce-sms-notifications' ),
				)
			)

		),
		'ywsn_send_section_end'        => array(
			'type' => 'sectionend',
		),
 
	 
	 
		'ywsn_url_shortening_end'   => array(
			'type' => 'sectionend',
		),

		'ywsn_debug_title' => $debug_title,
		'ywsn_debug_log'   => $debug_field,
		'ywsn_debug_end'   => $debug_end,

	)

);