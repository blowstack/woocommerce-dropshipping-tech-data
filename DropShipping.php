<?php

/**
 * @package tech-data dropshipping woocommerce plugin
 */

/*
 Plugin name: DropShipping Techdata WooCommerce Edition
 Description: This plugin provides interface to interact with Techdata warehouses using WooCommerce.
 */

if ( ! defined('ABSPATH')) {
  die;
}

defined('ABSPATH') or die('ABSPATH not defined!');

if ( ! function_exists( 'add_action')) {
  echo 'Wordpress encounter problem, add_action isn\'t working!';
  exit;
}

class DropShipping {


  public $plugin_name;
  public static $plugin_table_prefix = 'dropshipping_';
  public static $csv_folder_path = "/upload/csv/";
  public static $zip_folder_path = "/upload/zip/";
  public static $type_software = 'software';
  public static $type_hardware = 'hardware';

  function __construct() {
    $this->plugin_name = plugin_basename(__FILE__);
    require_once( dirname( __FILE__ ) . '/repos/TablesRepository.php' );
    require_once( dirname( __FILE__ ) . '/TechDataConfigManager.php' );
    require_once( dirname( __FILE__ ) . '/TechDataSynchronizer.php' );
    require_once( dirname( __FILE__ ) . '/TechDataProductGenerator.php' );
  }

  /**
   * @return string
   */
  public static function getTablePrefixes() {
    global $wpdb;
    return $wpdb->prefix . self::$plugin_table_prefix;
  }

  /**
   * @return string
   */
  public static function getCsvFolderPath() {
    return dirname(__FILE__) . self::$csv_folder_path;
  }

  /**
   * @return string
   */
  public static function getZipFolderPath() {
    return dirname(__FILE__) . self::$zip_folder_path;
  }

  function install() {

    // create and make all required tables and alternations
    $TablesRepository = new TablesRepository();
    $TablesRepository->createProductTable();
    $TablesRepository->createProfitTable();
    $TablesRepository->createTemporaryHardwareMaterialsTable();
    $TablesRepository->createTemporaryHardwarePricesTable();
    $TablesRepository->createTemporarySoftwareTable();
    $TablesRepository->createFtpConfigTable();
    $TablesRepository->createOrdersTable();
    $TablesRepository->createOrderItemsTable();
    $TablesRepository->alterWpPostsTable();

  }

  function uninstall() {
    global $wpdb;

    $table_name = $wpdb->prefix . "dropshipping_techdata_temp";

    $sql = "drop table if exists $table_name";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

  }

  function register() {
    add_action('admin_enqueue_scripts', [ $this, 'enqueue']);
    add_action('admin_menu', [ $this, 'add_admin_pages']);
    add_filter("plugin_action_links_$this->plugin_name", [ $this, 'settings_link']);
  }

  function settings_link($links) {
    $settings_links = '<a href="options-general.php?page=Dropshipping">Settings</a>';
    array_push($links, $settings_links);
    return $links;
  }


  function add_admin_pages() {
    add_menu_page('DropShipping', 'Dropshipping', 'manage_options', 'DropShipping', [ $this, 'admin_index'],'dashicons-store', 10);
    add_submenu_page( 'DropShipping', 'software', 'Software', 'manage_options', 'DropShipping/software.php', [ $this, 'software_index'] );
    add_submenu_page( 'DropShipping', 'hardware', 'Hardware', 'manage_options', 'DropShipping/hardware.php', [ $this, 'hardware_index'] );
  }

  function admin_index() {
    require_once plugin_dir_path(__FILE__) . 'templates/admin.php';
  }

  function software_index() {
    require_once plugin_dir_path(__FILE__) . 'templates/software.php';
  }

  function hardware_index() {
    require_once plugin_dir_path(__FILE__) . 'templates/hardware.php';
  }

  function enqueue() {
      wp_enqueue_style('dropshipping_styles', plugins_url('/assets/dropshipping.css', __FILE__), [], '1');
      wp_enqueue_script('dropshipping_scripts', plugins_url('/assets/dropshipping.js', __FILE__), [], '1');
  }
}

if ( class_exists('DropShipping')) {
  $DropShipping = new DropShipping();
  $DropShipping->register();
}

function woocommerce_product_custom_fields() {
  global $woocommerce, $post;


  echo '<div class="product_custom_field">';
  woocommerce_wp_text_input(
    array(
      'id' => '_manufacturer_id',
      'placeholder' => 'Kod producenta',
      'label' => __('Kod producenta', 'woocommerce'),
      'desc_tip' => 'true'
    )
  );
  echo '</div>';

  echo '<div class="product_custom_field">';
  woocommerce_wp_text_input(
    array(
      'id' => '_cost',
      'placeholder' => 'Koszt',
      'label' => __('Koszt', 'woocommerce'),
      'desc_tip' => 'true'
    )
  );
  echo '</div>';

  echo '<div class="product_custom_field">';
  woocommerce_wp_text_input(
    array(
      'id' => '_brand',
      'placeholder' => 'Brand',
      'label' => __('Brand', 'woocommerce'),
      'desc_tip' => 'true'
    )
  );
  echo '</div>';

  echo '<div class="product_custom_field">';
  woocommerce_wp_select(
    array(
      'id' => '_dropshipping',
      'label' => __('Dropshipping', 'woocommerce'),
      'desc_tip' => 'true',
      'options' => [
        'none' =>__('none', 'woocommerce'),
        'software' =>__('software', 'woocommerce'),
        'hardware' =>__('hardware', 'woocommerce'),
      ]
    )
  );
  echo '</div>';

}

