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

if ( ! class_exists( 'YIT_Plugin_SubPanel' ) ) {
    /**
     * YIT Plugin Panel
     *
     * Setting Page to Manage Plugins
     *
     * @class YIT_Plugin_Panel
     * @package    Yithemes
     * @since      1.0
     * @author     Your Inspiration Themes
     */

    class YIT_Plugin_SubPanel extends YIT_Plugin_Panel {

        /**
         * @var string version of class
         */
        public $version = '1.0.0';

        /**
         * @var array a setting list of parameters
         */
        public $settings = array();


        /**
         * @var array
         */
        private $_main_array_options = array();

        /**
         * Constructor
         *
         * @since  1.0
         * @author Emanuela Castorina <emanuela.castorina@yithemes.it>
         */

        public function __construct( $args = array() ) {
            if ( ! empty( $args ) ) {
                $this->settings = $args;
                $this->settings['parent'] = $this->settings['page'];
                $this->_tabs_path_files = $this->get_tabs_path_files();

                add_action( 'admin_init', array( $this, 'register_settings' ) );
                add_action( 'admin_menu', array( &$this, 'add_setting_page' ) );
                add_action( 'admin_bar_menu', array( &$this, 'add_admin_bar_menu' ), 100 );
                add_action( 'admin_init', array( &$this, 'add_fields' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
            }
        }


        /**
         * Register Settings
         *
         * Generate wp-admin settings pages by registering your settings and using a few callbacks to control the output
         *
         * @return void
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.it>
         */
        public function register_settings() {
            register_setting( 'yit_' . $this->settings['page'] . '_options', 'yit_' . $this->settings['page'] . '_options', array( &$this, 'options_validate' ) );
        }



        /**
         * Add Setting SubPage
         *
         * add Setting SubPage to wordpress administrator
         *
         * @return array validate input fields
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.it>
         */
        public function add_setting_page() {
            global $admin_page_hooks;
            $logo = YIT_CORE_PLUGIN_URL . '/assets/images/yithemes-icon.png';

            $admin_logo = function_exists( 'yit_get_option' ) ? yit_get_option( 'admin-logo-menu' ) : '';

            if ( isset( $admin_logo ) && !empty( $admin_logo ) && $admin_logo != '' && $admin_logo ) {
                $logo = $admin_logo;
            }

            if( ! isset( $admin_page_hooks['yit_plugin_panel'] ) ) {
                $position = apply_filters( 'yit_plugins_menu_item_position', '62.32' );
                add_menu_page( 'yit_plugin_panel', 'ABS', 'nosuchcapability', 'yit_plugin_panel', NULL, $logo, $position );
            }

            add_submenu_page( 'yit_plugin_panel', $this->settings['label'], $this->settings['label'], 'manage_options', $this->settings['page'], array( $this, 'yit_panel' ) );
            remove_submenu_page( 'yit_plugin_panel', 'yit_plugin_panel' );

        }

        /**
         * Show a tabbed panel to setting page
         *
         * a callback function called by add_setting_page => add_submenu_page
         *
         * @return void
         * @since    1.0
         * @author   Emanuela Castorina <emanuela.castorina@yithemes.it>
         */
        public function yit_panel() {

            $tabs        = '';
            $current_tab = $this->get_current_tab();

            // tabs
            foreach ( $this->settings['admin-tabs'] as $tab => $tab_value ) {
                $active_class = ( $current_tab == $tab ) ? ' nav-tab-active' : '';
                $tabs .= '<a class="nav-tab' . $active_class . '" href="?page=' . $this->settings['page'] . '&tab=' . $tab . '">' . $tab_value . '</a>';
            }
            ?>
            <div id="icon-themes" class="icon32"><br /></div>
            <h2 class="nav-tab-wrapper">
                <?php echo $tabs ?>
            </h2>

            <div id="wrap" class="plugin-option">
                <?php $this->message(); ?>
                <h2><?php echo $this->get_tab_title() ?></h2>

                <?php if ( $this->is_show_form() ) : ?>
                    <form method="post" action="options.php">
                        <?php do_settings_sections( 'yit' ); ?>
                        <p>&nbsp;</p>
                        <?php settings_fields( 'yit_' . $this->settings['page'] . '_options' ); ?>
                        <input type="hidden" name="<?php echo $this->get_name_field( 'current_tab' ) ?>" value="<?php echo esc_attr( $current_tab ) ?>" />
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'yith-plugin-fw' ) ?>" style="float:left;margin-right:10px;" />
                    </form>
                    <form method="post">
                        <?php $warning = __( 'If you continue with this action, you will reset all the options in this page.', 'yith-plugin-fw' ) ?>
                        <input type="hidden" name="yit-action" value="reset" />
                        <input type="submit" name="yit-reset" class="button-secondary" value="<?php _e( 'Reset to default', 'yith-plugin-fw' ) ?>" onclick="return confirm('<?php echo $warning . '\n' . __( 'Are you sure?', 'yith-plugin-fw' ) ?>');" />
                    </form>
                    <p>&nbsp;</p>
                <?php endif ?>
            </div>
        <?php
        }



    }

}

