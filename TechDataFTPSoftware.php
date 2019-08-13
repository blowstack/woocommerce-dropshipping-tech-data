<?php

require_once( dirname( __FILE__ ) . '/TechDataFTP.php' );

/*
 * TechDataSoftware
 */
class TechDataFTPSoftware extends TechDataFTP {


  public function __construct($local_file_path, $server_file_name, $ftp_ip, $ftp_user, $ftp_password ) {
    parent::__construct();
    require_once( dirname( __FILE__ ) . '/repos/WpProductRepository.php' );

    $this->setLocalFilePath($local_file_path);
    $this->setServerFileName($server_file_name);
    $this->setFtpIp($ftp_ip);
    $this->setFtpUser($ftp_user);
    $this->setFtpPassword($ftp_password);
    $this->setFilename("ftp://$this->ftp_user:$this->ftp_password@$this->ftp_ip/$this->server_file_name");
  }


  protected function saveContentsToDB($dropshipping) {

    $local_file_path = $this->getLocalFilePath();
    $header = true;
    $file = fopen($local_file_path, "r");
    $table_name = $this->table_name;



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
    $this->saveContentsToDB("software");
   }

}