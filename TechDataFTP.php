<?php

/*
 * TechDataFTP
 */

abstract class TechDataFTP {

  protected $local_file_path;
  protected $server_file_name;
  protected $ftp_ip;
  protected $ftp_user;
  protected $ftp_password;
  protected $filename;
  protected $CSVcontents;
  protected $CSVIndexes;
  protected $table_name;
  protected $wp_filesystem;
  protected $dropshipping;


  public function __construct() {

    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );

    global $wpdb;
    global $wp_filesystem;

    $this->setTableName($wpdb->prefix . "dropshipping_techdata_soft_temp");
    $this->setWpFilesystem($wp_filesystem);
  }

  /**
   * @return mixed
   */
  public function getCSVcontents() {
    return $this->CSVcontents;
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
   * @param mixed $local_file_path
   */
  public function setLocalFilePath($local_file_path): void {
    $this->local_file_path = $local_file_path;
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
  public function getLocalFilePath() {
    return $this->local_file_path;
  }

  /**
   * @return mixed
   */
  public function getServerFileName() {
    return $this->server_file_name;
  }

  /**
   * @param mixed $CSVcontents
   */
  public function setCSVcontents($CSVcontents): void {
    $this->CSVcontents = $CSVcontents;
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
   * @return string
   */
  public function getTableName(): string {
    return $this->table_name;
  }

  /**
   * @param string $table_name
   */
  public function setTableName(string $table_name): void {
    $this->table_name = $table_name;
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
    $this->CSVcontents =  file_get_contents($filename);
  }

  protected function writeContentsToFile() {

    $local_file_path = $this->local_file_path;
    $CSVcontents = $this->CSVcontents;
    $wp_filesystem = $this->getWpFilesystem();

    if (!$wp_filesystem->put_contents($local_file_path, $CSVcontents, 'FS_CHMOD_FILE')) {
      echo 'error saving file!';
    }
  }

  /**
   * @param $distributor
   * @param $manufacturer
   * @param $brand
   * @param $description
   * @param $price
   * @param $stock
   * @param $category_1
   * @param $category_2
   * @param $ean
   * @param $status
   * @param $dropshipping
   * @return array
   */
  public function setCSVIndexes($distributor, $manufacturer, $brand, $description, $price, $stock, $category_1, $category_2, $ean, $status, $dropshipping) {

    $csv_indexes = [
      'distributor_id' => $distributor,
      'manufacturer_id' => $manufacturer,
      'brand' => $brand,
      'description' => $description,
      'price' => $price,
      'stock' => $stock,
      'category_1' => $category_1,
      'category_2' => $category_2,
      'ean' => $ean,
      'status' => $status,
      'dropshipping' => $dropshipping,
    ];
    $this->CSVIndexes = $csv_indexes;
  }

  /**
   * @return mixed
   */
  public function getCSVIndexes() {
    return $this->CSVIndexes;
  }

  /**
   * @param $CSVIndexes
   */
  protected function saveContentsToDB($CSVIndexes): void {

    $local_file_path = $this->getLocalFilePath();
    $header = true;
    $file = fopen($local_file_path, "r");
    $table_name = $this->getTableName();
    $dropshipping = $this->getDropshipping();

    while (($emapData = fgetcsv($file, 0, "\t")) !== FALSE) {

      $emapData = array_map("utf8_encode", $emapData);

      // data header not included
      if ($header) {
        $header = false;
        continue;
      }
      else {

        $distributor_id = $emapData[$CSVIndexes['distributor_id']];
        $manufacturer_id = $emapData[$CSVIndexes['manufacturer_id']];
        $brand = $emapData[$CSVIndexes['brand']];
        $description = $emapData[$CSVIndexes['description']];
        $price = $emapData[$CSVIndexes['price']];
        $stock = $emapData[$CSVIndexes['stock']];
        $category_1 = $emapData[$CSVIndexes['category_1']];
        $category_2 = $emapData[$CSVIndexes['category_2']];
        $ean = $emapData[$CSVIndexes['ean']];
        $status = $emapData[$CSVIndexes['status']];

        $WpProductRepository = new WpProductRepository();
        $WpProductRepository->insertFromCSV($table_name, $distributor_id, $manufacturer_id, $brand, $description, $price, $stock, $category_1, $category_2, $ean, $status, $dropshipping);
      }
    }
  }

}