<?php

  $TechDataConfigManager = new TechDataConfigManager();
  $Config = $TechDataConfigManager->getConfg(DropShipping::$type_software);

  if (isset($_POST['sync']) && $_POST['sync']) {
    $TechDataSynchronizer = new TechDataSynchronizer(DropShipping::$type_software);
    $TechDataSynchronizer->syncAllFromTechData();
  }

  if (isset($_POST['config']) && $_POST['config']) {

    $TechDataConfigManager->setConfig($_POST, DropShipping::$type_software);
  }

?>


<div class="container" style="margin-top: 30px">
  <form method="post">

    <h2>Software FTP Credentials</h2>

    <label style="display: inline-block;width: 100px;">User:</label>
    <input type="text" name="user" value="<?php echo ($Config->user ?? '') ?>"><br>

    <label style="display: inline-block;width: 100px;">Password:</label>
    <input type="password" name="password" value="<?php echo ($Config->password ?? '') ?>"><br>

    <label style="display: inline-block;width: 100px;">Server address:</label>
    <input type="text" name="server_address" value="<?php echo ($Config->server_ip ?? '') ?>"><br>

    <label style="display: inline-block;width: 100px;">File name:</label>
    <input type="text" name="file_name" value="<?php echo ($Config->file_name ?? '') ?>"><br>

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
    <input type="submit" name="sync" value="Sync software from TechData">
    <?php
    if (isset($_POST['sync']) && $_POST['sync']) {
      echo 'ok';
    }
    ?>
  </form>
</div>