<?php

/**
 * Class TablesRepository
 */
class TablesRepository {

  static $table_name_product = "techdata_soft_temp";
  static $table_name_profit = "profit";
  static $table_name_temp_hard_materials= "temporary_hard_materials";
  static $table_name_temp_hard_prices = "temporary_hard_prices";
  static $table_name_temp_soft = "temporary_soft";
  static $table_name_ftp_config = "ftp_config";
  static $table_name_wp_posts = "posts";
  private $charset_collate;

  public function __construct() {

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    $this->charset_collate = $wpdb->get_charset_collate();
  }


  /**
   * @return string
   */
  public static function getTableNameProduct(): string {

    $prefixes = DropShipping::getTablePrefixes();
    return $prefixes. self::$table_name_product;
  }

  /**
   * @return string
   */
  public static function getTableNameProfit(): string {

    $prefixes = DropShipping::getTablePrefixes();
    return $prefixes. self::$table_name_profit;
  }

  /**
   * @return string
   */
  public static function getTableNameTempHardMaterial(): string {

    $prefixes = DropShipping::getTablePrefixes();
    return $prefixes. self::$table_name_temp_hard_materials;
  }

  /**
   * @return string
   */
  public static function getTableNameTempHardPrices(): string {

    $prefixes = DropShipping::getTablePrefixes();
    return $prefixes. self::$table_name_temp_hard_prices;
  }

  /**
   * @return string
   */
  public static function getTableNameTempSoft(): string {

    $prefixes = DropShipping::getTablePrefixes();
    return $prefixes. self::$table_name_temp_soft;
  }

  /**
   * @return string
   */
  public static function getTableNameFtpConfig(): string {

    $prefixes = DropShipping::getTablePrefixes();
    return $prefixes. self::$table_name_ftp_config;
  }


  /**
   * @return string
   */
  public static function getTableNameWpPosts(): string {
    global $wpdb;

    $prefix = $wpdb->prefix;
    return $prefix. self::$table_name_wp_posts;
  }


  /**
   * @param $table_name
   * @param $charset_collate
   * @return string
   */
  private function getProductTableSQL(): string {

    $table_name = $this->getTableNameProduct();
    $charset_collate = $this->charset_collate;

    $sql = "CREATE TABLE $table_name (
                    distributor_id int(11) NOT NULL,
                    manufacturer_id varchar(25) NULL,
                    brand varchar(1000) NULL,
                    description text NULL,
                    price decimal(8,2),
                    stock varchar(100),
                    category_1 varchar(105) NULL,
                    category_2 varchar(105) NULL,
                    ean varchar(130) NULL,
                    status varchar(150) NOT NULL,
                    dropshipping varchar(100) NULL,
                    PRIMARY KEY  (distributor_id)
                    )
                     $charset_collate";

