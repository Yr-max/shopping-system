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
            <h3 class="card-title">Order Details Listing</h3>
          </div>
          <!-- /.card-header -->
          <?php
            // offset function
          if (!empty($_GET['page-no'])) {
            $page_no = $_GET['page-no'];
          }else {
            $page_no = 1;
          }

          $numOfrecord = 5;
          $offset = ($page_no -1) * $numOfrecord;

            //to get sale orders details data
          $stmt = $db->prepare("SELECT * FROM sale_order_details WHERE sale_order_id=".$_GET['id']);
          $stmt->execute();
          $rawResult = $stmt->fetchAll();
          $total_pages = ceil(count($rawResult) / $numOfrecord);

          $stmt = $db->prepare("SELECT * FROM sale_order_details WHERE sale_order_id=".$_GET['id']." LIMIT $offset,$numOfrecord");
          $stmt->execute();
          $result = $stmt->fetchAll();
          ?>
          <div class="card-body">
            <table class="table table-bordered">
              <thead>                  
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Order Date</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <?php
              if ($result) 
              {
                $i = 1;
                foreach ($result as $value) {
                  ?>
                  <?php
                    // to get product name to display in order details table for admin view
                  $productStmt = $db->prepare("SELECT * FROM products WHERE id=".$value['product_id']);
                  $productStmt->execute();
                  $productResust = $productStmt->fetchAll();
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo escape($productResust[0]['name']); ?></td>
                    <td><?php echo escape($value['quantity']); ?></td>
                    <td><?php echo escape(date('Y-m-d',strtotime($value['order_date']))); ?></td>
                  </tr>

                  <?php
                  $i++;
                } 
              }
              ?>
            </tbody>
          </table><br>
          <div>
            <div class="d-flex justify-content-between align-items-center">
              <a href="order_list.php" class="btn btn-info">Back</a>
              <nav aria-label="Page navigation example">
                <ul class="pagination mb-0">
                  <li class="page-item"><a class="page-link" href="?page-no=1">First</a></li>
                  <li class="page-item <?php if ($page_no <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="<?php echo ($page_no <= 1) ? '#' : '?page-no=' . ($page_no - 1); ?>">Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#"><?php echo $page_no; ?></a></li>
                    <li class="page-item <?php if ($page_no >= $total_pages) echo 'disabled'; ?>">
                      <a class="page-link" href="<?php echo ($page_no >= $total_pages) ? '#' : '?page-no=' . ($page_no + 1); ?>">Next</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="?page-no=<?php echo $total_pages; ?>">Last</a></li>
                  </ul>
                </nav>
              </div>

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
