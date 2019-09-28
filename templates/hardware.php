<?php

  $TechDataConfigManager = new TechDataConfigManager();
  $Config = $TechDataConfigManager->getConfg(DropShipping::$type_hardware);

  if (isset($_POST['sync']) && $_POST['sync']) {

    $TechDataSynchronizer = new TechDataSynchronizer(DropShipping::$type_hardware);
    $TechDataSynchronizer->syncAllFromTechData();
  }

  if (isset($_POST['config']) && $_POST['config']) {

    $TechDataConfigManager->setConfig($_POST, DropShipping::$type_hardware);
  }

?>


<div class="container" style="margin-top: 30px">
  <form method="post">

    <h2>Hardware FTP Credentials</h2>

    <label style="display: inline-block;width: 100px;">User:</label>
    <input type="text" name="user" value="<?php echo ($Config->user ?? '') ?>" required="required"><br>

    <label style="display: inline-block;width: 100px;">Password:</label>
    <input type="password" name="password" value="<?php echo ($Config->password ?? '') ?>" required="required"><br>

    <label style="display: inline-block;width: 100px;">Server address:</label>
    <input type="text" name="server_address" placeholder="IP or name" value="<?php echo ($Config->server_ip ?? '') ?>" required="required"><br>

    <label style="display: inline-block;width: 100px;">File name:</label>
    <input type="text" name="file_name" value="<?php echo ($Config->file_name ?? '') ?>"  placeholder="without extension" required="required"><br>

    <input type="submit" name="config" value="Save config">
    <?php
    if (isset($_POST['config']) && $_POST['config']) {
      echo 'ok';
      header("Refresh:0");
    }
    ?>
  </form>
</div>

<div class="container" style="margin-top: 30px">
  <form method="post">
    <input type="submit" name="sync" value="Sync hardware from TechData">
    <p> - downloads zip file from the TechData server</p>
    <p> - extracts the file to a csv folder (4 separate files)</p>
    <p> - insert new products and categories if necessary to WooCommerce</p>
    <p> - updates stocks and prices provided by TechData</p>
    <p> - updates profit margin for products where prices changed</p>
    <p> - use it with caution, it's time and resource consuming <br> &nbsp; and may lock your shop for a while!</p>
    <?php
      if (isset($_POST['sync']) && $_POST['sync']) {
       echo 'ok';
      }
    ?>
    <br>
  </form>
</div>