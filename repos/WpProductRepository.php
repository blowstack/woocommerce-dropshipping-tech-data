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


  //  generate WP Post object
  public function generateWpPosts() {

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

  //  generate WP PostMeta object
  public function generateWpPostMetasBasic(array $post_metas) {
    $wpdb = $this->wpdb;

    foreach ($post_metas as $meta => $value) {

      $wpdb->query('start transaction');
      $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '$meta', '$value'
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration");
      $wpdb->query('commit');
    }
  }

  public function generateWpPostMetaSku() {

    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_sku', pt.manufacturer_id
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaPrice() {

    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_price', pt.price
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaRegularPrice() {

    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_regular_price', pt.price
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaStock() {

    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_stock', pt.stock
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration");
    $wpdb->query('commit');
  }
}
