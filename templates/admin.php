<?php

global $wpdb;

$profits = $wpdb->get_results("SELECT * FROM wp_dropshipping_profit");

?>

<?php
  if (isset($_POST['save']) && $_POST['save']) {

    foreach ($_POST as $id => $profit) {
      if ($id != 'save' && $profit != 'Accept changes') {
        $wpdb->query("update wp_dropshipping_profit set profit = {$profit} where id = {$id}");
      }
    }

    $TechDataSoftware = new TechDataProductGenerator();
    $TechDataSoftware->updatePrice();
  }
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
              <td>  
                <input type='text' name='$profit->id' value='$profit->profit' placeholder='enter percentage' required='required'> %
              </td>
              </tr>";
    }
    ?>
  </table>
    <input type="submit" name="save" value="Save and update">
  </form>
  <?php
  if (isset($_POST['save']) && $_POST['save'] ) {
    echo 'saved and updated!';
  }
  ?>

  <?php

  if (isset($_POST['csv']) && $_POST['csv']) {
    $TechDataProductGenerator = new TechDataProductGenerator(DropShipping::$type_software);
    $products = $TechDataProductGenerator->getForCSV();

    ob_end_clean();

    $fp = fopen('php://output', 'w');

    if ($fp && $products) {
      $filename = 'products_all_ ' . date('Y-m-d') . '.csv';
      header('Content-Type: text/csv');
      header("Content-Disposition: attachment; filename={$filename}");
      header('Pragma: no-cache');
      header('Expires: 0');
      fputcsv($fp, [
        'sap_no',
        'producer_code',
        'vendor',
        'title',
        'stock',
        'price',
        'cost'
      ]);

      foreach ($products as $product) {
        fputcsv($fp, [
          $product->sap_no,
          $product->producer_code,
          $product->vendor,
          $product->title,
          $product->stock,
          $product->price,
          $product->cost,
        ]);
      }
    }
    exit;
  }
  ?>


  <div class="container" style="margin-top: 20px">
    <form method="post">
      <input type="submit" name="csv" value="Download CSV">
      <?php
      if (isset($_POST['csv']) && $_POST['csv']) {
        echo 'ok';
      }
      ?>
      <br>
    </form>
  </div>