<?php
/*
Plugin Name: Click
Description: Functions and modifications to match site requirements
Version:     1.0
Author:      Sebastian Guerrero
*/

include( plugin_dir_path( __FILE__ ) . 'includes/admin.php' );

// Script hooks.
add_action( 'wp_enqueue_scripts', 'click_scripts' );
//add_action( 'admin_enqueue_scripts', 'click_admin_scripts' );

function click_scripts () {
	wp_enqueue_script ( 'click-js', plugins_url('/js/main.js', __FILE__), array('jquery'),  rand(111,9999), 'all' );
	wp_enqueue_style ( 'click',  plugins_url('/css/main.css', __FILE__), array(),  rand(111,9999), 'all' );

	wp_localize_script( 'click-js', 'ajax_params', array('ajax_url' => admin_url( 'admin-ajax.php' )));

	if ( is_page( 5065 ) ) {
    wp_enqueue_script ( 'md5-js', 'https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.5.0/js/md5.min.js', array('jquery'), 'all' );
  }
}

// function click_admin_scripts () {
// 	wp_enqueue_script ( 'click-js-admin', plugins_url('/js/admin.js', __FILE__), array('jquery'),  rand(111,9999), 'all' );
// 	wp_enqueue_style ( 'main-admin',  plugins_url('/css/admin.css', __FILE__), array(),  rand(111,9999), 'all' );

// 	wp_localize_script( 'click-js-admin', 'ajax_params', array('ajax_url' => admin_url( 'admin-ajax.php' )));
// }

/************************************************************/
/********************* Helper functions *********************/
/************************************************************/

function click_load_template($template, $folder = '') {
	// first check if this is the page where you want to render your own template
	// if ( !is_page($the_page_you_want)) {
		// return $template;
	// }

	// get the actual file name, like single.php or page.php
	$filename = basename($template);
	if(!empty($folder) && strpos($folder, '/') !== 0) {
		$folder = '/' . $folder;
	}

	// build a path for the filename in a folder named for our plugin "fisherman" in the theme folder
	$custom_template = sprintf('%s/%s%s/%s', get_stylesheet_directory(), 'click', $folder, $filename);

	// if the template is found, awesome! return it. that's what we'll use.
	if ( is_file($custom_template) ) {
		return $custom_template;
	}

	// otherwise, build a path for the filename in a folder named "templates" in our plugin folder
	$custom_template = click_file_build_path(plugin_dir_path( __FILE__ ), 'templates', $folder, $filename);
	//$custom_template = sprintf('%stemplates%s/%s', plugin_dir_path( __FILE__ ), $folder, $filename);

	// found? return our plugin's default template
	if ( is_file($custom_template) ) {
		return $custom_template;
	}

	// otherwise, build a path for the filename in a folder named "templates" in our plugin folder
	$custom_template = sprintf('%stemplates/%s', plugin_dir_path( __FILE__ ), $filename);

	// found? return our plugin's default template
	if ( is_file($custom_template) ) {
		return $custom_template;
	}

	return $template;
}

function click_file_build_path($plugin, $template_folder, $folder, $filename) {
  return $plugin . DIRECTORY_SEPARATOR .
          $template_folder . DIRECTORY_SEPARATOR .
          $folder . DIRECTORY_SEPARATOR .
          $filename;
}

/************************************************************/
/************************ Educacion *************************/
/************************************************************/

function click_get_educacion_filter_html () {
	global $wp_query;

	// Category taxonomies query.
	// 
	$parent_ids = array(129, 138, 144);
	$args = array(
    'taxonomy' => 'portfolio-category',
		'hide_empty' => false,
		'include'	=> $parent_ids,
	);

	// The Term Query
	$term_query = new WP_Term_Query( $args );

	// foreach($term_query -> get_terms() as $term) {
	// 	$term_children = get_term_children( $term, $taxonomy );
	// 	if(count($term_children) > 0 ) {
	// 		$term -> child_terms = click_get_children_taxonomies($term, $term_children);
	// 	}
	// }

	$wp_query -> query_vars['click_args']['term_query'] = $term_query;
	$wp_query -> query_vars['click_args']['term_id'] = '';
	$wp_query -> query_vars['click_args']['category_args'] = array();

	$template_url = click_load_template('filter.php', 'educacion');
	load_template($template_url, true);
}
	
add_shortcode( 'click_educacion_filter', 'click_get_educacion_filter_html' );

function click_educacion_get_category_html($term_id, $display_name) {
	global $post;
	$post -> click_args = (object)[
		'current_category_id' => $term_id,
		'current_category_display_name' => $display_name,
	];

	$template_url = click_load_template('portfolio-category.php', 'educacion');
	load_template($template_url, false);
}

