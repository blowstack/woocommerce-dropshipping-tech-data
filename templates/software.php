<?php

  if ($_POST['sync']) {

    $TechDataFTPSoftware = new TechDataFTPSoftware(
      '../wp-content/plugins/DropShipping/upload/csv/techdata_soft.csv',
      '0000565134.csv',
      '62.225.34.76', 'ESD565134', '9sWQvaP0');
    $TechDataProductGenerator = new TechDataProductGenerator();
    $categories = $TechDataProductGenerator->getTechDataCategories('software');
    $TechDataProductGenerator->insertNewCategories($categories);
    $TechDataFTPSoftware->importFromTechData();
    $TechDataProductGenerator->generatePosts('software');
    $TechDataProductGenerator->generatePostMetaSku();
    $TechDataProductGenerator->generatePostMetaManufacturer();
    $TechDataProductGenerator->generatePostMetaPrice();
    $TechDataProductGenerator->generatePostMetaRegularPrice();
    $TechDataProductGenerator->generatePostMetaStock();
    $TechDataProductGenerator->generateWpPostMetaCost();
    $TechDataProductGenerator->generateWpPostMetaImage();
    $TechDataProductGenerator->generatePostMetaProducerCode();
    $TechDataProductGenerator->generatePostMetaBrand();
    $TechDataProductGenerator->generatePostMetaDropShipping('software');
    $TechDataProductGenerator->generatePostCategories();

    // price, sku, regular_price, stock generated separately
    $TechDataProductGenerator->generateWpPostMetasBasic(
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
        '_low_stock_amount' => 1,
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
        '_stock_status' => 'instock',
        '_product_version' => '3.5.6',
        '_edit_lock' => '1564156041:1',
        '_edit_last' => 1,
      ]
    );
    $TechDataProductGenerator->updatePriceByMarginAndCost();
  }
  ?>

<div class="container">
    <h2>Synchronizacja Software</h2>
    <form method="post">
    <input type="submit" name="sync" value="Ręczna synchronizacja">
    <?php
    if ($_POST['sync']) {
      echo 'ok';
    }
    ?>
  </form>
</div>