<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWSN_MultiVendor' ) ) {

	/**
	 * Implements compatibility with YITH WooCommerce Multi Vendor
	 *
	 * @class   YWSN_MultiVendor
	 * @package Yithemes
	 * @since   1.0.3
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWSN_MultiVendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YWSN_MultiVendor
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWSN_MultiVendor
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self( $_REQUEST );

			}

			return self::$instance;
		}

		/**
		 * @var YITH_Vendor current vendor
		 */
		protected $vendor;

		/**
		 * @var YITH_Vendor active vendors
		 */
		protected $active_vendors;

		/**
		 * @var string Yith WooCommerce SMS Notifications vendor panel page
		 */
		protected $_panel_page = 'yith_vendor_sn_settings';

		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.0.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_vendor_panel = null;

		/**
		 * Constructor
		 *
		 * @since   1.0.3
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->vendor         = yith_get_vendor( 'current', 'user' );
			$this->active_vendors = YITH_Vendors()->get_vendors( array( 'enabled_selling' => true ) );

			if ( $this->check_ywsn_vendor_enabled() ) {

				if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {

					add_action( 'admin_menu', array( $this, 'add_ywsn_vendor' ), 5 );
					add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
					add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );

				}

				add_filter( 'ywsn_active_sms', array( $this, 'get_vendor_active_sms' ), 10, 2 );
				add_filter( 'ywsn_admin_phone_numbers', array( $this, 'get_vendor_admin_numbers' ), 10, 2 );

			}

		}

		/**
		 * Add SMS Notifications panel for vendors
		 *
		 * @since   1.0.3
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_ywsn_vendor() {

			if ( ! empty( $this->_vendor_panel ) ) {
				return;
			}

			$tabs = array(
				'vendor' => __( 'Settings', 'yith-woocommerce-sms-notifications' ),
			);

			$args = array(
				'create_menu_page' => false,
				'parent_slug'      => '',
				'page_title'       => _x( 'SMS Notifications', 'plugin name in admin page title', 'yith-woocommerce-sms-notifications' ),
				'menu_title'       => _x( 'SMS Notifications', 'plugin name in admin WP menu', 'yith-woocommerce-sms-notifications' ),
				'capability'       => 'manage_vendor_store',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YWSN_DIR . 'plugin-options/vendor',
				'icon_url'         => 'dashicons-admin-settings',
				'position'         => 99
			);

			$this->_vendor_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Add custom post type screen to WooCommerce list
		 *
		 * @since   1.0.3
		 *
		 * @param   $screen_ids
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = $this->_panel_page;

			return $screen_ids;

		}

		/**
		 * Initializes CSS and javascript
		 *
		 * @since   1.0.3
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			if ( ! empty( $_GET['page'] ) && ( $_GET['page'] == $this->_panel_page ) ) {

				wp_register_style( 'yit-plugin-style', YIT_CORE_PLUGIN_URL . '/assets/css/yit-plugin-panel.css' );
				wp_enqueue_style( 'yit-plugin-style' );

			}

		}

		/**
		 * Check if SMS Notifications for vendors allowed
		 *
		 * @since   1.0.3
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function check_ywsn_vendor_enabled() {

			return ( get_option( 'yith_wpv_vendors_enable_sms' ) == 'yes' );

		}

		/**
		 * Get active SMS list for vendor
		 *
		 * @since   1.0.3
		 *
		 * @param $value
		 * @param $order
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_active_sms( $value, $order ) {

			$vendor = yith_get_vendor( $order->post->post_author, 'user' );

			if ( $vendor->is_valid() ) {

				$value = get_option( 'ywsn_sms_active_send_vendor_' . $vendor->id );
			}

			return $value;

		}

		/**
		 * Check active SMS list for vendor
		 *
		 * @since   1.0.3
		 *
		 * @param $value
		 * @param $order
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_admin_numbers( $value, $order ) {

			$vendor = yith_get_vendor( $order->post->post_author, 'user' );

			if ( $vendor->is_valid() ) {

				$value = explode( ',', trim( get_option( 'ywsn_admin_phone_vendor_' . $vendor->id ) ) );
			}

			return $value;

		}

	}

	/**
	 * Unique access to instance of YWSN_MultiVendor class
	 *
	 * @return \YWSN_MultiVendor
	 */
	function YWSN_MultiVendor() {

		return YWSN_MultiVendor::get_instance();

	}

	YWSN_MultiVendor();

}