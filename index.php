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
      // Display data from product table 
    $stmt = $db->prepare("SELECT * FROM products ORDER BY id DESC");
    $stmt->execute();
    $rawResult = $stmt->fetchAll();
    $total_pages = ceil(count($rawResult) / $numOfrecord);

    $stmt = $db->prepare("SELECT * FROM products ORDER BY id DESC LIMIT $offset,$numOfrecord");
    $stmt->execute();
    $result = $stmt->fetchAll();
  }else {

    $searchKey = $_POST['search'] ? $_POST['search'] : $_COOKIE['search'];
    // Display data from product table 
    $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' ORDER BY id DESC");
    $stmt->execute();
    $rawResult = $stmt->fetchAll();
    $total_pages = ceil(count($rawResult) / $numOfrecord);

    $stmt = $db->prepare("SELECT * FROM products WHERE name LIKE '%$searchKey%' ORDER BY id DESC LIMIT $offset,$numOfrecord");
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
								<a href="#" data-toggle="collapse"><span class="lnr lnr-arrow-right"></span><?php echo escape($value['name']); ?></a>
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
						<img class="img-fluid" src="admin/images/<?php echo escape($value['image']); ?>" style="height: 250px;">
						<div class="product-details">
							<h6><?php echo escape($value['name']); ?></h6>
							<div class="price">
								<h6><?php echo escape($value['price']); ?></h6>
							</div>
							<div class="prd-bottom">

								<a href="" class="social-info">
									<span class="ti-bag"></span>
									<p class="hover-text">add to bag</p>
								</a>
								<a href="" class="social-info">
									<span class="lnr lnr-move"></span>
									<p class="hover-text">view more</p>
								</a>
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