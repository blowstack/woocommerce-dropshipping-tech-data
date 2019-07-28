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
                        where dropshipping = 'software'
                        and NOT EXISTS(
                              SELECT 1
                              FROM wp_posts
                              WHERE `own_migration` = wp_dropshipping_techdata_soft_temp.distributor_id
                              )");
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
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '$meta' and 
                              `post_id` = post.id )");
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
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_sku' and 
                              `post_id` = post.id   )");
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
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_price' and 
                              `post_id` = post.id   )");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaRegularPrice() {
    //                          post.id, '_regular_price', pt.price + (select profit_margin from wp_terms where
    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_regular_price', pt.price 
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_regular_price' and 
                              `post_id` = post.id   )");
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
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_stock' and 
                              `post_id` = post.id   )");
    $wpdb->query('commit');
  }

  public function getTechDataCategories(string $dropshipping): array {

    $wpdb = $this->wpdb;
    $results =  $wpdb->get_results("select category_1 from wp_dropshipping_techdata_soft_temp where dropshipping = '$dropshipping' group by category_1", ARRAY_A);
    $array_categories = [];

    foreach ($results as $result => $value) {
      $array_categories[] = $value['category_1'];
    }

    return $array_categories;
  }

  public function insertNewCategories($categories) {
    $wpdb = $this->wpdb;

    $wpdb->query('start transaction');
    foreach ($categories as $category) {

      $wpdb->query("insert into wp_terms
                          (
                            `name`, slug
                          )
                          select
                            '$category', replace('$category', ' ', '-')
                          from dual
                          WHERE NOT EXISTS(
                              SELECT 1
                              FROM wp_terms
                              WHERE `name` = '$category'
                              )
                          ");

      $wpdb->query("insert into wp_term_taxonomy
                          (
                          term_id, taxonomy, `description`
                          )
                          
                          select
                          wp_terms.term_id, 'product_cat', wp_terms.`name`
                          from wp_terms
                          WHERE NOT EXISTS(
                              SELECT 1
                              FROM wp_term_taxonomy
                              WHERE `term_id` = wp_terms.term_id
                              )
                          ");
    }
    $wpdb->query('commit');
  }

  public function generateProductType() {

    $wpdb = $this->wpdb;

    $wpdb->query('start transaction');
    // simple product
    $wpdb->query("set @simple_product = (select taxonomy.term_taxonomy_id from wp_term_taxonomy taxonomy inner join wp_terms term on term.term_id = taxonomy.term_id where term.name = 'simple' and taxonomy.taxonomy = 'product_type' limit 1 )");
    $wpdb->query("insert ignore into wp_term_relationships
                        (
                          object_id, term_taxonomy_id, term_order
                        )
                        select post.id, @simple_product, 0
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration;");
    $wpdb->query('commit');
  }

  public function generateProductLang() {

    $wpdb = $this->wpdb;

    $wpdb->query('start transaction');
    // product_lang
    $wpdb->query("set @product_lang = (select taxonomy.term_taxonomy_id from wp_term_taxonomy taxonomy inner join wp_terms term on term.term_id = taxonomy.term_id where taxonomy.taxonomy = 'language' limit 1 )");
    $wpdb->query("insert ignore into wp_term_relationships
                        (
                          object_id, term_taxonomy_id, term_order
                        )
                        select post.id, @product_lang, 0
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration;");
    $wpdb->query('commit');
  }

  public function generateProductCategory() {

    $wpdb = $this->wpdb;

    $wpdb->query('start transaction');
    $wpdb->query("insert ignore into wp_term_relationships
                        (
                          object_id, term_taxonomy_id, term_order
                        )
                        select post.id as object_id, taxonomy.term_taxonomy_id, 0
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        inner join wp_terms term on term.name = pt.category_1
                        inner join wp_term_taxonomy taxonomy on taxonomy.term_id = term.term_id");
    $wpdb->query('commit');
  }

}