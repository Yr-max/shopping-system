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
            <h3 class="card-title">Product Listings</h3>
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

          // for search function
          if (empty($_POST['search']) && empty($_COOKIE['search'])) {
              // Display data from posts table 
            $stmt = $db->prepare("SELECT * FROM products ORDER BY id DESC");
            $stmt->execute();
            $rawResult = $stmt->fetchAll();
            $total_pages = ceil(count($rawResult) / $numOfrecord);

            $stmt = $db->prepare("SELECT * FROM products ORDER BY id DESC LIMIT $offset,$numOfrecord");
            $stmt->execute();
            $result = $stmt->fetchAll();
          }else {

            $searchKey = isset($_POST['search']) ? $_POST['search'] : (isset($_COOKIE['search']) ? $_COOKIE['search'] : '');
            // Display data from products table 
            $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' ORDER BY id DESC");
            $stmt->execute();
            $rawResult = $stmt->fetchAll();
            $total_pages = ceil(count($rawResult) / $numOfrecord);

            $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' ORDER BY id DESC LIMIT $offset,$numOfrecord");
            $stmt->execute();
            $result = $stmt->fetchAll();
          }
          ?>
          <div class="card-body">
            <div>
              <a href="product_add.php" type="button" class="btn btn-success">Create New Product</a>
            </div><br>
            <table class="table table-bordered">
              <thead>                  
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Category</th>
                  <th>In Stock</th>
                  <th>Price</th>
                  <th style="width: 50px">Actions</th>
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
                    // to get category name to display in products table for admin view
                    $categoryStmt = $db->prepare("SELECT * FROM categories WHERE id=".$value['category_id']);
                    $categoryStmt->execute();
                    $categoryResust = $categoryStmt->fetchAll();
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo escape($value['name']); ?></td>
                    <td><?php echo escape(substr($value['description'], 0,50)); ?></td>
                    <td><?php echo escape($categoryResust[0]['name']); ?></td>
                    <td><?php echo escape($value['quantity']); ?></td>
                    <td><?php echo escape($value['price']); ?></td>
                    <td>
                      <div class="btn-group">
                        <div class="container">
                          <a href="product_edit.php?id=<?php echo $value['id'] ?>" type="button" class="btn btn-secondary">Edit</a>
                        </div>
                        <div class="container">
                          <a href="product_delete.php?id=<?php echo $value['id'] ?>" 
                            onclick="return confirm('Are you sure you want to delete this item?');"
                            type="button" class="btn btn-danger">Delete</a>
                          </div>
                        </div>
                      </td>
                    </tr>

                    <?php
                    $i++;
                  } 
                }
                ?>
              </tbody>
            </table><br>
            <div>
              <nav aria-label="Page navigation example" style="float:right;">
                <ul class="pagination">
                  <li class="page-item"><a class="page-link" href="?page-no=1">First</a></li>
                  <li class="page-item <?php if ($page_no <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if ($page_no <= 1) {
                      echo '#';
                    }else {
                      echo "?page-no".($page_no-1);
                    } ?>">Previous
                  </a>
                </li>
                <li class="page-item"><a class="page-link" href="#"><?php echo $page_no; ?></a></li>
                <li class="page-item <?php if ($page_no >= $total_pages) { echo 'disabled'; } ?>">
                  <a class="page-link" href="<?php 
                  if ($page_no >= $total_pages) {
                    echo '#';
                  } else {
                    echo "?page-no=" . ($page_no + 1);
                  }
                ?>">
                Next
              </a>

            </li>
            <li class="page-item"><a class="page-link" href="?page-no=<?php echo $total_pages; ?>">Last</a></li>
          </ul>
        </nav>
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
