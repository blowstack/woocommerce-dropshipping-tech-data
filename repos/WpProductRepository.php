<?php


class WpProductRepository {

  private $wpdb;
  private $table_name;

  public function __construct() {
    global $wpdb;
    $this->wpdb = $wpdb;
  }

  public function insertRawFromCSV($table_name, $file_path) {
    $wpdb = $this->wpdb;

    $wpdb->query("
    LOAD DATA  INFILE '{$file_path}'
    IGNORE INTO TABLE $table_name
    FIELDS TERMINATED BY ';' ENCLOSED BY '\"'
    LINES TERMINATED BY '\r\n'
    IGNORE 1 LINES
    ");
  }
  public function insertFromCSV($table_name, $distributor_id, $manufacturer_id, $brand, $description, $price, $stock, $category_1, $category_2, $ean, $status, $dropshipping) {
    $wpdb = $this->wpdb;
    $wpdb->query("insert ignore into $table_name(distributor_id, manufacturer_id, brand, description, price, stock, category_1, category_2, ean, status, dropshipping)
                                values('$distributor_id', '$manufacturer_id', '$brand', '$description', '$price', '$stock', '$category_1', '$category_2', '$ean', '$status', '$dropshipping')");
  }

  public function insertMaterialsIntoDropshipping($target_table_name, $source_table_name) {
    $wpdb = $this->wpdb;

    $wpdb->query("insert ignore into $target_table_name(distributor_id, manufacturer_id, brand, description, price, stock, category_1, category_2, ean, status, dropshipping)
                        select SapNo, PartNo, Vendor, Nazwa, ' ', Magazyn, FamilyPr_PL, PodklasaPr_PL, EAN, 'live', 'hardware'
                        from $source_table_name");
  }

  public function updateDropshippingHardware($target_table_name, $source_prices_table_name, $source_stock_table_name, $source_profit_table_name) {
    $wpdb = $this->wpdb;

    // prices
    $wpdb->query('start transaction');
    $wpdb->query("update $target_table_name target
                        inner join $source_prices_table_name source
                        set target.price = source.Cena_w_TD
                        where distributor_id = source.SapNo");
    $wpdb->query('commit');

    // stock
    $wpdb->query('start transaction');
    $wpdb->query("update $target_table_name target
                        inner join $source_stock_table_name source
                        set target.stock = source.Magazyn
                        where distributor_id = source.SapNo");
    $wpdb->query('commit');



  }

  public function clearTemporaryTables(array $tables_names) {
    $wpdb = $this->wpdb;

    foreach ($tables_names as $table_name) {
      $wpdb->query('start transaction');
      $wpdb->query("truncate table $table_name");
      $wpdb->query('commit');
    }
  }

  /**
   * @param $table_name
   * @return array|object|null
   */
  public function getForCSV() {
    $wpdb = $this->wpdb;

    $products = $wpdb->get_results("
                        select  sku.meta_value as sap_no, manufacturer.meta_value as producer_code, vendor.meta_value as vendor, post.post_title as title,  price.meta_value as price, stock.meta_value as stock, round(price.meta_value - cost.meta_value,2) as profit
                        from wp_posts post
                        inner join wp_postmeta sku on post.id = sku.post_id
                        inner join wp_postmeta stock on post.id = stock.post_id
                        inner join wp_postmeta manufacturer on post.id = manufacturer.post_id
                        inner join wp_postmeta price on post.id = price.post_id
                        inner join wp_postmeta cost on post.id = cost.post_id
                        inner join wp_postmeta vendor on post.id = vendor.post_id
                        where post.post_type = 'product'
                        and sku.meta_key = '_sku'
                        and stock.meta_key = '_stock'
                        and manufacturer.meta_key = '_manufacturer_id'
                        and price.meta_key = '_regular_price'
                        and cost.meta_key = '_cost'
                        and vendor.meta_key = '_brand'
                        ");

    return $products;
  }


  //  generate WP Post object
  public function generateWpPosts($dropshipping) {

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
                        where dropshipping = '$dropshipping'
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
                          post.id, '_sku', pt.distributor_id
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_sku' and 
                              `post_id` = post.id   )");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaManufacturer() {

    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_manufacturer_id', pt.manufacturer_id
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_manufacturer_id' and 
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

  public function generateWpPostMetaProducerCode() {
    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_manufacturer_id', pt.manufacturer_id 
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_manufacturer_id' and 
                              `post_id` = post.id   )");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaCost() {
    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_cost', pt.price 
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_cost' and 
                              `post_id` = post.id   )");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaBrand() {
    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_brand', pt.brand 
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_brand' and 
                              `post_id` = post.id   )");
    $wpdb->query('commit');
  }

  public function generateWpPostMetaDropShipping($dropshipping) {
    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_dropshipping', '$dropshipping' 
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_dropshipping' and 
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

  public function generateWpPostMetaImage() {

    $wpdb = $this->wpdb;
    $wpdb->query('start transaction');
    $wpdb->query("insert into wp_postmeta
                        (
                          post_id, meta_key, meta_value
                        )
                        select
                          post.id, '_thumbnail_id', image.id
                        from wp_posts post
                        inner join wp_dropshipping_techdata_soft_temp pt on pt.distributor_id = post.own_migration
                        inner join wp_posts image on image.post_title = pt.brand
                        where NOT EXISTS(
                              SELECT 1
                              FROM wp_postmeta
                              WHERE `meta_key` = '_thumbnail_id' and 
                              `post_id` = post.id   )
                        and image.post_type = 'attachment'");
    $wpdb->query('commit');
  }

  public function updatePrice() {
    $wpdb = $this->wpdb;

    $profits = $wpdb->get_results("SELECT * FROM wp_dropshipping_profit where profit is not null");

    foreach ($profits as $profit) {
      $profit_value = $profit->profit;
      $range_from = $profit->range_from;
      $range_to = $profit->range_to;

      $wpdb->query('start transaction');
      $wpdb->query("update wp_postmeta meta_price
                          inner join wp_postmeta meta_cost on meta_cost.post_id = meta_price.post_id          
                          set meta_price.meta_value = round(meta_cost.meta_value + meta_price.meta_value * $profit_value / 100, 2)
                          where (meta_price.meta_key = meta_price.meta_key = '_regular_price')
                          and meta_cost.meta_key = '_cost'
                          and meta_cost.meta_value >= $range_from
                          and meta_cost.meta_value <= $range_to");
      $wpdb->query('commit');
    }
  }

  public function updateStock() {
    $wpdb = $this->wpdb;

      $wpdb->query('start transaction');
      $wpdb->query("update wp_postmeta current_stock
                          inner join wp_posts post on post.ID = current_stock.post_id          
                          inner join wp_dropshipping_techdata_soft_temp dropshipping_temp on dropshipping_temp.distributor_id = post.own_migration          
                          set current_stock.meta_value = dropshipping_temp.stock
                          where current_stock.meta_key = '_stock'");
      $wpdb->query('commit');
    }





}