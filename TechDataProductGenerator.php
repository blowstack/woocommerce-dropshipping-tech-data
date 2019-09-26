<?php

/*
 * TechDataProductGenerator
 */
class TechDataProductGenerator {

  private $dropshipping_type;
  private $WpProductRepository;

  /**
   * TechDataProductGenerator constructor.
   * @param $dropshipping_type
   */
  public function __construct($dropshipping_type) {
    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );
    $this->dropshipping_type = $dropshipping_type;
    $this->WpProductRepository = new WpProductRepository();
  }

  public function generatePosts() {
    $dropshipping_type = $this->dropshipping_type;

    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPosts($dropshipping_type);
  }

  public function generateWpPostMetasBasic(array $post_metas) {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetasBasic($post_metas);
  }

  public function generatePostMetaSku() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaSku();
  }

  public function generatePostMetaManufacturer() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaManufacturer();
  }

  public function generatePostMetaPrice() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaPrice();
  }

  public function generatePostMetaRegularPrice() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaRegularPrice();
  }

  public function generatePostMetaStock() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaStock();
  }

  public function generatePostMetaProducerCode() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaProducerCode();
  }

  public function generatePostMetaBrand() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaBrand();
  }

  public function generatePostMetaDropShipping() {
    $dropshipping_type = $this->dropshipping_type;

    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaDropShipping($dropshipping_type);
  }

  public function getTechDataCategories(): array {
    $dropshipping_type = $this->dropshipping_type;

    $WpProductRepository = $this->WpProductRepository;
    $categories = $WpProductRepository->getTechDataCategories($dropshipping_type);
    return $categories;
  }

  public function insertNewCategories($categories) {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->insertNewCategories($categories);
  }

  public function generatePostCategories() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateProductType();
    $WpProductRepository->generateProductLang();
    $WpProductRepository->generateProductCategory();
  }

  public function generateWpPostMetaCost() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaCost();
  }

  public function generateWpPostMetaImage() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->generateWpPostMetaImage();
  }

  public function updatePrice() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->updatePrice();
  }

  public function updateStock() {
    $WpProductRepository = $this->WpProductRepository;
    $WpProductRepository->updateStock();
  }

  /**
   * @param $table_name
   * @return array|object|null
   */
  public function getForCSV(): array {
    $WpProductRepository = $this->WpProductRepository;
    $products = $WpProductRepository->getForCSV();
    return $products;
  }

}