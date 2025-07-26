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


if ($_POST) {
  if (empty($_POST['name']) || empty($_POST['email'])) {
    
    if (empty($_POST['name'])) {
      $nameError = 'Name field is require';
    }
    if (empty($_POST['email'])) {
      $emailError = 'Email field is require';
    }
  }else if(!empty($_POST['password']) && strlen($_POST['password']) < 4) {
      $passwordError = 'Password should be 4 character at least.';
  }
  else {

    $id = $_GET['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $role = empty($_POST['role']) ? 0 : 1;

    // Check for existing user
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND id != :id");
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // ✅ Corrected PDO usage

    if ($user) {
      echo "<script>alert('This email is already used');</script>";
    }else {
      // update get_data into user table 
      if ($password != null) {
          $stmt = $db->prepare("UPDATE users SET name='$name', email='$email', password='$password', role='$role' WHERE id='$id'");
      }else {
        $stmt = $db->prepare("UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$id'");
      }
      $result = $stmt->execute();

      if ($result) 
      {
        echo "<script>
        alert('User is  successfully updated');
        window.location.href = 'user_list.php';
        </script>";
      }
    }

  }
  
}


?>

<?php
include('header.php');

// To show update data from user table
$stmt = $db->prepare("SELECT * FROM users WHERE id=".$_GET['id']);
$stmt->execute();
$result = $stmt->fetchAll();

// print "<pre>";
// print_r($result);
?>

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">User Edit Table</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
              <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
              <div class="form-group">
                <input type="hidden" name="id" value="<?php echo $result[0]['id']; ?>">
                <label>Name</label>
                <p style="color: red;"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                <input class="form-control" type="text" name="name" value="<?php echo escape($result[0]['name']); ?>" >
              </div>
              <div class="form-group">
                <label>Email</label>
                <p style="color: red;"><?php echo empty($emailError) ? '' : '*'.$emailError; ?></p>
                <input class="form-control" type="email" name="email" value="<?php echo escape($result[0]['email']); ?>" >
              </div>
              <div class="form-group">
                <label>Password</label>
                <p style="color: red;"><?php echo empty($passwordError) ? '' : '*'.$passwordError; ?></p>
                <span style="font-size: 15px; color: blue;">This user already has a password</span>
                <input class="form-control" type="text" name="password" value="<?php echo $result[0]['password']; ?>" >
              </div>
              <div class="form-group">
                <label for="role">Role</label><br>
                <input type="checkbox" name="role" value="1 
                <?php if ($result[0]['role'] == 1) echo 'checked'; ?>" 
                >
              </div>

              <div class="form-group">
                <input class="btn btn-success" type="submit" value="Update">
                <a href="user_list.php" class="btn btn-secondary">Back</a>
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
