<?php 
/* 
Template Name: Config Guide 
*/
get_header();?>
<?php
	# SINGLE PRODUCT
	# =======================================================
	if( isset($_GET['product']) ){
		global $wpdb;
		$row = $wpdb->get_results("SELECT * from {$wpdb->prefix}posts WHERE ID = {$_GET['product']}");
		/* Single Product Found
		 ------------------------------------*/
		if( count($row) ){
			global $post;
			$i=1;
			foreach($row as $key => $pst): 
				setup_postdata($pst);
				ob_start();
				include 'partial/product.php';
				echo ob_get_clean();
			endforeach;
			wp_reset_query();
		/* Single Product Doesn't Exist
		 ------------------------------------*/
		} else {
			echo '<h1>'.get_the_title().'</h1>'; 
			the_content(); ?>
			<div class="container">
				<div class="row">
					<form action="<?php echo get_permalink( $post->ID ); ?>/?product-search" method="GET" id="search-product-form">
						<div id="product-form-container">
							<input type="text" name="product-search" value="" placeholder="Search products..."><input type="submit" value="Search" name="search">		
						</div>
					</form>
				</div>
			</div>
			<?php echo '<p style="font-size: 18px">Sorry but this product does not exist.</p>';
			include 'partial/products.php';
		}
	# SEARCH RESULTS
	# =======================================================
	} elseif( isset($_GET['product-search']) ) {
		echo '<h1>'.get_the_title().'</h1>'; 
		the_content(); ?>
		<div class="container">
			<div class="row">
				<form action="<?php echo get_permalink( $post->ID ); ?>/?product-search" method="GET" id="search-product-form">
					<div id="product-form-container">
						<input type="text" name="product-search" value="" placeholder="Search products..."><input type="submit" value="Search" name="search">		
					</div>
				</form>
			</div>
		</div>
		<?php 
		ob_start();
		include 'partial/products.php';
		echo ob_get_clean();
	# LANDING PAGE - READY FOR SEARCH
	# =======================================================
	} else {
		echo '<h1>'.get_the_title().'</h1>'; 
		the_content(); 
	?>
		<div class="container">
			<div class="row">
				<form action="<?php echo get_permalink( $post->ID ); ?>/?product-search" method="GET" id="search-product-form">
					<div id="product-form-container">
						<input type="text" name="product-search" value="" placeholder="Search products..."><input type="submit" value="Search" name="search">		
					</div>
				</form>
			</div>
		</div>
		<?php 
		ob_start();
		include 'partial/products.php';
		echo ob_get_clean();
	}
get_footer();