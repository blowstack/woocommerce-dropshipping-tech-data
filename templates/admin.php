<?php
global $wpdb;

 if($_POST['save']) {
   foreach ($_POST as $term => $profit) {
     if ($term != 'save' && $profit != 'Accept changes') {
       $wpdb->query("update wp_term_taxonomy set profit_margin = {$profit} where term_id = {$term}");
     }
   }
 }

if($_POST['update']) {
  $TechDataSoftware = new TechDataProductGenerator();
  $TechDataSoftware->updatePriceByMarginAndCost();

}

  $categories = $wpdb->get_results("SELECT * FROM wp_term_taxonomy taxonomy where taxonomy.taxonomy = 'product_cat'");
?>



<div class="container">
  <h2>Profit Margin Table</h2>
  <form method="post">
  <table>
    <tr>
      <th>Category</th>
      <th>Profit %</th>
    </tr>
    <?php
    foreach ($categories as $category) {
      echo "<tr>
            <td> $category->description </td>
            <td>  <input type='text' name='$category->term_id' value='$category->profit_margin' placeholder='enter percentage' required='required'> </td>
            </tr>";
    }
    ?>
  </table>
    <input type="submit" name="save" value="Accept changes">
  </form>

  <h2>Update prices for Software</h2>
  <form method="post">
    <input type="submit" name="update" value="Update software products">
    <?php
    if ($_POST['update']) {
      echo 'ok';
    }
    ?>
  </form>
</div>