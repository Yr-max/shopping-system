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
  //Validation 
  if (empty($_POST['name']) || empty($_POST['description']) || empty($_POST['category'])
      || empty($_POST['quantity']) || empty($_POST['price']) || empty($_FILES['image'])) 
  {
    if (empty($_POST['name'])) {
      $nameError = "Name field is require";
    }
    if (empty($_POST['description'])) {
      $descriptionError = "Description field is require";
    }
    if (empty($_POST['category'])) {
      $categoryError = "Choose one category";
    }
    if (empty($_POST['quantity'])) {
      $qtyError = "Quantity field is require";
    }elseif (is_numeric($_POST['quantity']) != 1) {
      $qtyError = "Quantity field is must be integer";
    }
    if (empty($_POST['price'])) {
      $priceError = "Price field is require";
    }elseif (is_numeric($_POST['price']) != 1) {
      $priceError = "Price field is must be integer";
    }
    if (empty($_POST['image'])) {
      $imageError = "Image field is require";
    }
  }else {

    //validate image to update
    if ($_FILES['image']['name'] != null) {

      $file = 'images/'.($_FILES['image']['name']);
      $imageType = pathinfo($file,PATHINFO_EXTENSION);

    if ($imageType != 'jpg' && $imageType != 'jpeg' && $imageType != 'png') {
      echo "<script>alert('Image type must be jpg, png, jpeg');</script>";
    }else {//image validation success

      $id = $_POST['id'];
      $name = $_POST['name'];
      $description = $_POST['description'];
      $category_id = $_POST['category']; // to get category id
      $quantity = $_POST['quantity'];
      $price = $_POST['price'];
      $image = $_FILES['image']['name'];

      move_uploaded_file($_FILES['image']['tmp_name'], $file);

      $stmt = $db->prepare("UPDATE products SET name=:name, description=:description, 
                      category_id=:category_id, quantity=:quantity, price=:price, image=:image WHERE id=:id");
      $result = $stmt->execute(array(
        ':name'=>$name,
        ':description'=>$description,
        ':category_id'=>$category_id,
        ':quantity'=>$quantity,
        ':price'=>$price,
        ':image'=>$image,
        ':id'=>$id
      ));

      if ($result) {
        echo "<script>alert('Product is successfully updated');
        window.location.href='index.php';
        </script>";
      }
  }
    }else {

      $id = $_POST['id'];
      $name = $_POST['name'];
      $description = $_POST['description'];
      $category_id = $_POST['category']; // to get category id
      $quantity = $_POST['quantity'];
      $price = $_POST['price'];

      $stmt = $db->prepare("UPDATE products SET name=:name, description=:description, 
                      category_id=:category_id, quantity=:quantity, price=:price WHERE id=:id");
      $result = $stmt->execute(array(
        ':name'=>$name,
        ':description'=>$description,
        ':category_id'=>$category_id,
        ':quantity'=>$quantity,
        ':price'=>$price,
        ':id'=>$id
      ));

      if ($result) {
        echo "<script>alert('Product is successfully updated');
        window.location.href='index.php';
        </script>";
      }
    }
  }
  
}
?>

<?php
  include('header.php');

  $stmt = $db->prepare("SELECT * FROM products WHERE id=".$_GET['id']);
  $stmt->execute();
  $result = $stmt->fetchAll();
?>

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Product Creating Table</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?php echo $result[0]['id']; ?>">
              <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
              <div class="form-group">
                <label>Name</label>
                <p style="color: red;"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                <input class="form-control" type="text" name="name" value="<?php echo escape($result[0]['name']) ?>" >
              </div>
              <div class="form-group">
                <label>Description</label>
                <p style="color: red;"><?php echo empty($descriptionError) ? '' : '*'.$descriptionError; ?></p>
                <textarea class="form-control" name="description" rows="4" cols="30"><?php echo escape($result[0]['description']) ?></textarea>
              </div>
              <div class="form-group">
                <?php
                  // get category id and name to display in drowdown list
                  $categoryStmt = $db->prepare("SELECT * FROM categories");
                  $categoryStmt->execute();
                  $categoryResust = $categoryStmt->fetchAll();
                ?>
                <label>Category</label>
                <p style="color: red;"><?php echo empty($categoryError) ? '' : '*'.$categoryError; ?></p>
                <select class="form-control" name="category">
                  <option value="">SELECT CATEGORY</option>
                  <?php
                    foreach ($categoryResust as $value) { ?>
                      
                      <?php
                        if ($value['id'] == $result[0]['category_id']) : ?>

                          <option value="<?php echo $value['id'] ?>" selected>
                            <?php echo $value['name'] ?>  
                          </option>
                        <?php else : ?>
                          <option value="<?php echo $value['id'] ?>">
                            <?php echo $value['name'] ?>  
                          </option>
                        <?php endif ?>
                      ?>

                      <?php
                    }
                  ?>
                </select>
              </div>
              <div class="form-group">
                <label>Quantity</label>
                <p style="color: red;"><?php echo empty($qtyError) ? '' : '*'.$qtyError; ?></p>
                <input class="form-control" type="number" name="quantity" value="<?php echo escape($result[0]['quantity']) ?>">
              </div>
              <div class="form-group">
                <label>Price</label>
                <p style="color: red;"><?php echo empty($priceError) ? '' : '*'.$priceError; ?></p>
                <input class="form-control" type="number" name="price" value="<?php echo escape($result[0]['price']) ?>">
              </div>
              <div class="form-group">
                <label>Image</label><br>
                <img src="images/<?php echo escape($result[0]['image']) ?>" width="150" height="150">
                <p style="color: red;"><?php echo empty($imageError) ? '' : '*'.$imageError; ?></p>
                <input type="file" name="image" >
              </div>
              <div class="form-group">
                <input class="btn btn-success" type="submit" value="Update">
                <a href="index.php" class="btn btn-secondary">Back</a>
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
