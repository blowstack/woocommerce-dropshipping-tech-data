<?php

  if (isset($_POST['sync']) && $_POST['sync']) {

    $TechDataSynchronizer = new TechDataSynchronizer(DropShipping::$type_software);
    $TechDataSynchronizer->syncAllFromTechData();
  }
?>

<?php

if (isset($_POST['config']) && $_POST['config']) {


}
?>




<div class="container" style="margin-top: 30px">
  <form method="post">
    <caption><h2>Software FTP Credentials</h2></caption>
    <label style="display: inline-block;width: 100px;">User:</label> <input type="text" name="user"><br>
   <label style="display: inline-block;width: 100px;">Password:</label>  <input type="password" name="password"><br>
   <label style="display: inline-block;width: 100px;">Server address:</label>  <input type="text" name="server_address"><br>
   <label style="display: inline-block;width: 100px;">File name:</label>  <input type="text" name="file_name"><br>
    <input type="submit" name="config" value="Save config">
    <?php
    if (isset($_POST['config']) && $_POST['config']) {
      echo 'ok';
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