<?php

/**
 * Class OrderRepository
 */
class OrderRepository {

  private $wpdb;

  public function __construct() {
    global $wpdb;
    $this->wpdb = $wpdb;

  }

  public function registerNewSoftwareOrder($wp_order_id, $order_reference_no, $response_code, $response_message, $response_items  ): void {
    $wpdb = $this->wpdb;
    $orders_table_name = TablesRepository::getTableNameOrders();
    $order_items_table_name = TablesRepository::getTableNameOrderItems();

    $wpdb->insert("$orders_table_name",
      [
        'order_id' => $wp_order_id,
        'order_reference_no' => $order_reference_no,
        'response_code' => $response_code,
        'response_message' => $response_message,
        'full' => json_encode($response_items),
      ]
    );

    $orderTechDataId = $wpdb->insert_id;

    foreach ($response_items as $item) {
      if ($item['distributorItemIdentifier'] && $item['orderlineReferenceNo']) {
        $wpdb->insert("$order_items_table_name", array('order_tech_data_id' => $orderTechDataId, 'distributor_item_identifier' => $item['distributorItemIdentifier'], 'order_line_reference_no' => $item['orderlineReferenceNo'],));
      }
    }
  }

}