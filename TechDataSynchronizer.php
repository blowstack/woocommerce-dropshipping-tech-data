<?php

/*
 * TechDataFTP
 */

class TechDataSynchronizer {

  private $FTP;
  private $ProductGenerator;
  private $dropshipping;

  /**
   * TechDataSynchronizer constructor.
   * @param $dropshipping_type
   */
  public function __construct($dropshipping) {

    require_once( dirname( __FILE__ ) . '/TechDataFTPHardware.php' );
    require_once( dirname( __FILE__ ) . '/TechDataFTPSoftware.php' );
    require_once( dirname( __FILE__ ) . '/TechDataProductGenerator.php' );

    $ConfigManager = new TechDataConfigManager();
    $Config = $ConfigManager->getConfg($dropshipping);
    $this->dropshipping = $dropshipping;

    switch ($dropshipping) {
      case DropShipping::$type_software:

        $this->FTP = new TechDataFTPSoftware(
          "techdata_soft.csv",
          "$Config->file_name",
          "$Config->server_ip",
          "$Config->user",
          "$Config->password"
        );
        break;

      case DropShipping::$type_hardware:
        $this->FTP = new TechDataFTPHardware(
          'techdata_hard.zip',
          "$Config->file_name",
          "$Config->server_ip",
          "$Config->user",
          "$Config->password"
        );
        break;
    }

    $this->ProductGenerator = new TechDataProductGenerator($dropshipping);
  }


  /**
   * import data from TechData through FTP
   * insert data to WP
   * update product data
   */
  public function syncAllFromTechData() {

    $FTP = $this->FTP;
    $TechDataProductGenerator = $this->ProductGenerator;
    $categories = $TechDataProductGenerator->getTechDataCategories();
    $TechDataProductGenerator->insertNewCategories($categories);
    $FTP->importFromTechData();
    $TechDataProductGenerator->generatePosts();
    $TechDataProductGenerator->generatePostMetaSku();
    $TechDataProductGenerator->generatePostMetaManufacturer();
    $TechDataProductGenerator->generatePostMetaPrice();
    $TechDataProductGenerator->generatePostMetaRegularPrice();
    $TechDataProductGenerator->generatePostMetaStock();
    $TechDataProductGenerator->generatePostMetaEan();
    $TechDataProductGenerator->generateWpPostMetaCost();
    $TechDataProductGenerator->generateWpPostMetaImage();
    $TechDataProductGenerator->generatePostMetaProducerCode();
    $TechDataProductGenerator->generatePostMetaBrand();
    $TechDataProductGenerator->generatePostMetaDropShipping();
    $TechDataProductGenerator->generatePostCategories();

    $dropshipping = $this->dropshipping;
    $virtual = $dropshipping == DropShipping::$type_software ? 1 : 0;


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
        '_virtual' => $virtual,
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
    $TechDataProductGenerator->updatePrice();
    $TechDataProductGenerator->updateStock();
  }
}