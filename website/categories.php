<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Product Categories</title>
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php $currentPage = "categories"; ?>
<!-- currency --> 
	<?php include 'currency.php'; ?>
	<!-- /currency --> 
  <!-- HEADER -->
<?php include 'header.php'; ?>
		<!-- /HEADER -->

		<!-- NAVIGATION -->
<?php include 'navigation.php'; ?>
		<!-- /NAVIGATION -->

		<!-- BREADCRUMB -->
		<div id="breadcrumb" class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-12">
						<h3 class="breadcrumb-header">Categories</h3>
						<ul class="breadcrumb-tree">
							<li class="active">Categories</li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->


  <!-- CATEGORY SECTION --> 
   <!-- once selected it will go to the store and show the filtered products ex. user seleccted keyboard then it will show the keyboard products -->
   <h2 class="text-center mb-4">Categories</h2>
  <section class="section">
    <div class="container">
      <div class="row">
		<div class="col-md-3 col-xs-6">
			<div class="product"> 
				<div class="category">
						<div class="category-img">
							<img src="./img/product01.png" alt="">
						</div>
						<div class="product-body">
						<h3 class="product-category"><a href="store.php">All</a></h3>
					</div>
						</div>
        	</div>
		</div>

        <div class="col-md-3 col-xs-6">
			<div class="product"> 
				<div class="category">
						<div class="category-img">
							<img src="./img/product15.png" alt="">
						</div>
						<div class="product-body">
						<h3 class="product-category"><a href="store.php">Keyboards</a></h3>
					</div>
						</div>
        	</div>
		</div>

        <div class="col-md-3 col-xs-6">
          <div class="product"> 
				<div class="category">
						<div class="category-img">
							<img src="./img/product05.png" alt="">
						</div>
						<div class="product-body">
						<h3 class="product-category"><a href="store.php">Headphones</a></h3>
					</div>
						</div>
        	</div>
        </div>

        <div class="col-md-3 col-xs-6">
          <div class="product"> 
				<div class="category">
						<div class="category-img">
							<img src="./img/product02.png" alt="">
						</div>
						<div class="product-body">
						<h3 class="product-category"><a href="store.php">Monitors</a></h3>
					</div>
						</div>
        	</div>
        </div>

        <div class="col-md-3 col-xs-6">
         <div class="product"> 
				<div class="category">
						<div class="category-img">
							<img src="./img/product09.png" alt="">
						</div>
						<div class="product-body">
						<h3 class="product-category"><a href="store.php">Mice</a></h3>
					</div>
						</div>
        	</div>	
        </div>
      </div>
    </div>
  </section>

 <!-- FOOTER -->
<?php include 'footer.php'; ?>
		<!-- /FOOTER -->
<script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>