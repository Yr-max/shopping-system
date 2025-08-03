<?php

if (isset($_POST['search']) && $_POST['search'] != '') {
	  setcookie('search', $_POST['search'], time() + (86400 * 30), "/");
	} else {
	  if (empty($_GET['page-no'])) {
	    unset($_COOKIE['search']);
	    setcookie('search', null, -1, '/');
	  }
	}

?>

<?php include('header.php') ?>

<?php
	
	require 'config/config.php';

    // offset function
  if (!empty($_GET['page-no'])) {
    $page_no = $_GET['page-no'];
  }else {
    $page_no = 1;
  }

  $numOfrecord = 6;
  $offset = ($page_no -1) * $numOfrecord;

  // for search function
  if (empty($_POST['search']) && empty($_COOKIE['search'])) {

  	if (isset($_GET['category_id'])) {

  		$categoryId = $_GET['category_id'];
  		// Display data from product table 
	    $stmt = $db->prepare("SELECT * FROM products WHERE category_id=$categoryId AND quantity > 0 ORDER BY id DESC");
	    $stmt->execute();
	    $rawResult = $stmt->fetchAll();
	    $total_pages = ceil(count($rawResult) / $numOfrecord);

	    $stmt = $db->prepare("SELECT * FROM products WHERE category_id=$categoryId AND quantity > 0 ORDER BY id DESC LIMIT $offset,$numOfrecord");
	    $stmt->execute();
	    $result = $stmt->fetchAll();
  	} else {
  		// Display data from product table 
	    $stmt = $db->prepare("SELECT * FROM products WHERE quantity > 0 ORDER BY id DESC");
	    $stmt->execute();
	    $rawResult = $stmt->fetchAll();
	    $total_pages = ceil(count($rawResult) / $numOfrecord);

	    $stmt = $db->prepare("SELECT * FROM products WHERE quantity > 0 ORDER BY id DESC LIMIT $offset,$numOfrecord");
	    $stmt->execute();
	    $result = $stmt->fetchAll();
  	}

  }else {

    $searchKey = isset($_POST['search']) ? $_POST['search'] : (isset($_COOKIE['search']) ? $_COOKIE['search'] : '');
    // Display data from product table 
    $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' AND quantity > 0 ORDER BY id DESC");
    $stmt->execute();
    $rawResult = $stmt->fetchAll();
    $total_pages = ceil(count($rawResult) / $numOfrecord);

    $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' AND quantity > 0 ORDER BY id DESC LIMIT $offset,$numOfrecord");
    $stmt->execute();
    $result = $stmt->fetchAll();
  }
?>

<div class="container">
	<div class="row">
		<div class="col-xl-3 col-lg-4 col-md-5">
			<div class="sidebar-categories">
				<div class="head">Browse Categories</div>
				<ul class="main-categories">
					<li class="main-nav-list">
						<?php
							$categoryStmt = $db->prepare("SELECT * FROM categories ORDER BY id DESC");
							$categoryStmt->execute();
							$categoryResust = $categoryStmt->fetchAll();
						?>
						<?php
							foreach ($categoryResust as $key => $value) { ?>
								<a href="index.php?category_id=<?php echo $value['id']; ?>"><span class="lnr lnr-arrow-right"></span><?php echo escape($value['name']); ?></a>
						<?php } ?>
					</li>
				</ul>
			</div>
		</div>
<div class="col-xl-9 col-lg-8 col-md-7">
<!-- Start Filter Bar -->
<div class="filter-bar d-flex flex-wrap align-items-center">
	<div class="pagination">
		<a href="?page-no=1" class="active">First</a>
		<a href="<?php if ($page_no <= 1) {
              echo '#';
	            }else {
	              echo "?page-no".($page_no-1);
	            } ?>" class="prev-arrow <?php if ($page_no <= 1) { echo 'disabled'; } ?>">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
        </a>
		<a href="#" class="active"><?php echo $page_no; ?></a>
		<a href="<?php 
	          if ($page_no >= $total_pages) {
	            echo '#';
	          } else {
	            echo "?page-no=" . ($page_no + 1);
	          }
	        ?>" class="next-arrow <?php if ($page_no >= $total_pages) { echo 'disabled'; } ?>">
			<i class="fa fa-long-arrow-right" aria-hidden="true"></i>
		</a>
		<a href="?page-no=<?php echo $total_pages; ?>" class="active">Last</a>
	</div>
</div>
<!-- End Filter Bar -->
<!-- Start Best Seller -->
<section class="lattest-product-area pb-40 category-list">
	<div class="row">
		<?php
			if ($result) {
				foreach ($result as $key => $value) { ?>
					<!-- single product -->
				<div class="col-lg-4 col-md-6">
					<div class="single-product">
						<a href="product_details.php?id=<?php echo $value['id']; ?>"><img class="img-fluid" 	src="admin/images/<?php echo escape($value['image']); ?>" style="height: 250px;">
						</a>
						<div class="product-details">
							<h6><?php echo escape($value['name']); ?></h6>
							<div class="price">
								<h6><?php echo escape($value['price']); ?></h6>
							</div>
							<div class="prd-bottom">

								<form action="addtocart.php" method="post">
									<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
									<input type="hidden" name="id" value="<?php echo escape($value['id']); ?>">
									<input type="hidden" name="qty" value="1">

									<div class="social-info">
										<button type="submit" style="display: contents;" class="social-info">
											<span class="ti-bag"></span>
											<p class="hover-text" style="left: 20px;">add to bag</p>
										</button>
									</div>

									<a href="product_details.php?id=<?php echo $value['id']; ?>" class="social-info">
										<span class="lnr lnr-move"></span>
										<p class="hover-text">view more</p>
									</a>
								</form>
							</div>
						</div>
					</div>
				</div>

			<?php
				}
				//code
			}
		?>
	</div>
</section>
<!-- End Best Seller -->

<?php include 'footer.php'; ?>