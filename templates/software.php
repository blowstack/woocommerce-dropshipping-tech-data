<?php

  if (isset($_POST['sync']) && $_POST['sync']) {

    $TechDataSynchronizer = new TechDataSynchronizer(DropShipping::$type_software);
    $TechDataSynchronizer->syncAllFromTechData();
  }
  ?>

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