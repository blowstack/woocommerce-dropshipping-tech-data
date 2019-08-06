<?php

  if ($_POST['sync']) {

    $TechDataSoftware = new TechDataSoftware();
    $categories = $TechDataSoftware->getTechDataCategories('software');
    $TechDataSoftware->insertNewCategories($categories);
//    $profit_margins = $TechDataSoftware->getProfitMargins('software');
    $TechDataSoftware->importFromTechData();
    $TechDataSoftware->generatePosts();
    $TechDataSoftware->generatePostMetaSku();
    $TechDataSoftware->generatePostMetaPrice();
    $TechDataSoftware->generatePostMetaRegularPrice();
    $TechDataSoftware->generatePostMetaStock();
    $TechDataSoftware->generateWpPostMetaCost();
    $TechDataSoftware->generateWpPostMetaImage();
    $TechDataSoftware->generatePostProducerCode();
    $TechDataSoftware->generatePostCategories();

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
//        '_thumbnail_id'  => '-1',
        '_stock_status' => 'instock',
        '_product_version' => '3.5.6',
        '_edit_lock' => '1564156041:1',
        '_edit_last' => 1,
      ]
    );
    $TechDataSoftware->updatePriceByMargin();
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