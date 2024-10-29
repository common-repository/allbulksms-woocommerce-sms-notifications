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
 * Outputs a custom table template in plugin options panel
 *
 * @class   YITH_WC_Check_Matrix_Table
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */
class YITH_WC_Check_Matrix_Table {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WC_Check_Matrix_Table
	 * @since 1.0.0
	 */
	protected static $instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WC_Check_Matrix_Table
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

		add_action( 'woocommerce_admin_field_yith-wc-check-matrix-table', array( $this, 'output' ) );

	}

	/**
	 * Outputs a custom table template in plugin options panel
	 *
	 * @since   1.0.0
	 *
	 * @param   $option
	 *
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	public function output( $option ) {

		$option_value = get_option( $option['id'] );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $option['id'] ); ?>"><?php echo esc_html( $option['title'] ); ?></label>
			</th>
			<td class="forminp" id="<?php echo $option['id'] ?>">

				<table class="widefat wp-list-table yith-check-matrix-table" cellspacing="0">
					<thead>
					<tr>
						<th><?php echo $option['main_column']['label']; ?></th>
						<?php foreach ( $option['columns'] as $column ) : ?>
							<th class="checkbox-column">

								<a id="<?php echo $column['id'] ?>-lnk" href="#" class="tips" data-tip="<?php echo $column['tip'] ?>">
									<?php echo $column['label'] ?>
								</a>

							</th>
						<?php endforeach; ?>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $option['main_column']['rows'] as $key => $label ) : ?>
						<tr>
							<td class="main-column">
								<?php echo $label ?>
							</td>
							<?php foreach ( $option['columns'] as $column ) : ?>
								<td class="checkbox-column">
									<input
										name="<?php echo $option['id'] ?>[<?php echo $key; ?>][<?php echo $column['id'] ?>]"
										id="<?php echo $option['id'] ?>[<?php echo $key; ?>][<?php echo $column['id'] ?>]"
										type="checkbox"
										class="<?php echo $column['id'] ?>-cb"
										value="1"
										<?php checked( isset( $option_value[ $key ][ $column['id'] ] ) ? $option_value[ $key ][ $column['id'] ] : '0', '1' ); ?>
									/>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>

				</table>

			</td>
		</tr>
		<?php
	}

}

/**
 * Unique access to instance of YITH_WC_Check_Matrix_Table class
 *
 * @return \YITH_WC_Check_Matrix_Table
 */
function YITH_WC_Check_Matrix_Table() {

	return YITH_WC_Check_Matrix_Table::get_instance();

}

new YITH_WC_Check_Matrix_Table();