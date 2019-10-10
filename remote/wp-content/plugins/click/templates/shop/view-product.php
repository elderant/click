<?php
/**
 * Loop View Product
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo sprintf( '<a href="%s" class="%s" %s>%s</a>',
		esc_url( $click_args['url'] ),
		esc_attr( isset( $click_args['class'] ) ? implode(' ',$click_args['class']) : 'button' ),
		isset( $click_args['attributes'] ) ? wc_implode_html_attributes( $click_args['attributes'] ) : '',
		esc_html( __('Ver Producto', 'click') )
	);
