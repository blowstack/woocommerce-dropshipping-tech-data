<?php

/*
 * TechDataSoftware
 */
class TechDataSoftware {

  public function __construct( ) {
    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );
  }

   public function generatePosts() {
     $WpProductRepository = new WpProductRepository();
     $WpProductRepository->generateWpPosts();
   }

   public function generateWpPostMetasBasic(array $post_metas) {
     $WpProductRepository = new WpProductRepository();
     $WpProductRepository->generateWpPostMetasBasic($post_metas);
   }

   public function generatePostMetaSku() {
     $WpProductRepository = new WpProductRepository();
     $WpProductRepository->generateWpPostMetaSku();
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

  public function generatePostMetaDropShipping($type) {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->generateWpPostMetaDropShipping($type);
  }

  public function getTechDataCategories(string $dropshipping): array {
    $WpProductRepository = new WpProductRepository();
    $categories = $WpProductRepository->getTechDataCategories($dropshipping);
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

  public function updatePriceByMargin() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->updatePriceByMargin();
  }

  public function updatePriceByMarginAndCost() {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->updatePriceByMarginAndCost();
  }
}