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


// add new user/admin 
if ($_POST) 
{
  if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['address']) || empty($_POST['password']) || strlen($_POST['password']) < 4) {
    
    if (empty($_POST['name'])) {
      $nameError = 'Name field is require';
    }
    if (empty($_POST['email'])) {
      $emailError = 'Email field is require';
    }
    if (empty($_POST['phone'])) {
      $phoneError = 'Phone field is require';
    }
    if (empty($_POST['address'])) {
      $addressError = 'Address field is require';
    }
    if (empty($_POST['password'])) {
      $passwordError = 'Password field is require';
    }
    if(strlen($_POST['password']) < 4) {
      $passwordError = 'Password should be 4 character at least.';
    }
  }else {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $role = empty($_POST['role']) ? 0 : 1;
   
    // Check for existing user
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // ✅ fixed typo (PDO not POD)

    if ($user) {
      echo "<script>
      alert('This email is already used');
      </script>";
    }else {
          // insert get_data into users table 
      $stmt = $db->prepare("INSERT INTO users (name,email,phone,address,password,role) VALUES (:name,:email,:phone,:address,:password,:role)");
      $result = $stmt->execute(
        array(':name' =>$name,':email' =>$email,':phone' =>$phone,':address' =>$address,':password' =>$password,':role' =>$role)
      );
      if ($result) {
        echo "<script>
        alert('New user is successfully added');
        window.location.href = 'user_list.php';
        </script>";
      }
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
            <h3 class="card-title">User Creating Table</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
              <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
              <div class="form-group">
                <label>Name</label>
                <p style="color: red;"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                <input class="form-control" type="text" name="name" >
              </div>
              <div class="form-group">
                <label>Email</label>
                <p style="color: red;"><?php echo empty($emailError) ? '' : '*'.$emailError; ?></p>
                <input class="form-control" type="email" name="email" >
              </div>
              <div class="form-group">
                <label>Phone Number</label>
                <p style="color: red;"><?php echo empty($phoneError) ? '' : '*'.$phoneError; ?></p>
                <input class="form-control" type="text" name="phone" >
              </div>
              <div class="form-group">
                <label>Address</label>
                <p style="color: red;"><?php echo empty($addressError) ? '' : '*'.$addressError; ?></p>
                <input class="form-control" type="text" name="address" >
              </div>
              <div class="form-group">
                <label>Password</label>
                <p style="color: red;"><?php echo empty($passwordError) ? '' : '*'.$passwordError; ?></p>
                <input class="form-control" type="text" name="password" >
              </div>
              <div class="form-group">
                <label for="vehicle3">Role</label><br>
                <input type="checkbox" name="role" value="1">
              </div>
              <div class="form-group">
                <input class="btn btn-success" type="submit" value="Create">
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
