<?php


class WpProductRepository {

  private $wpdb;
  private $table_name;

  public function __construct() {
    global $wpdb;
    $this->wpdb = $wpdb;
  }

  public function insertFromCSV($table_name, $distributor_id, $manufacturer_id, $brand, $description, $price, $stock, $category_1, $category_2, $ean, $status, $dropshipping) {
    $wpdb = $this->wpdb;

    $wpdb->query("insert ignore into $table_name(distributor_id, manufacturer_id, brand, description, price, stock, category_1, category_2, ean, status, dropshipping)
                                values('$distributor_id', '$manufacturer_id', '$brand', '$description', '$price', '$stock', '$category_1', '$category_2', '$ean', '$status', '$dropshipping')");
  }

  public function generateWpProducts() {

    $wpdb = $this->wpdb;

    $wpdb->query('start transaction');
    $wpdb->query('set @currentDate := curdate()');
    $wpdb->query("insert into wp_posts
                        (
                          post_author, post_date, post_date_gmt, post_content,
                          post_title, post_excerpt, post_status, comment_status,
                          ping_status, post_name, to_ping, pinged,
                          post_modified, post_modified_gmt, post_content_filtered, post_type, own_migration
                        )

                        select
                          1, @currentDate, @currentDate, `description`,
                            `description`, ' ',  'publish', 'open',
                             'closed', slugify(left(replace(replace(description, '', ''), '', ''), 100)), ' ', ' ',
                            @currentDate, @currentDate, ' ', 'product', distributor_id
                        from wp_dropshipping_techdata_soft_temp
                        where dropshipping = 'software'");
    $wpdb->query('commit');

  }
}
