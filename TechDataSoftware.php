<?php

/*
 * TechDataSoftware
 */

class TechDataSoftware {

  private $local_file_path = '../wp-content/plugins/DropShipping/upload/csv/techdata_soft.csv';
  private $server_file_name = '0000565134.csv';
  private $ftp_ip = '62.225.34.76';
  private $ftp_user = 'ESD565134';
  private $ftp_password = '9sWQvaP0';
  private $filename;
  private $CSVcontents;
  private $table_name;
  private $wp_filesystem;
  private $wpdb;

  public function __construct() {
    global $wp_filesystem;
    global $wpdb;
    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );

    $this->filename = "ftp://$this->ftp_user:$this->ftp_password@$this->ftp_ip/$this->server_file_name";
    $this->table_name = $wpdb->prefix . "dropshipping_techdata_soft_temp";
    $this->wp_filesystem = $wp_filesystem;
    $this->wpdb = $wpdb;
  }

  private function downloadContents() {
    $filename = $this->filename;
    $this->CSVcontents =  file_get_contents($filename);
  }

  private function writeContentsToFile() {

    $local_file_path = $this->local_file_path;
    $CSVcontents = $this->CSVcontents;
    $wp_filesystem = $this->wp_filesystem;

    //    utf8_encode($CSVcontents);

    if (!$wp_filesystem->put_contents($local_file_path, $CSVcontents, 'FS_CHMOD_FILE')) {
        echo 'error saving file!';
      }
  }

  private function saveContentsToDB() {

    $local_file_path = $this->local_file_path;
    $header = true;
    $file = fopen($local_file_path, "r");
    $table_name = $this->table_name;
    $dropshipping = "software";


    while (($emapData = fgetcsv($file, 0, "\t")) !== FALSE) {

    $emapData = array_map("utf8_encode", $emapData);

      // data header not included
      if ($header) {
        $header = false;
        continue;
      }
      else {

        $distributor_id = $emapData[0];
        $manufacturer_id = $emapData[1];
        $brand = $emapData[2];
        $description = $emapData[3];
        $price = $emapData[4];
        $stock = $emapData[5];
        $category_1 = $emapData[6];
        $category_2 = $emapData[7];
        $ean = $emapData[8];
        $status = $emapData[9];

        $WpProductRepository = new WpProductRepository();
        $WpProductRepository->insertFromCSV($table_name, $distributor_id, $manufacturer_id, $brand, $description, $price, $stock, $category_1, $category_2, $ean, $status, $dropshipping);
      }
    }
  }

   public function importFromTechData() {

    $this->downloadContents();
    $this->writeContentsToFile();
    $this->saveContentsToDB();
   }

   public function generateWpProducts() {
     $WpProductRepository = new WpProductRepository();
     $WpProductRepository->generateWpProducts();

   }

}