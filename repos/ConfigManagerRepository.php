<?php

/**
 * Class ConfigManagerRepository
 */
class ConfigManagerRepository {

  private $wpdb;

  public function __construct() {
    global $wpdb;
    $this->wpdb = $wpdb;
  }


  /**
   * @param $dropshipping_type
   * @return array|object|null
   */
  public function getConfig($dropshipping_type) {
    $wpdb = $this->wpdb;
    $table_name = TablesRepository::getTableNameFtpConfig();

    $config = $wpdb->get_results("select * from $table_name where dropshipping_type = '$dropshipping_type' order by id desc limit 1");

    return $config;
  }

  public function setConfig($config, $dropshipping_type) {
    $wpdb = $this->wpdb;
    $table_name = TablesRepository::getTableNameFtpConfig();

    $user = $config['user'];
    $password = $config['password'];
    $server_address = $config['server_address'];
    $file_name = $config['file_name'];

    $wpdb->query("INSERT INTO $table_name (dropshipping_type, user, password, server_ip, file_name)
                        VALUES ('$dropshipping_type','$user', '$password', '$server_address', '$file_name'  )
                        ");
  }

}