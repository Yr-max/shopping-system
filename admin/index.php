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
            <h3 class="card-title">Product Listing</h3>
          </div>
          <!-- /.card-header -->
          
          <div class="card-body">
            <div>
              <a href="category.php" type="button" class="btn btn-success">Create New Blog</a>
            </div><br>
            <table class="table table-bordered">
              <thead>                  
                <tr>
                  <th style="width: 10px">#</th>
                  <th>Blogs Title</th>
                  <th>Content</th>
                  <th style="width: 50px">Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              
              </tbody>
            </table><br>
            <div>
              <nav aria-label="Page navigation example" style="float:right;">
                
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
