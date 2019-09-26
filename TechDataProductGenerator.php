<?php

/*
 * TechDataProductGenerator
 */
class TechDataProductGenerator {

  private $dropshipping_type;

  public function __construct($dropshipping_type) {
    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );
    $this->dropshipping_type = $dropshipping_type;
  }

  public function generatePosts() {
    $dropshipping_type = $this->dropshipping_type;

    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPosts($dropshipping_type);
  }

  public function generateWpPostMetasBasic(array $post_metas) {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetasBasic($post_metas);
  }

  public function generatePostMetaSku() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaSku();
  }

  public function generatePostMetaManufacturer() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaManufacturer();
  }

  public function generatePostMetaPrice() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaPrice();
  }

  public function generatePostMetaRegularPrice() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaRegularPrice();
  }

  public function generatePostMetaStock() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaStock();
  }

  public function generatePostMetaProducerCode() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaProducerCode();
  }

  public function generatePostMetaBrand() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaBrand();
  }

  public function generatePostMetaDropShipping() {
    $dropshipping_type = $this->dropshipping_type;

    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaDropShipping($dropshipping_type);
  }

  public function getTechDataCategories(): array {
    $dropshipping_type = $this->dropshipping_type;

    $WpProductRepository = new WpProductRepository();
    $categories = $WpProductRepository->getTechDataCategories($dropshipping_type);
    return $categories;
  }

  public function insertNewCategories($categories) {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->insertNewCategories($categories);
  }

  public function generatePostCategories() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateProductType();
    $WpProductRepository->generateProductLang();
    $WpProductRepository->generateProductCategory();
  }

  public function generateWpPostMetaCost() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaCost();
  }

  public function generateWpPostMetaImage() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaImage();
  }

  public function updatePrice() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->updatePrice();
  }

  public function updateStock() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->updateStock();
  }

  /**
   * @param $table_name
   * @return array|object|null
   */
  public function getForCSV(): array {
    $WpProductRepository = new WpProductRepository();
    $products = $WpProductRepository->getForCSV();
    return $products;
  }

}