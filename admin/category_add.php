<?php

session_start();
require "../config/config.php";
require "../config/common.php";

// Control Login Session
if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'] )) 
{
  header('location: login.php');
}

if ($_SESSION['role'] != 1) {
  header('location: login.php');
}// Control Login Session



if ($_POST) 
{

  if (empty($_POST['name']) || empty($_POST['description'])) {
    if (empty($_POST['name'])) {
      $nameError = "Category name field is require";
    }
    if (empty($_POST['description'])) {
      $descriptionError = "Description field is require";
    }
  }else {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $db->prepare("INSERT INTO categories(name, description) VALUES (:name, :description)");
    $result = $stmt->execute(array(':name'=>$name, ':description'=>$description));

    if ($result) {
      echo "<script>
        alert('New Category is added');
        window.location.href='category.php';
        </script>";
    }
}
  
}
?>

<?php
include('header.php');
?>

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Category Creating Table</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <form action="" method="post">
              <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
              <div class="form-group">
                <label>Name</label>
                <p style="color: red;"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                <input class="form-control" type="text" name="name" >
              </div>
              <div class="form-group">
                <label>Description</label>
                <p style="color: red;"><?php echo empty($descriptionError) ? '' : '*'.$descriptionError; ?></p>
                <textarea class="form-control" name="description" rows="4" cols="50"></textarea>
              </div>
              <div class="form-group">
                <input class="btn btn-success" type="submit" value="Create">
                <a href="category.php" class="btn btn-secondary">Back</a>
              </div>
            </form>
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
