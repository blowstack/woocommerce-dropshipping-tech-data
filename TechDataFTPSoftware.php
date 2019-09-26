<?php

require_once( dirname( __FILE__ ) . '/TechDataFTP.php' );
require_once( dirname( __FILE__ ) . '/TechDataFTPInterface.php' );

/*
 * TechDataFTPSoftware
 */
class TechDataFTPSoftware extends TechDataFTP implements TechDataFTPInterface {

  /**
   * TechDataFTPSoftware constructor.
   * @param $product_file_path
   * @param $server_file_name
   * @param $ftp_ip
   * @param $ftp_user
   * @param $ftp_password
   */
  public function __construct($product_file_path, $server_file_name, $ftp_ip, $ftp_user, $ftp_password ) {
    parent::__construct();

    $this->setDropshipping(DropShipping::$type_software);
    $this->setProductFilePath(DropShipping::getCsvFolderPath() . $product_file_path);
    $this->setServerFileName($server_file_name);
    $this->setFtpIp($ftp_ip);
    $this->setFtpUser($ftp_user);
    $this->setFtpPassword($ftp_password);
    $this->setFilename("ftp://$this->ftp_user:$this->ftp_password@$this->ftp_ip/$this->server_file_name");
  }

   public function importFromTechData(): void {

    $this->downloadContents();
    $this->writeContentsToFile();

    $csv_file_path = $this->getProductFilePath();
    $table_name_product = TablesRepository::getTableNameProduct();
    $table_name_temporary_software = TablesRepository::getTableNameTempSoft();

    $this->insertRawCSVToTemporaryTables($table_name_temporary_software, $csv_file_path,"'\t'");
    $this->insertSoftwareIntoDropshipping($table_name_product, $table_name_temporary_software);

    $this->clearTemporaryTables([ $table_name_temporary_software ]);
   }
}