function click_educacion_get_category_html_args($term_id, $all_term = false) {
	if($all_term) {
		return (object)[
			'child_categories' => array(),
			'main_list' => '',
		];
	}

	$child_categories = get_categories(array( 'parent' => $term_id, 'taxonomy' => 'portfolio-category', 'hide_empty' => false, ));
	$main_list = $term_id == 129 || 
								$term_id == 138 ||
								$term_id == 144 ? ' main-list' : '';
	
	if(count($child_categories) > 0) {
		$all_term_id = '';
		foreach($child_categories as $category) {
			$all_term_id .= $category -> term_id . ' ';
		} 

		$all_term_object = (object)[
			'term_id' => $all_term_id,
			'name' => __('Todos', 'click'),
			// 'slug' => __('Todos', 'click'),
			// 'term_group' => 0,
			// 'term_taxonomy_id' => $all_term_id,
			// 'taxonomy' => 'portfolio-category',
			// 'description' => '',
			// 'parent' => $term_id,
			// 'count' => 0,
			// 'filter' => 'raw',
			// 'term_order' => 1,
			// 'cat_ID' => 141,
			// 'category_count' => 5,
			// 'category_description' => '',
			// 'cat_name' => __('Todos', 'click'),
			// 'category_nicename' => __('Todos', 'click'),
			// 'category_parent' => $term_id,
		];

		array_unshift($child_categories, $all_term_object);
	}

	$args = (object)[
		'child_categories' => $child_categories,
		'main_list' => $main_list,
	];

	return $args;
}

/************************************************************/
/*********************** Shop functions *********************/
/************************************************************/
function click_build_contextual_user_menu () {
	$template_url = click_load_template('shop-code.php', 'shop');
	load_template($template_url, true);
}
add_shortcode( 'click_shop_code_html', 'click_build_contextual_user_menu' );


// shop code form handlers
add_action( 'admin_post_nopriv_access_discount_shop', 'access_discount_shop_handler' );
add_action( 'admin_post_access_discount_shop', 'access_discount_shop_handler' );

function access_discount_shop_handler () {
	if ( isset( $_POST['action'] ) && strcasecmp($_POST['action'], 'access_discount_shop') == 0 ) {

		setcookie( 'shop_code', md5($_POST['shop_code']), 1 * DAYS_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		//$_SESSION['shopCode'] = md5($_POST['shop_code']);
		
    wp_redirect('/shop2/'); // CHANGE ON LIVE
    exit;
	}
}

add_action( 'the_post', 'click_check_shop_code' );

function click_check_shop_code() {
	global $wp_query;
	global $post;
	
  if(sizeOf($wp_query -> get_posts()) > 0 ) {
    if($wp_query -> get_posts()[0] -> ID == 5070) {
			$shop_codes = get_option( 'shop_codes', array() );
			
			//$code_used = $_SESSION['shopCode'];
			if(!isset($_COOKIE['shop_code'])) {
				wp_redirect('/shop3/');
			}
			$code_used = $_COOKIE['shop_code'];

			$valid_code = FALSE;
      foreach($shop_codes as $code) {
				if($code['code'] == $code_used) {
					$valid_code = TRUE;
					break;
				}
			}

			if(!$valid_code) {
				wp_redirect('/shop3/'); // CHANGE ON LIVE
			}
		}
		elseif($wp_query -> get_posts()[0] -> ID == 5065){
			if(!isset($_COOKIE['shop_code'])) {
				return;
			}
			$shop_codes = get_option( 'shop_codes', array() );
			$code_used = $_COOKIE['shop_code'];

			$valid_code = FALSE;
      foreach($shop_codes as $code) {
				if($code['code'] == $code_used) {
					$valid_code = TRUE;
					break;
				}
			}

			if($valid_code) {
				wp_redirect('/shop2/'); // CHANGE ON LIVE
			}
		}
	}
}

add_action( 'wp_ajax_nopriv_get_shop_codes_array', 'click_get_shop_codes_array' );
add_action( 'wp_ajax_get_shop_codes_array', 'click_get_shop_codes_array' );

function click_get_shop_codes_array() {
	$shop_codes = get_option( 'shop_codes', array() );
	echo json_encode($shop_codes);
	die();
}

add_filter('woocommerce_single_product_image_thumbnail_html', 'click_single_product_image_thumbnail_html', 10, 2);
function click_single_product_image_thumbnail_html($sprintf, $attachment_id) {
	return wc_get_gallery_image_html( $attachment_id, TRUE );
}

add_action('woocommerce_after_shop_loop_item', 'click_after_shop_loop_item', 5);
function click_after_shop_loop_item() {
	global $wp_query;
	global $product;
	$args = [];
	error_log('calling button loop for product : ' . $product -> get_id());

	$args['url'] = get_permalink( $product -> get_id() );
	$args['class'] = ['button', 'product_type_' . $product->get_type(), 'view-product'];
	$args['attributes'] = array(
		'data-product_id'  => $product->get_id(),
		'data-product_sku' => $product->get_sku(),
		'aria-label'       => $product->add_to_cart_description(),
		'rel'              => 'nofollow',
	);
	
	$wp_query -> query_vars['click_args'] = $args;

	$template_url = click_load_template('view-product.php', 'shop');
	load_template($template_url, FALSE);
}

/************************************************************/
/******************* Shop single functions ******************/
/************************************************************/

add_action('woocommerce_after_single_product_summary', 'click_remove_related_products', 1);
function click_remove_related_products() {
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}

add_action('woocommerce_after_single_product', 'click_add_back_to_shop', 99);

function click_add_back_to_shop() {
	$template_url = click_load_template('back-to-shop.php', 'shop-single');
	load_template($template_url, FALSE);
}

/************************************************************/
/********************* Checkout functions *******************/
/************************************************************/

function click_get_student_product_in_cart() {
	global $woocommerce;

	$args = array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'term_id',
				'terms' => 167,
			),
		),
	);
	$student_products = new WP_Query($args);
	$_cart = $woocommerce->cart->get_cart();

	$student_product_in_cart = FALSE;
	foreach($_cart as $product_array) {
		if(in_array($product_array['product_id'], $student_products->get_posts())){
			$student_product_in_cart = TRUE;
			break;
		}
	}
	
	return $student_product_in_cart;
}

