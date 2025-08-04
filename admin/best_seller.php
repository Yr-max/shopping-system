<?php

session_start();
require "../config/config.php";
require "../config/common.php";

// Control for access  Login Session 
if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'] )) {
  header('location: login.php');
}

if ($_SESSION['role'] != 1) {
  header('location: login.php');
}// Control Login Session



if (isset($_POST['search']) && $_POST['search'] != '') {
  setcookie('search', $_POST['search'], time() + (86400 * 30), "/");
} else {
  if (empty($_GET['page-no'])) {
    unset($_COOKIE['search']);
    setcookie('search', null, -1, '/');
  }
}


?>

<?php
include('header.php');
?>

<!-- Main content - -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Best Sell Items</h3>
            <br>
            <p>Items which are sold above 5:</p>
          </div>
          <!-- /.card-header -->
          <?php

          $currentDate = date('Y-m-d');

            // Display data from sale order table 
          $stmt = $db->prepare("SELECT * FROM sale_order_details GROUP BY product_id HAVING SUM(quantity) > 5 ORDER BY id DESC");

          $stmt->execute();
          $result = $stmt->fetchAll();

          ?>

          <div class="card-body">
            <table id="d-table" class="table table-bordered display">
              <thead>
                <tr>
                  <th style="width: 30px">#</th>
                  <th>Products</th>
                </tr>
              </thead>
              <tbody>
                <?php
              if ($result) 
              {
                $i = 1;
                foreach ($result as $value) {
                  ?>
                  <?php
                    $stmt = $db->prepare("SELECT * FROM products WHERE id=".$value['product_id']);
                    $stmt->execute();
                    $resust = $stmt->fetchAll();
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo escape($resust[0]['name']); ?></td>
                  </tr>

                    <?php
                    $i++;
                  } 
                }
                ?>
              </tbody>
            </table><br>
            <div>

            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
    </div>
<!-- /.row -->
</div><!-- /.container-fluid -->
</div>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include('footer.html');
?>

<script>
  $(document).ready(function () {
    $('#d-table').DataTable(); // ← zero config
  });
</script>