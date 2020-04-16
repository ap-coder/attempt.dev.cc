<?php


	wp_reset_postdata();

	global $wpdb;


	$sql = "SELECT 
	SQL_CALC_FOUND_ROWS *, 
	ps.post_title as product_name,
	(SELECT model_number FROM wp_codeqr_products as cpf WHERE model_number IS NOT NULL AND cpf.product_id = ps.ID LIMIT 1 ) as model_number,
	(SELECT visible FROM wp_codeqr_products as cpf WHERE model_number IS NOT NULL AND cpf.product_id = ps.ID LIMIT 1 ) as visible
  FROM {$wpdb->prefix}posts as ps 
	JOIN wp_codeqr_products as cpf ON cpf.product_id = ps.ID
  WHERE 1=1";
	$per_page = 25;
	$offset = 0;

	$criteria = array(
		'cpf.model_number'
	);
	// Let's create an array for placeholders to insert into a prepared argument
	$prepare_args = array();

	// Let see if there are any arguments to our search;
	if( !empty($_GET) ){

		// Let's check if there's a search string

		if( isset($_GET['product-search']) && strlen($_GET['product-search']) > 0 ){
			$query=$_GET['product-search'];


			// Add the necessary string; We can even search by First and Last name
			$sql .= " AND (";
			$sql .= implode(" LIKE '%%%s%%' OR ", $criteria) ." LIKE '%%%s%%'";
			$sql .= " )";
			for($x=0;$x<count($criteria);$x++){$prepare_args[] = $wpdb->esc_like($query); }
		}

		// If we set a per page limit
		if( isset($_GET['per_page']) ){
			$per_page = $_GET['per_page'];
		}

		// If we select a page of the list
		if( isset($_GET['paged']) && is_numeric($_GET['paged'])){
			$offset = (($per_page) * ($_GET['paged'] -1));
		}
		
	}

	// Set the ORDER
	$orderby = isset($_GET['orderby']) && !empty($_GET['orderby']) ? $_GET['orderby'] : ' ps.post_title';
	$order = isset($_GET['order']) && !empty($_GET['order']) ? $_GET['order'] : ' ASC';
	$sql .= " AND post_type = 'avada_portfolio' AND post_status = 'publish'";
	$sql .= " GROUP BY ps.ID  ORDER BY {$orderby} {$order} 
			  ";

	if( $per_page ){

		// Let's set a limit of rows
		$sql .= " LIMIT ".$per_page." OFFSET ".$offset;
	}

	$sql .= "";


	if( empty($prepare_args) ){

		// we don't need a prepared statement;
		$prepared_sql = $sql;

	} else {

		// Put the sql and placeholder args
		$prepared_sql = $wpdb->prepare($sql, $prepare_args);
	}

	// modify the results based on our search
	$results = $wpdb->get_results($prepared_sql);

	$count = $wpdb->get_var("SELECT FOUND_ROWS() as count");

	if($count <= $per_page){
		$pages = 1;
	} else {
		$pages = ceil(abs($count /$per_page ));
	}
	
	$current_page = isset($_GET['paged']) && is_numeric($_GET['paged']) ? $_GET['paged'] : '1';

	if( $results ){

		global $post;
		echo '<div id="products">';
		echo '<div class="row">';
		$i=1;
		foreach ($results as $key => $pst): setup_postdata($pst); 
			if(!$pst->visible ){continue;}
			$model_number = $pst->model_number;
			ob_start();
			include 'product-result.php';
			echo ob_get_clean();

		endforeach;
		wp_reset_postdata();

	} else {

		echo '<p style="font-size: 18px">Sorry, no matches were found. Please try again.</p>';
	}

	echo '</div>';
	echo '</div>';