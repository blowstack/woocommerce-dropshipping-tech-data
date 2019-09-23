<?php
global $wpdb;

 if (isset($_POST['save']) && $_POST['save']) {
   foreach ($_POST as $id => $profit) {
     if ($id != 'save' && $profit != 'Accept changes') {
       $wpdb->query("update wp_dropshipping_profit set profit = {$profit} where id = {$id}");
     }
   }
 }

if (isset($_POST['update_prices']) && $_POST['update_prices'] ) {
  $TechDataSoftware = new TechDataProductGenerator();
  $TechDataSoftware->updatePriceByMarginAndCost();

}

  $categories = $wpdb->get_results("SELECT * FROM wp_term_taxonomy taxonomy where taxonomy.taxonomy = 'product_cat'");
  $profits = $wpdb->get_results("SELECT * FROM wp_dropshipping_profit");
?>


<div class="container">
  <form method="post">
  <table width="30%" style="text-align: center">
    <caption><h2>Profit Margin Table</h2></caption>
    <tr>
      <th>From</th>
      <th>To</th>
      <th>Profit</th>
    </tr>
<?php
  foreach ($profits as $profit) {
          echo "<tr>
                <td> $profit->range_from </td>
                <td> $profit->range_to </td>
                <td>  <input type='text' name='$profit->id' value='$profit->profit' placeholder='enter percentage' required='required'> % </td>
                </tr>";
  }
?>
    <?php
    if (isset($_POST['save']) && $_POST['save'] ) {
      echo "<tr>
      <th>
        saved!
      </th>
    </tr>";
    }
    ?>
  </table>
    <input type="submit" name="save" value="Accept changes">
  </form>



  <h2>Update prices for Software</h2>
  <form method="post">
    <input type="submit" name="update_prices" value="Update software products">
    <?php
    if (isset($_POST['update_prices']) && $_POST['update_prices'] ) {
      echo 'ok';
    }
    ?>
  </form>
</div>