function woocommerce_product_custom_fields_save($post_id)
{
  // Producer Code
  $woocommerce_custom_product_text_field = $_POST['_manufacturer_id'];
  if (!empty($woocommerce_custom_product_text_field))
    update_post_meta($post_id, '_manufacturer_id', esc_attr($woocommerce_custom_product_text_field));

  // Cost
  $woocommerce_custom_product_text_field = $_POST['_cost'];
  if (!empty($woocommerce_custom_product_text_field))
    update_post_meta($post_id, '_cost', esc_attr($woocommerce_custom_product_text_field));

  // Brand
  $woocommerce_custom_product_text_field = $_POST['_brand'];
  if (!empty($woocommerce_custom_product_text_field))
    update_post_meta($post_id, '_brand', esc_attr($woocommerce_custom_product_text_field));


  // Dropshipping
  $woocommerce_custom_product_select_field = $_POST['_dropshipping'];
  if (!empty($woocommerce_custom_product_select_field))
    update_post_meta($post_id, '_dropshipping', esc_attr($woocommerce_custom_product_select_field));

}

function woocommerce_product_custom_fields_display() {
  global $post;
  // Check for the custom field value
  $product = wc_get_product( $post->ID );
  $manufacturer_id = $product->get_meta( '_manufacturer_id' );
  $producer_brand = $product->get_meta( '_brand' );
  if( $producer_brand ) {
    // Only display our field if we've got a value for the field title
    echo "<div class='cfwc-custom-field-wrapper'>Producent: $producer_brand</div>";
  }
  if( $manufacturer_id ) {
    // Only display our field if we've got a value for the field title
    echo "<div class='cfwc-custom-field-wrapper'>Kod producenta: $manufacturer_id</div>";
  }
}


// todo czy to jest potrzebne
function register_myclass() {
  class TechDataProduct extends WC_Product {

    public function __construct($product = 0) {
      parent::__construct($product);
    }
  }
}

register_activation_hook(__FILE__, [ $DropShipping , 'install']);

// todo czy to jest potrzebne
add_action( 'init', 'register_myclass' );

// display in frontend additional fields
add_action( 'woocommerce_before_add_to_cart_button', 'woocommerce_product_custom_fields_display' );

// display in backend additional fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');

// save additional fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');

// place order
add_action( 'woocommerce_order_details_after_order_table', 'placeOrderTechData');

//// cron ftp
//add_action( 'dropshipping_software_sync', 'syncAllSoftwareFromTechData');


// search engine
add_filter('posts_join', 'cf_search_join' );

add_filter( 'posts_where', 'cf_search_where' );

add_filter( 'posts_distinct', 'cf_search_distinct' );
// search engine



// search engine
function cf_search_join( $join ) {
  global $wpdb;

  if ( is_search() ) {
    $join .=' LEFT JOIN '.TablesRepository::getTableNameProduct(). ' ON '. $wpdb->posts . '.dropshipping_id = ' . TablesRepository::getTableNameProduct() . '.distributor_id ';
  }

  return $join;
}

function cf_search_where( $where ) {
  global $pagenow, $wpdb;

  if ( is_search() ) {
    $where = preg_replace(
      "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
      "(".$wpdb->posts.".post_title LIKE $1) OR (".TablesRepository::getTableNameProduct().".manufacturer_id LIKE $1)", $where );
  }

  return $where;
}

function cf_search_distinct( $where ) {
  global $wpdb;

  if ( is_search() ) {
    return "DISTINCT";
  }

  return $where;
}
// search engine



function placeOrderTechData( $order ) {

  include_once(ABSPATH . 'TechDataSoftwareApi.php');
  global $wpdb;

  $orders_table_name = TablesRepository::getTableNameOrders();

  $status = $order->get_status();
  $restricted_statues = ['on-hold', 'failed', 'cancelled'];
//    $restricted_statues = ['on-hold', 'pending', 'failed', 'cancelled'];

  // no payment or error
  if (in_array($status, $restricted_statues)) {
    return TechDataSoftwareApi::printWarning();
  }

  $orderId = $order->get_id();
  $Items = $order->get_items();
  $dropshipping_software_order_check =  $wpdb->get_results("select * from $orders_table_name where order_id = '$orderId' and (response_code = 3002 or response_code = 3001)");


//  if (!$dropshipping_software_order_check && $status == 'processing') {
  if (!$dropshipping_software_order_check) {

    $preparedOrderItems = TechDataSoftwareApi::prepareSoftwareOrderItems($Items);
    $TechDataSoftwareApi = new TechDataSoftwareApi($orderId, $preparedOrderItems);
    $TechDataSoftwareApi->placeOrder();

    $dropshipping_reference_no = $TechDataSoftwareApi->getOrderReferenceNo();
    $dropshipping_response_code = $TechDataSoftwareApi->getResponseCode();
    $dropshipping_response_message = $TechDataSoftwareApi->getResponseMessage();
    $dropshipping_response_items = $TechDataSoftwareApi->getOrderResponseItems();
    $TechDataSoftwareApi->registerNewSoftwareOrder($orderId, $dropshipping_reference_no, $dropshipping_response_code, $dropshipping_response_message, $dropshipping_response_items);
  }

}

//function syncAllSoftwareFromTechData() {
////  require_once( dirname( __FILE__ ) . '/TechDataSynchronizer.php' );
//
////  global $wpdb;
////  $table_name = TablesRepository::getTableNameProduct();
////  $wpdb->query("update $table_name set price = 2 where price is not null");
//  $TechDataSynchronizer = new TechDataSynchronizer(DropShipping::$type_software);
//  $TechDataSynchronizer->syncAllFromTechData();
//}
