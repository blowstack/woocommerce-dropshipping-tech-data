<?php
include_once(ABSPATH . 'TechDataHardwareApi.php');

global $wpdb;

$title = 'test';
$items = $wpdb->get_results("select wp_");

$TechDataHardwareApi = new TechDataHardwareApi("$title",$items);

echo $TechDataHardwareApi->getTitle();
