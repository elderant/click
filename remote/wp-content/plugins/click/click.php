<?php
/*
Plugin Name: Click
Description: Functions and modifications to match site requirements
Version:     1.0
Author:      Sebastian Guerrero
*/

// Script hooks.
add_action( 'wp_enqueue_scripts', 'click_scripts' );

function click_scripts () {
	wp_enqueue_script ( 'click-js', plugins_url('/js/main.js', __FILE__), array('jquery'),  rand(111,9999), 'all' );
	wp_enqueue_style ( 'click',  plugins_url('/css/main.css', __FILE__), array(),  rand(111,9999), 'all' );
}

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
	$custom_template = file_build_path(plugin_dir_path( __FILE__ ), 'templates', $folder, $filename);
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

function file_build_path(...$segments) {
	return join(DIRECTORY_SEPARATOR, $segments);
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

	error_log(print_r($child_categories,1));

	$args = (object)[
		'child_categories' => $child_categories,
		'main_list' => $main_list,
	];

	return $args;
}