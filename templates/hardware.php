<?php

  if (isset($_POST['sync']) && $_POST['sync']) {

    $TechDataSynchronizer = new TechDataSynchronizer(DropShipping::$type_hardware);
    $TechDataSynchronizer->syncAllFromTechData();
  }
?>

<div class="container" style="margin-top: 30px">
  <form method="post">
    <input type="submit" name="sync" value="Sync hardware from TechData">
    <?php
      if (isset($_POST['sync']) && $_POST['sync']) {
       echo 'ok';
      }
    ?>
    <br>
  </form>
</div>