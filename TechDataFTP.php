<?php

/*
 * TechDataFTP
 */

abstract class TechDataFTP {

  protected $product_file_path;
  protected $csv_price_file_path;
  protected $server_file_name;
  protected $ftp_ip;
  protected $ftp_user;
  protected $ftp_password;
  protected $filename;
  protected $product_file_contents;
  protected $wp_filesystem;
  protected $dropshipping;


  public function __construct() {

    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );
    global $wp_filesystem;

    $this->setWpFilesystem($wp_filesystem);
  }

  /**
   * @return mixed
   */
  public function getproduct_file_contents() {
    return $this->product_file_contents;
  }

  /**
   * @return mixed
   */
  public function getFilename() {
    return $this->filename;
  }

  /**
   * @return mixed
   */
  public function getFtpIp() {
    return $this->ftp_ip;
  }

  /**
   * @return mixed
   */
  public function getFtpPassword() {
    return $this->ftp_password;
  }

  /**
   * @param mixed $product_file_path
   */
  public function setProductFilePath($product_file_path): void {
    $this->product_file_path = $product_file_path;
  }

  /**
   * @param mixed $csv_price_file_path
   */
  public function setCsvPriceFilePath($csv_price_file_path): void {
    $this->csv_price_file_path = $csv_price_file_path;
  }

  /**
   * @param mixed $server_file_name
   */
  public function setServerFileName($server_file_name): void {
    $this->server_file_name = $server_file_name;
  }

  /**
   * @return mixed
   */
  public function getFtpUser() {
    return $this->ftp_user;
  }

  /**
   * @return mixed
   */
  public function getProductFilePath() {
    return $this->product_file_path;
  }

  /**
   * @return mixed
   */
  public function getCsvPriceFilePath() {
    return $this->csv_price_file_path;
  }

  /**
   * @return mixed
   */
  public function getServerFileName() {
    return $this->server_file_name;
  }

  /**
   * @param mixed $product_file_contents
   */
  public function setproduct_file_contents($product_file_contents): void {
    $this->product_file_contents = $product_file_contents;
  }

  /**
   * @param mixed $filename
   */
  public function setFilename($filename): void {
    $this->filename = $filename;
  }

  /**
   * @param mixed $ftp_ip
   */
  public function setFtpIp($ftp_ip): void {
    $this->ftp_ip = $ftp_ip;
  }

  /**
   * @param mixed $ftp_password
   */
  public function setFtpPassword($ftp_password): void {
    $this->ftp_password = $ftp_password;
  }


  /**
   * @param mixed $ftp_user
   */
  public function setFtpUser($ftp_user): void {
    $this->ftp_user = $ftp_user;
  }

  /**
   * @return mixed
   */
  public function getWpFilesystem() {
    return $this->wp_filesystem;
  }

  /**
   * @return mixed
   */
  public function getDropshipping() {
    return $this->dropshipping;
  }

  /**
   * @param mixed $dropshipping
   */
  public function setDropshipping($dropshipping): void {
    $this->dropshipping = $dropshipping;
  }

  /**
   * @param mixed $wp_filesystem
   */
  public function setWpFilesystem($wp_filesystem): void {
    $this->wp_filesystem = $wp_filesystem;
  }

  protected function downloadContents() {
    $filename = $this->filename;
    $this->product_file_contents =  file_get_contents($filename);
  }

  protected function writeContentsToFile() {

    $product_file_path = $this->product_file_path;
    $product_file_contents = $this->product_file_contents;
    $wp_filesystem = $this->getWpFilesystem();

    if (!$wp_filesystem->put_contents($product_file_path, $product_file_contents, 'FS_CHMOD_FILE')) {
      echo 'error saving file!';
    }
  }


  protected function extractFiles() {
    $zip = new ZipArchive;
    $product_file_path = $this->getProductFilePath();
    if ($zip->open($product_file_path) === TRUE) {
      $zip->extractTo(DropShipping::getCsvFolderPath());
      $zip->close();
    } else {
      echo 'failed';
    }

  }

  /**
   * @param $table_name
   * @param $file_path
   * @param $termination
   * instead of using slow PHP arrays with eventually effect in memory leak
   * fast insert (but 1:1) csv file through mysql
   * trade of is three additional tables for temporary data
   */
  protected function insertRawCSVToTemporaryTables($table_name, $file_path, $termination): void {
        $WpProductRepository = new WpProductRepository();
        $WpProductRepository->insertRawCSVToTemporaryTables($table_name, $file_path, $termination);
  }

  /**
   * @param $target_table_name
   * @param $source_table_name
   */
  protected function insertMaterialsIntoDropshipping($target_table_name, $source_table_name): void {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->insertMaterialsIntoDropshipping($target_table_name, $source_table_name);
  }

  /**
   * @param $target_table_name
   * @param $source_table_name
   */
  protected function insertSoftwareIntoDropshipping($target_table_name, $source_table_name): void {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->insertSoftwareIntoDropshipping($target_table_name, $source_table_name);
  }

  /**
   * @param $target_table_name
   * @param $source_prices_table_name
   * @param $source_stock_table_name
   * @param $source_profit_table_name
   */
  protected function  updateDropshippingHardware($target_table_name, $source_prices_table_name, $source_stock_table_name): void {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->updateDropshippingHardware($target_table_name, $source_prices_table_name, $source_stock_table_name);
  }

  protected function clearTemporaryTables(array $tables_names) {
    $WpProductRepository = new WpProductRepository();
    $WpProductRepository->clearTemporaryTables($tables_names);
  }

}