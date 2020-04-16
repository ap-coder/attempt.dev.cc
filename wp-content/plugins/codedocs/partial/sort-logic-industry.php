<?php
	unset($_GET['page']);

	global $wpdb;
	
	$per_page = 25;
	$offset = 0;

	// Let's create an array for placeholders to insert into a prepared argument
	$prepare_args = array();

	// Let see if there are any arguments to our search;
	if( !empty($_GET) ){

		// Let's check if there's a search string

		if( isset($_GET['s']) && strlen($_GET['s']) > 0 ){
			$query=$_GET['s'];


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
	$orderby = isset($_GET['orderby']) && !empty($_GET['orderby']) ? $_GET['orderby'] : ' t.slug';
	$order = isset($_GET['order']) && !empty($_GET['order']) ? $_GET['order'] : ' DESC';
	$sql .= " WHERE tt.taxonomy = 'portfolio_category'";
	$sql .= " ORDER BY {$orderby} {$order} ";

	if( $per_page ){

		// Let's set a limit of rows
		$sql .= " LIMIT ".$per_page." OFFSET ".$offset;
	}


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

	$direction = isset($_GET['order']) && !empty($_GET['order']) && $_GET['order'] == 'desc' ? 'asc' : 'desc';

	$columns = array(
		't.name' => 'Industry Name',
		'' => 'Shortcode'
	);
