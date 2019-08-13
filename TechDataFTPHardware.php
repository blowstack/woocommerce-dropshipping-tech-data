<?php

require_once( dirname( __FILE__ ) . '/TechDataFTP.php' );
require_once( dirname( __FILE__ ) . '/TechDataFTPInterface.php' );

/*
 * TechDataSoftware
 */
class TechDataFTPHardware extends TechDataFTP implements TechDataFTPInterface {


  public function __construct($local_file_path, $server_file_name, $ftp_ip, $ftp_user, $ftp_password) {
//  public function __construct($local_file_path, $server_file_name, $ftp_ip, $ftp_user, $ftp_password, $file_material_path, $file_price_path ) {
    parent::__construct();

    $this->setDropshipping("hardware");
    $this->setLocalFilePath($local_file_path);
    $this->setServerFileName($server_file_name);
    $this->setFtpIp($ftp_ip);
    $this->setFtpUser($ftp_user);
    $this->setFtpPassword($ftp_password);
    $this->setFilename("ftp://$this->ftp_user:$this->ftp_password@$this->ftp_ip/$this->server_file_name");
    $this->setCSVindexes(0,1,2,3,'',5,13, 9, 11, '');
  }


   public function importFromTechData(): void {

    $this->downloadContents();
    $this->writeContentsToFile();
    $this->extractFiles();
    $CSVIndexes = $this->getCSVIndexes();
    $this->saveContentsToDB(";", $CSVIndexes, plugins_url('/DropShipping/upload/csv/TD_Material.csv'));
    $this->saveContentPriceToDB(plugins_url('/DropShipping/upload/csv/TD_Prices.csv'));
   }
}