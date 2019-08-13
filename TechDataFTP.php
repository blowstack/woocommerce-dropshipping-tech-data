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
  protected $table_name;
  protected $wp_filesystem;


  public function __construct() {
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

}