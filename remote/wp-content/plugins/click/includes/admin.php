<?php

// shop code form handlers
add_action( 'admin_post_nopriv_update_shop_code', 'update_shop_code_handler' );
add_action( 'admin_post_update_shop_code', 'update_shop_code_handler' );

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class SP_Plugin {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $codes_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
    add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
    add_action( 'admin_menu', [ $this, 'plugin_submenu' ] );
  }
  
  public static function set_screen( $status, $option, $value ) {
    return $value;
  }
  
  public function plugin_menu() {
  
    $hook = add_menu_page(
      'Códigos de la tienda',
      'Códigos de la tienda',
      'manage_options',
      'wp_list_shop_codes',
      [ $this, 'plugin_settings_page' ]
    );
  
    add_action( "load-$hook", [ $this, 'screen_option' ] );
  }

  public function plugin_submenu() {
    $hook = add_submenu_page(
      'wp_list_shop_codes',
      'Agregar Código',
      'Agregar Código',
      'manage_options',
      'wp_add_shop_code',
      [ $this, 'plugin_create_page' ]
    );
  }

  /**
  * Screen options
  */
  public function screen_option() {

    $option = 'per_page';
    $args   = [
      'label'   => 'Codes',
      'default' => 5,
      'option'  => 'codes_per_page'
    ];

    add_screen_option( $option, $args );

    $this->codes_obj = new Shop_Codes_List();
  }

  /**
  * Plugin settings page
  */
  public function plugin_settings_page() {
    global $wp_query;
    $wp_query -> query_vars['click_admin_args']['codes_obj'] = $this->codes_obj;
    
    $template_url = click_load_template('shop-codes-list.php', 'admin');
    load_template($template_url, true);
  }

  /** Singleton instance */
  public static function get_instance() {
    if ( ! isset( self::$instance ) ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Plugin settings page
   */
  public function plugin_create_page() {
    global $wp_query;
    $wp_query -> query_vars['click_admin_args']['name'] = null;

    $template_url = click_load_template('shop-codes-create.php', 'admin');
    load_template($template_url, true);
  }

}// End SP_Plugin Class

function update_shop_code_handler () {
  if ( isset( $_POST['action'] ) && strcasecmp($_POST['action'], 'update_shop_code') == 0 ) {
    $name = $_POST['name'];
    $shop_code_name = $_POST['shop_code_name'];
    $shop_code_hashed = md5($_POST['shop_code_base']);
    
    if (empty($name)) {
      $shop_options = get_option( 'shop_codes', array() );
      array_push($shop_options, array('name' => $shop_code_name,'code' => $shop_code_hashed));
      update_option( 'shop_codes', $shop_options );
    }
    else {
      //TODO
    }
    wp_redirect(get_admin_url(null, '/admin.php?page=wp_list_shop_codes'));
  }
}


add_action( 'plugins_loaded', function () {
	SP_Plugin::get_instance();
} );

class Shop_Codes_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Shop code', 'click' ), //singular name of the listed records
			'plural'   => __( 'Shop codes', 'click' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

  }
  
  /**
   * Retrieve shop code data from the database
   *
   * @param int $per_page
   * @param int $page_number
   *
   * @return mixed
   */
  public static function get_codes( $per_page = 5, $page_number = 1 ) {
    return get_option( 'shop_codes', array() );
    
    // global $wpdb;

    // $sql = "SELECT * FROM {$wpdb->prefix}users";

    // if ( ! empty( $_REQUEST['orderby'] ) ) {
    //   $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
    //   $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
    // }

    // $sql .= " LIMIT $per_page";
    // $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
    // $result = $wpdb->get_results( $sql, 'ARRAY_A' );

    // error_log('get codes result : ' . print_r($result,1));

    // return $result;
  }
  
  /**
   * Delete a customer record.
   *
   * @param int $id customer ID
   */
  public static function delete_code( $name ) {
    $shop_codes = get_option( 'shop_codes', array() );
    $index = 0;
    foreach($shop_codes as $code ) {
      if($code['name'] == $name) {
        break;
      }
      $index++;
    }
    unset($shop_codes[$index]);
    update_option( 'shop_codes', $shop_codes );
    // global $wpdb;

    // $wpdb->delete(
    //   "{$wpdb->prefix}users",
    //   [ 'ID' => $id ],
    //   [ '%d' ]
    // );
  }

  /**
   * Returns the count of records in the database.
   *
   * @return null|string
   */
  public static function record_count() {
    return count(get_option( 'shop_codes', array() ));


    // global $wpdb;
    // $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}users";
    // return $wpdb->get_var( $sql );
  }

  /**
   * Method for name column
   *
   * @param array $item an array of DB data
   *
   * @return string
   */
  function column_name( $item ) {
    // create a nonce
    $delete_nonce = wp_create_nonce( 'click_delete_code' );
    
    $title = '<strong>' . $item['name'] . '</strong>';

    $actions = [
      'delete' => sprintf( '<a href="?page=%s&action=%s&code=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete_code', $item['name'], $delete_nonce )
    ];

    return $title . $this->row_actions( $actions );
  }

  /**
   * Render a column when no column specific method exists.
   *
   * @param array $item
   * @param string $column_name
   *
   * @return mixed
   */
  public function column_default( $item, $column_name ) {
    switch ( $column_name ) {
      case 'address':
      case 'city':
        return $item[ $column_name ];
      default:
        return print_r( $item, true ); //Show the whole array for troubleshooting purposes
    }
  }

  /**
   * Render the bulk edit checkbox
   *
   * @param array $item
   *
   * @return string
   */
  function column_cb( $item ) {
    return sprintf(
      '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['name']
    );
  }

  /**
   *  Associative array of columns
   *
   * @return array
   */
  function get_columns() {
    $columns = [
      'cb'      => '<input type="checkbox" />',
      'name'    => __( 'Nombre', 'click' ),
    ];

    return $columns;
  }

  /**
   * Columns to make sortable.
   *
   * @return array
   */
  public function get_sortable_columns() {
    $sortable_columns = array(
      'name' => array( 'name', true ),
    );

    return $sortable_columns;
  }

  /**
   * Returns an associative array containing the bulk action
   *
   * @return array
   */
  public function get_bulk_actions() {
    $actions = [
      'bulk-delete' => 'Delete',
    ];

    return $actions;
  }

  /**
   * Handles data query and filter, sorting, and pagination.
   */
  public function prepare_items() {

    $this->_column_headers = $this->get_column_info();

    /** Process bulk action */
    $this->process_bulk_action();

    $per_page     = $this->get_items_per_page( 'codes_per_page', 10 );
    $current_page = $this->get_pagenum();
    $total_items  = self::record_count();

    $this->set_pagination_args( [
      'total_items' => $total_items, //WE have to calculate the total number of items
      'per_page'    => $per_page //WE have to determine how many items to show on a page
    ] );


    $this->items = self::get_codes( $per_page, $current_page );
  }

  public function process_bulk_action() {
    //Detect when a bulk action is being triggered...
    if ( 'delete_code' === $this -> current_action() ) {
      // In our file that handles the request, verify the nonce.
      $nonce = esc_attr( $_REQUEST['_wpnonce'] );
  
      if ( ! wp_verify_nonce( $nonce, 'click_delete_code' ) ) {
        die( 'Something went wrong please contact your web developer' );
      }
      else {
        self::delete_code( $_GET['code'] );
  
        wp_redirect(get_admin_url(null, '/admin.php?page=wp_list_shop_codes'));
        exit;
      }
  
    }
  
    // If the delete bulk action is triggered
    if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
         || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
    ) {
      
      $delete_ids = esc_sql( $_POST['bulk-delete'] );
      // loop over the array of record IDs and delete them
      foreach ( $delete_ids as $id ) {
        self::delete_code( $id );
      }
      
      wp_redirect(get_admin_url(null, '/admin.php?page=wp_list_shop_codes'));
      exit;
    }
  }

  protected function get_primary_column_name(){
    return 'name';
  }
}// end Shop_Codes_List class