    return $sql;
  }

  /**
   * @param $table_name
   * @param $charset_collate
   * @return string
   */
  private function getProfitTableSQL(): string {

    $table_name = $this->getTableNameProfit();
    $charset_collate = $this->charset_collate;

    $sql = "CREATE TABLE $table_name (
                    id int(11) auto_increment,
                    range_from decimal(8,2) NOT NULL,
                    range_to decimal(8,2) NOT NULL,
                    profit float NOT NULL,
                    PRIMARY KEY (id)
                    )
                     $charset_collate";

    return $sql;
  }

  /**
   * @param $table_name
   * @param $charset_collate
   * @return string
   */
  private function getTemporaryHardwareMaterialsTableSQL(): string {

    $table_name = $this->getTableNameTempHardMaterial();
    $charset_collate = $this->charset_collate;

    $sql = "CREATE TABLE $table_name (
                     SapNo varchar(255) not null,
                     PartNo varchar(255) null,
                     Vendor varchar(255) null,
                     Nazwa varchar(255) null,
                     Czas varchar(255) null,
                     Magazyn int(11) null,
                     DataDostawy varchar(255) null,
                     EC varchar(5) null,
                     URL varchar(255) null,
                     Rodzina varchar(255) null,
                     WagaBrutto varchar(10) null,
                     EAN varchar(20) null,
                     FamilyPr_kod varchar(255) null,
                     FamilyPr_PL varchar(255) null,
                     KlasaPr_kod varchar(255) null,
                     KlasaPr_PL varchar(255) null,
                     PodklasaPr_kod varchar(255) null,
                     PodklasaPr_PL varchar(255) null,
                     NazwaEC text null,
                     KIT varchar(255) null,
                     Wielkogabaryt varchar(255) null,
                     Sredniogabaryt varchar(255) null,
                     PRIMARY KEY (SapNo)
                     )
                     $charset_collate";

    return $sql;
  }

  /**
   * @param $table_name
   * @param $charset_collate
   * @return string
   */
  private function getTemporaryHardwarePricesTableSQL(): string {

    $table_name = $this->getTableNameTempHardPrices();
    $charset_collate = $this->charset_collate;

    $sql = "CREATE TABLE $table_name (
                     SapNo varchar(255) not null,
                     Cena_w_TD varchar(255) null,
                     Waluta varchar(10) null,
                     PRIMARY KEY (SapNo)
                     )
                     $charset_collate";

    return $sql;
  }

  /**
   * @param $table_name
   * @param $charset_collate
   * @return string
   */
  private function getTemporarySoftwareTableSQL(): string {

    $table_name = $this->getTableNameTempSoft();
    $charset_collate = $this->charset_collate;

    $sql = "CREATE TABLE $table_name (
                      ProductId varchar(255) not null,
                      ManufPartNo varchar(255) null,
                      Brand varchar(255) null,
                      Description varchar(255) null,
                      Price varchar(255) null,
                      Stock varchar(255) null,
                      Category1 varchar(255) null,
                      Category2 varchar(255) null,
                      EAN varchar(255) null,
                      Status varchar(255) null,
                      ReleaseDate varchar(255) null,
                      PRIMARY KEY (ProductId)
                    )
                    $charset_collate";

    return $sql;
  }

  private function getFtpConfigTableSQL(): string {

    $table_name = $this->getTableNameFtpConfig();
    $charset_collate = $this->charset_collate;

    $sql = "create table $table_name (
            id int auto_increment,
            dropshipping_type varchar(8) not null,
            user varchar(255) not null,
            password varchar(255) not null,
            server_ip varchar(50) not null,
            file_name varchar(255) not null,
            primary key (id)
            )
            $charset_collate";

    return $sql;
  }



  private function getAlterWpPostTableSQL() {

    $table_name = $this->getTableNameWpPosts();

    $sql = "alter table $table_name
            add dropshipping_id varchar(50) null;";

    return $sql;
  }

  public function createProductTable(): void {


    $sql = $this->getProductTableSQL();
    dbDelta( $sql );
  }

  public function createProfitTable(): void {

    $sql = $this->getProfitTableSQL();
    dbDelta( $sql );
  }

  public function createTemporaryHardwareMaterialsTable(): void {

    $sql = $this->getTemporaryHardwareMaterialsTableSQL();
    dbDelta( $sql );
  }

  public function createTemporaryHardwarePricesTable(): void {

    $sql = $this->getTemporaryHardwarePricesTableSQL();
    dbDelta( $sql );
  }


  public function createTemporarySoftwareTable(): void {

    $sql = $this->getTemporarySoftwareTableSQL();
    dbDelta( $sql );
  }

  public function createFtpConfigTable(): void {

    $sql = $this->getFtpConfigTableSQL();
    dbDelta( $sql );
  }

  public function alterWpPostsTable() {
    global $wpdb;
    $sql = $this->getAlterWpPostTableSQL();
    $wpdb->query($sql);
  }

}