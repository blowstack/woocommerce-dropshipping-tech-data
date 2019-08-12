<?php

require_once( dirname( __FILE__ ) . '/TechDataFTP.php' );

/*
 * TechDataSoftware
 */
class TechDataFTPSoftware extends TechDataFTP {

  protected $local_file_path;
  protected $server_file_name;
  protected $ftp_ip;
  protected $ftp_user;
  protected $ftp_password;
  protected $filename;
  protected $CSVcontents;
  protected $table_name;
  protected $wp_filesystem;
  protected $wpdb;

  public function __construct($local_file_path, $server_file_name, $ftp_ip, $ftp_user, $ftp_password ) {
    global $wp_filesystem;
    global $wpdb;
    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );

    $this->setLocalFilePath($local_file_path);
    $this->setServerFileName($server_file_name);
    $this->setFtpIp($ftp_ip);
    $this->setFtpUser($ftp_user);
    $this->setFtpPassword($ftp_password);
    $this->setFilename("ftp://$this->ftp_user:$this->ftp_password@$this->ftp_ip/$this->server_file_name");

    $this->table_name = $wpdb->prefix . "dropshipping_techdata_soft_temp";
    $this->wp_filesystem = $wp_filesystem;
    $this->wpdb = $wpdb;
  }

  protected function downloadContents() {
    $filename = $this->filename;
    $this->CSVcontents =  file_get_contents($filename);
  }

  protected function writeContentsToFile() {

    $local_file_path = $this->local_file_path;
    $CSVcontents = $this->CSVcontents;
    $wp_filesystem = $this->wp_filesystem;

    //    utf8_encode($CSVcontents);

    if (!$wp_filesystem->put_contents($local_file_path, $CSVcontents, 'FS_CHMOD_FILE')) {
        echo 'error saving file!';
      }
  }

  protected function saveContentsToDB() {

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

}