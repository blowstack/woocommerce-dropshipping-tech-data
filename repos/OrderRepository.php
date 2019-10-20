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

  public function registerNewHardwareOrder($wp_order_id, $msg_id, $items, $env, $full) {
    $wpdb = $this->wpdb;
    $orders_table_name = TablesRepository::getTableNameOrdersHardware();

    $wpdb->insert("$orders_table_name",
      [
        'order_id' => $wp_order_id,
        'msg_id' => $msg_id,
        'env' => $env,
        'full' => $full
      ]
    );
  }

  public function getNewMsgId($env) {
    $wpdb = $this->wpdb;
    $orders_table_name = TablesRepository::getTableNameOrdersHardware();

    $result = $wpdb->get_results("select max(msg_id) as msg_id from $orders_table_name where env = '$env' limit 1");

    $last_msg_id = $result[0]->msg_id ? $result[0]->msg_id : 0;

    return $last_msg_id + 1;
  }

}