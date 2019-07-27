<?php
global $wpdb;

 if($_POST) {
   foreach ($_POST as $term => $profit) {
     $wpdb->query("update wp_terms set profit_margin = {$profit} where term_id = {$term}");
   }
 }

  $categories = $wpdb->get_results("select term.* from wp_terms term
                                          inner join wp_term_taxonomy taxonomy on taxonomy.term_id = term.term_id
                                          where taxonomy.taxonomy = 'product_cat'");
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
            <td> $category->name </td>
            <td>  <input type='text' name='$category->term_id' value='$category->profit_margin' placeholder='enter percentage' required='required'> </td>
            </tr>";
    }
    ?>
  </table>
    <input type="submit" value="Accept changes">
  </form>
</div>