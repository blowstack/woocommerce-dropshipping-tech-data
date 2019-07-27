<div class="container">
  <h2>Upload CSV</h2>

  <?php

  global $wpdb;
  global $wp_filesystem;

  if ($_POST['csv']) {

    ini_set('auto_detect_line_endings', true);

    $local_file = '../wp-content/plugins/DropShipping/upload/zip/techdata_hard.zip';
    $server_file = 'Datapack_565134_' . date('Y-m-d') . '.zip';
    $ftp_ip = 'ftp2.techdata-it-emea.com';
    $ftp_user = 'TDITuser_107';
    $ftp_password = 'TDPL@19492@74';
    $filename = "ftp://$ftp_user:$ftp_password@$ftp_ip/$server_file";

    $contents = file_get_contents($filename);

    if ($contents) {

      $myfile = fopen($local_file, "w") or die("Unable to open file!");


      if (!$wp_filesystem->put_contents($local_file, $contents, 'FS_CHMOD_FILE')) {
        echo 'error saving file!';
      }

      $zip = new ZipArchive;
      if ($zip->open('../wp-content/plugins/DropShipping/upload/zip/techdata_hard.zip') === TRUE) {
        $zip->extractTo('../wp-content/plugins/DropShipping/upload/csv/');
        $zip->close();
      } else {
        echo 'failed';
      }


      $table_name = $wpdb->prefix . "dropshipping_techdata_soft_temp";

      $file_material_path = plugins_url('/DropShipping/upload/csv/TD_Material.csv');
      $file_material = fopen($file_material_path, "r");
      $header_csv = true;

      while (($emapData = fgetcsv($file_material, 0, ";")) !== FALSE) {

        $emapData = array_map("utf8_encode", $emapData);

        if ($header_csv) {
          $header_csv = false;
          continue;
        }
        else {

          $distributor_id = $emapData[0];
          $manufacturer_id = $emapData[1];
          $brand = $emapData[2];
          $description = str_replace("'", '', $emapData[3]);
          $price = 0;
          $stock = $emapData[5];
          $category_1 = $emapData[13];
          $category_2 = $emapData[9];
          $ean = $emapData[11];
          $status = 'live';
          $dropshipping = 'hardware';

          $wpdb->query("insert ignore into $table_name(distributor_id, manufacturer_id, brand, description, price, stock, category_1, category_2, ean, status, dropshipping)
                                  values('$distributor_id', '$manufacturer_id', '$brand', '$description', '$price', '$stock', '$category_1', '$category_2', '$ean', '$status', '$dropshipping')");
        }
      }


      // aktualziacja cen
      $file_price_path = plugins_url('/DropShipping/upload/csv/TD_Prices.csv');
      $file_price = fopen($file_price_path, "r");
      $header_csv = true;

      while (($emapData = fgetcsv($file_price, 0, ";")) !== FALSE) {

        $emapData = array_map("utf8_encode", $emapData);

        if ($header_csv) {
          $header_csv = false;
          continue;
        }
        else {

          $distributor_id = $emapData[0];
          $price = str_replace(',', '.', $emapData[1]);


          $wpdb->query("update $table_name set price = $price where distributor_id = $distributor_id");
        }
      }

    }
  }



  if ($_POST['sync']) {

    global $wpdb;

    $table_name = $wpdb->prefix . "dropshipping_techdata_soft_temp";

    $count_all_software = $wpdb->get_results("select count(*) as total from $table_name where dropshipping = 'hardware'");
    $total = $count_all_software[0]->total;
    $categories = $categories = $wpdb->get_results("select term.* from wp_terms term
                                        inner join wp_term_taxonomy taxonomy on taxonomy.term_id = term.term_id
                                        where taxonomy.taxonomy = 'product_cat'");

    $images = $wpdb->get_results("select id, post_title from wp_posts where post_type = 'attachment'");


    for ($offset = 0; $offset < $total; $offset++ ) {

      $item = $wpdb->get_results("select * from $table_name where dropshipping = 'hardware' limit $offset, 1 ");

      $distributor_id = $item[0]->distributor_id;
        $manufacturer_id = $item[0]->manufacturer_id;
        $brand = $item[0]->brand;
        $description = $item[0]->description;
        $price = $item[0]->price;
        $stock = $item[0]->stock;
        $category_1 = $item[0]->category_1;
        $category_2 = $item[0]->category_2;
        $ean = $item[0]->ean;
        $status = $item[0]->status;
        $dropshipping = 'hardware';

        $is_item_exists = $wpdb->get_results("select meta.* from wp_postmeta meta inner join wp_posts post on post.id = meta.post_id
                                                  where post.post_type = 'product' and meta.meta_key = '_sku' and meta.meta_value = $distributor_id");
        if ($is_item_exists) {
          continue;
        }

        $category_id = null;
        $image_id = null;
        $profit_margin = 1;

        foreach ($categories as $category) {

          if ($category->name == $category_1) {
            $category_id = $category->term_id;
            $profit_margin = $category->profit_margin;
          }
        }


        foreach ($images as $image) {

          if ($image->post_title == $brand) {
            $image_id = $image->id;
          }
        }

        $price_for_clients = round($price + ( $price * $profit_margin / 100), 2);

        $wpdb->query('START TRANSACTION');

        $objProduct = new WC_Product();
        $objProduct->set_name($description);
        $objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
        $objProduct->set_catalog_visibility('visible'); // add the product visibility status
        $objProduct->set_description($description);
        $objProduct->set_sku($distributor_id); //can be blank in case you don't have sku, but You can't add duplicate sku's
        $objProduct->set_price($price_for_clients); // set product price
        $objProduct->set_regular_price($price_for_clients); // set product regular price
        $objProduct->set_manage_stock(true); // true or false
        $objProduct->set_stock_quantity($stock);
        $objProduct->set_stock_status('instock'); // in stock or out of stock value
        $objProduct->set_backorders('no');
        $objProduct->set_reviews_allowed(true);
        $objProduct->set_sold_individually(false);
        $objProduct->set_category_ids(array($category_id));
        $objProduct->set_virtual(true);
        if ($image_id) {
          $objProduct->set_image_id($image_id);
        }
        $objProduct->save();

        $product = wc_get_product( $objProduct->get_id());
        $product->update_meta_data( '_cost', $price  );
        $product->update_meta_data( '_brand', $brand  );
        $product->update_meta_data( '_manufacturer_id', $manufacturer_id  );
        $product->update_meta_data( '_dropshipping', $dropshipping  );
        $product->save();

       $wpdb->query('Commit');

       unset($item);
    }
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



