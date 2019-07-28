<div class="container">
  <h2>Upload CSV</h2>

  <?php

  global $wpdb;
  global $wp_filesystem;

  if ($_POST['csv']) {

    $TechDataSoftware = new TechDataSoftware();
    $TechDataSoftware->importFromTechData();
  }

  if ($_POST['sync']) {

    $TechDataSoftware = new TechDataSoftware();
    $TechDataSoftware->generatePosts();
    $TechDataSoftware->generatePostMetaSku();
    $TechDataSoftware->generatePostMetaPrice();
    $TechDataSoftware->generatePostMetaRegularPrice();
    $TechDataSoftware->generatePostMetaStock();

    // price, sku, regular_price, stock generated separately
    $TechDataSoftware->generateWpPostMetasBasic(
      [
        '_vs_post_settings' => 'a:1:{s:10:"vc_grid_id";a:0:{}}',
        '_wc_review_count' => 0,
        '_wc_rating_count' => 'a:0:{}',
        '_wc_average_rating' => 0,
        '_sale_price' => '',
        '_sale_price_dates_from' => '',
        '_sale_price_dates_to' => '',
        'total_sales' => '',
        '_tax_status' => 'taxable',
        '_tax_class' => '',
        '_manage_stock' => 'yes',
        '_backorders' => 'no',
        '_low_stock_amount' => 2,
        '_sold_individually' => 'no',
        '_weight' => '',
        '_length' => '',
        '_width' => '',
        '_height' => '',
        '_upsell_ids' => 'a:0:{}',
        '_crosssell_ids' => 'a:0:{}',
        '_purchase_note' => '',
        '_default_attributes' => 'a:0:{}',
        '_virtual' => 1,
        '_downloadable' => 'no',
        '_product_image_gallery' => '',
        '_download_limit' => '-1',
        '_download_expiry' => '-1',
        '_thumbnail_id'  => '-1',
        '_stock_status' => 'instock',
        '_product_version' => '3.5.6',
        '_edit_lock' => '1564156041:1',
        '_edit_last' => 1,
      ]
    );


//        '_stock' => pt.stock,
    //    global $wpdb;
//
//    $table_name = $wpdb->prefix . "dropshipping_techdata_soft_temp";
//
//    $count_all_software = $wpdb->get_results("select count(*) as total from $table_name where dropshipping = 'software'");
//    $total = $count_all_software[0]->total;
//
//    $categories = $categories = $wpdb->get_results("select term.* from wp_terms term
//                                        inner join wp_term_taxonomy taxonomy on taxonomy.term_id = term.term_id
//                                        where taxonomy.taxonomy = 'product_cat'");
//
//    $images = $wpdb->get_results("select id, post_title from wp_posts where post_type = 'attachment'");
//
//
//
//    for ($offset = 0; $offset < $total; $offset++ ) {
//
//      $item = $wpdb->get_results("select * from $table_name where dropshipping = 'software' limit $offset, 1    ");
//      $distributor_id = $item[0]->distributor_id;
//      $stock = $item[0]->stock;
//      $category_1 = $item[0]->category_1;
//      $dropshipping = 'software';
//
//      $is_item_exists = $wpdb->get_results("select meta.* from wp_postmeta meta inner join wp_posts post on post.id = meta.post_id
//                                                  where post.post_type = 'product' and meta.meta_key = '_sku' and meta.meta_value = $distributor_id");
//      if ($is_item_exists) {
//        continue;
//      }
//
//
//      $category_id = null;
//      $image_id = null;
//      $profit_margin = 1;
//
//      foreach ($categories as $category) {
//
//        if ($category->name == $item[0]->category_1) {
//          $category_id = $category->term_id;
//          $profit_margin = $category->profit_margin;
//        }
//      }
//
//
//      foreach ($images as $image) {
//
//        if ($image->post_title == $item[0]->brand) {
//          $image_id = $image->id;
//        }
//      }
//
//      $price_for_clients = round( $item[0]->price + (  $item[0]->price * $profit_margin / 100), 2);
//
//      $wpdb->query('START TRANSACTION');
//
//      $objProduct = new WC_Product();
//      $objProduct->set_name($item[0]->description);
//      $objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
//      $objProduct->set_catalog_visibility('visible'); // add the product visibility status
//      $objProduct->set_description($item[0]->description);
//      $objProduct->set_sku($distributor_id); //can be blank in case you don't have sku, but You can't add duplicate sku's
//      $objProduct->set_price($price_for_clients); // set product price
//      $objProduct->set_regular_price($price_for_clients); // set product regular price
//      $objProduct->set_manage_stock(true); // true or false
//      $objProduct->set_stock_quantity($item[0]->stock);
//      $objProduct->set_stock_status('instock'); // in stock or out of stock value
//      $objProduct->set_backorders('no');
//      $objProduct->set_reviews_allowed(true);
//      $objProduct->set_sold_individually(false);
//      $objProduct->set_category_ids(array($category_id));
//      $objProduct->set_virtual(true);
//      if ($image_id) {
//        $objProduct->set_image_id($image_id);
//      }
//      $objProduct->save();
//
//      $product = wc_get_product( $objProduct->get_id());
//      $product->update_meta_data( '_cost',  $item[0]->price  );
//      $product->update_meta_data( '_brand', $item[0]->brand  );
//      $product->update_meta_data( '_manufacturer_id',  $item[0]->manufacturer_id  );
//      $product->update_meta_data( '_dropshipping', $dropshipping  );
//      $product->save();
//
//      $wpdb->query('Commit');
//
//      unset($item);
//      unset($distributor_id);
//      unset($stock);
//      unset($category_1);
//      unset($dropshipping);
//    }
  }

  ?>

</div>

<div class="container">

  <form method="post">
    <input type="submit" name="csv" value="Pobierz dane z Techdaty">
    <?php
    if ($_POST['csv']) {
      echo 'ok';
    }
    ?>
    <br>
    <br>
    <input type="submit" name="sync" value="Dodaj nowe produkty">
    <?php
    if ($_POST['sync']) {
      echo 'ok';
    }
    ?>
  </form>

</div>


