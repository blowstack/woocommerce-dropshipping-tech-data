<?php

require_once( dirname( __FILE__ ) . '/TechDataFTP.php' );
require_once( dirname( __FILE__ ) . '/TechDataFTPInterface.php' );

/*
 * TechDataSoftware
 */
class TechDataFTPSoftware extends TechDataFTP implements TechDataFTPInterface {


  public function __construct($local_file_path, $server_file_name, $ftp_ip, $ftp_user, $ftp_password ) {
    parent::__construct();

    $this->setDropshipping("software");
    $this->setLocalFilePath($local_file_path);
    $this->setServerFileName($server_file_name);
    $this->setFtpIp($ftp_ip);
    $this->setFtpUser($ftp_user);
    $this->setFtpPassword($ftp_password);
    $this->setFilename("ftp://$this->ftp_user:$this->ftp_password@$this->ftp_ip/$this->server_file_name");
    $this->setCSVindexes(0,1,2,3,4,5,6, 7, 8, 9);
  }


   public function importFromTechData(): void {

    $this->downloadContents();
    $this->writeContentsToFile();
    $CSVIndexes = $this->getCSVIndexes();
    $csv_file_path = $this->getLocalFilePath();
    $this->saveContentsToDB("\t", $CSVIndexes, $csv_file_path);
   }
}