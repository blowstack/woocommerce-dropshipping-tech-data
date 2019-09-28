<?php

/*
 * TechDataFTP
 */

class TechDataConfigManager {

  private $ConfigManagerRepository;

  public function __construct() {
    require_once( dirname( __FILE__ ) . '/repos/ConfigManagerRepository.php' );

    $this->ConfigManagerRepository = new ConfigManagerRepository();
  }

  /**
   * @param $dropshipping_type
   * @return array
   */
  public function getConfg($dropshipping_type): ?object {
    $ConfigManagerRepository = $this->ConfigManagerRepository;

    $Config = $ConfigManagerRepository->getConfig($dropshipping_type);

    return $Config[0] ?? null;
  }
  public function setConfig(array $config, $dropshipping_type): void {
    $ConfigManagerRepository = $this->ConfigManagerRepository;

    $ConfigManagerRepository->setConfig($config, $dropshipping_type);
  }

}