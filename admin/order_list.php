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
            <h3 class="card-title">Orders Listing</h3>
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

            //to get sale orders data
            $stmt = $db->prepare("SELECT * FROM sale_orders ORDER BY id DESC");
            $stmt->execute();
            $rawResult = $stmt->fetchAll();
            $total_pages = ceil(count($rawResult) / $numOfrecord);

            $stmt = $db->prepare("SELECT * FROM sale_orders ORDER BY id DESC LIMIT $offset,$numOfrecord");
            $stmt->execute();
            $result = $stmt->fetchAll();
          ?>
          <div class="card-body">
            <!-- <div>
              <a href="category_add.php" type="button" class="btn btn-success">Create New Category</a>
            </div><br> -->
            <table class="table table-bordered">
              <thead>                  
                <tr>
                  <th style="width: 10px">#</th>
                  <th>User Name</th>
                  <th>Total Price</th>
                  <th>Order Date</th>
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
                    // to get user name to display in order list table for admin view
                    $userStmt = $db->prepare("SELECT * FROM users WHERE id=".$value['user_id']);
                    $userStmt->execute();
                    $userResust = $userStmt->fetchAll();

                    // print("<pre>");
                    // print_r($userResust);
                    // exit();
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo escape($userResust[0]['name']); ?></td>
                    <td><?php echo escape($value['total_price']); ?></td>
                    <td><?php echo escape(date('Y-m-d',strtotime($value['order_date']))); ?></td>

                    <td>
                      <div class="btn-group">
                        <div class="container">
                          <a href="order_details.php?id=<?php echo $value['id'] ?>" type="button" class="btn btn-primary">View</a>
                        </div>
                        <div class="container">
                          
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