function click_checkout_student_fields( $checkout ) {
	global $woocommerce;

	$student_product_in_cart = click_get_student_product_in_cart();
	if($student_product_in_cart) {
		echo '<div id="custom_checkout_field"><h3>' . __('Campos del estudiante') . '</h3>';
		woocommerce_form_field( 'order_school', array(
			'type'          => 'text',
			'class'         => array('school-definition-field form-row-wide'),
			'label'         => __('Colegio'),
			'placeholder'   => __('Nombre del colegio.'),
			'required'  		=> true,
			), $checkout->get_value( 'order_school' ));

		woocommerce_form_field( 'order_room', array(
			'type'          => 'text',
			'class'         => array('room-definition-field form-row-wide'),
			'label'         => __('Salón'),
			'placeholder'   => __('Número del salón'),
			'required'  		=> true,
			), $checkout->get_value( 'order_room' ));
		
		woocommerce_form_field( 'order_student', array(
			'type'          => 'text',
			'class'         => array('student-definition-field form-row-wide'),
			'label'         => __('Estudiante'),
			'placeholder'   => __('Nombre del estudiante.'),
			'required'  		=> true,
			), $checkout->get_value( 'order_student' ));
		echo '</div>';
	}
}
add_action( 'woocommerce_after_order_notes', 'click_checkout_student_fields' );

/**
 * Process the checkout
 */
function click_checkout_validate_student_fields() {
	// Check if set, if its not set add an error.
	$student_product_in_cart = click_get_student_product_in_cart();
	if($student_product_in_cart) {
		if ( ! $_POST['order_school'] ) {
			wc_add_notice( __( 'El nombre del colegio es un campo requerido.' ), 'error' );
		}
		
		if ( ! $_POST['order_room'] ) {
			wc_add_notice( __( 'El número del salón es un campo requerido.' ), 'error' );
		}

		if ( ! $_POST['order_student'] ) {
			wc_add_notice( __( 'El nombre del estudiante es un campo requerido.' ), 'error' );
		}
	}
}

add_action('woocommerce_checkout_process', 'click_checkout_validate_student_fields');

/**
 * Update the order meta with field value
 */
function click_order_student_fields_update( $order_id ) {
	if ( ! empty( $_POST['order_school'] ) ) {
		update_post_meta( $order_id, 'Order School', sanitize_text_field( $_POST['order_school'] ) );
	}
	if ( ! empty( $_POST['order_room'] ) ) {
		update_post_meta( $order_id, 'Order Room', sanitize_text_field( $_POST['order_room'] ) );
	}
	if ( ! empty( $_POST['order_student'] ) ) {
		update_post_meta( $order_id, 'Order Student', sanitize_text_field( $_POST['order_student'] ) );
	}
}
add_action( 'woocommerce_checkout_update_order_meta', 'click_order_student_fields_update' );

/**
 * Hide shipping method when a Lista Escolar product is on cart.
 *
 * @param array $rates Array of rates found for the package.
 * @return array
 */
function click_hide_salon_shipping_method( $rates ) {
	$student_product_in_cart = click_get_student_product_in_cart();
	if(!$student_product_in_cart) {
		unset($rates['free_shipping:2']);
	}

	return $rates;
}
add_filter( 'woocommerce_package_rates', 'click_hide_salon_shipping_method', 100 );

/************************************************************/
/*********************** Admin functions ********************/
/************************************************************/

function click_order_data_add_custom_fields($order) {
	$order_school = $order -> get_meta('Order School', true);
	$print_order_school = empty($order_school) ? 'No especificado' : $order_school;

	$order_room = $order -> get_meta('Order Room', true);
	$print_order_room = empty($order_room) ? 'No especificado' : $order_room;

	$order_student = $order -> get_meta('Order Student', true);
	$print_order_student = empty($order_student) ? 'No especificado' : $order_student;

	echo '<p><strong>' . __('Colegio del Estudiante', 'click') . '</strong><br>' . 
		$print_order_school . 
		'</p>';
	
	echo '<p><strong>' . __('Salón del Estudiante', 'click') . '</strong><br>' . 
		$print_order_room . 
		'</p>';
	
	echo '<p><strong>' . __('Nombre del Estudiante', 'click') . '</strong><br>' . 
		$print_order_student . 
		'</p>';
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'click_order_data_add_custom_fields', 10, 1 );