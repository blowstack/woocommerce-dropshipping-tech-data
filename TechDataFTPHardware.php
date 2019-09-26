<?php

require_once( dirname( __FILE__ ) . '/TechDataFTP.php' );
require_once( dirname( __FILE__ ) . '/TechDataFTPInterface.php' );

/*
 * TechDataFTPHardware
 */
class TechDataFTPHardware extends TechDataFTP implements TechDataFTPInterface {

  const MATERIAL_CSV = 'TD_Material.csv';
  const PRICES_CSV = 'TD_Prices.csv';

  /**
   * TechDataFTPHardware constructor.
   * @param $product_file_path
   * @param $server_file_name
   * @param $ftp_ip
   * @param $ftp_user
   * @param $ftp_password
   */
  public function __construct($product_file_path, $server_file_name, $ftp_ip, $ftp_user, $ftp_password) {
    parent::__construct();

    $this->setDropshipping(DropShipping::$type_hardware);
    $this->setProductFilePath($product_file_path);
    $this->setServerFileName($server_file_name);
    $this->setFtpIp($ftp_ip);
    $this->setFtpUser($ftp_user);
    $this->setFtpPassword($ftp_password);
    $this->setFilename("ftp://$this->ftp_user:$this->ftp_password@$this->ftp_ip/$this->server_file_name");
  }


   public function importFromTechData(): void {

    $this->downloadContents();
    $this->writeContentsToFile();
    $this->extractFiles();

    $material_csv_path = DropShipping::getCsvFolderPath() . self::MATERIAL_CSV;
    $prices_csv_path = DropShipping::getCsvFolderPath() . self::PRICES_CSV;

    $table_name_product = TablesRepository::getTableNameProduct();
    $table_name_temporary_hardware_material = TablesRepository::getTableNameTempHardMaterial();
    $table_name_temporary_hardware_prices = TablesRepository::getTableNameTempHardPrices();

    $this->insertRawCSVToTemporaryTables($table_name_temporary_hardware_material, $material_csv_path,"';'" );
    $this->insertMaterialsIntoDropshipping($table_name_product, $table_name_temporary_hardware_material);

    $this->insertRawCSVToTemporaryTables($table_name_temporary_hardware_prices, $prices_csv_path, "';'");
    $this->updateDropshippingHardware($table_name_product,$table_name_temporary_hardware_prices,$table_name_temporary_hardware_material);
    $this->clearTemporaryTables([ $table_name_temporary_hardware_material, $table_name_temporary_hardware_prices ]);
   }
}