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

$howto   = array(
	'ywsn_howto_section_title' => array(
		'name' => __( 'Placeholder reference', 'yith-woocommerce-sms-notifications' ),
		'type' => 'title',
	),
);
$counter = 0;

foreach ( YITH_WSN()->placeholder_reference() as $key => $label ) {

	$howto[ 'ywsn_howto_' . $counter ] = array(
		'type'  => 'yith-wc-label',
		'title' => $key,
		'desc'  => $label,
	);

	$counter ++;

}

$howto['ywsn_howto_section_end'] = array(
	'type' => 'sectionend',
);


return array(
	'howto' => $howto
);