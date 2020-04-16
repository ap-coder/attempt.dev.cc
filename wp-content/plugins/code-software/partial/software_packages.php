<?php
global $wpdb;
// We are going to start a query
$sql = "SELECT SQL_CALC_FOUND_ROWS *,
		sw.nicename as nicename
		FROM {$wpdb->prefix}software
		as sw WHERE 1=1 ";

$criteria = array(
    'sw.name',
    'sw.created_at',
);

include 'sort-logic.php';
?>
<div class="bootstrap-wrapper">
	<div class="wrap">
		<h1 class="wp-heading-inline">Software Packages</h1>
		<hr />
		<div class="row">
			<div class="col-md-4">

	 

<form action="" method="POST">
	<?php echo add_query_client(); ?>
	<div class="tablenav-pages one-page">
	 
		<nav aria-label="Page navigation example">
		  <ul class="pagination">
		    <li class="page-item">
		      <span class="tablenav-pages-navspan page-link" aria-label="First">
		        <span aria-hidden="true" data-page="1">First</span>
		        <span class="sr-only">First</span>
		      </span>
		    </li>
		    <li class="page-item"><span class="tablenav-pages-navspan page-link" aria-label="Previous" data-page="<?php echo $current_page < $pages ? $current_page - 1 : $current_page; ?>">Previous</li>

		    <li class="page-item"><span class="page-link current-page" id="current-page-selector"><?php echo $current_page; ?> <span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $pages; ?></span></a></li>

		    <li class="page-item"><span class="tablenav-pages-navspan page-link" aria-label="Next" data-page="<?php echo $current_page < $pages ? $current_page + 1 : $current_page; ?>">Next</li>
		    <li class="page-item">
		      <span class="tablenav-pages-navspan page-link" href="#" aria-label="Last">
		        <span aria-hidden="true" data-page="<?php echo $pages; ?>">Last</span>
		        <span class="sr-only">Last</span>
		      </span>
		    </li>
		  </ul>
		</nav>
	</div>
</form>
 


 

			</div>
		<div class="col-md-4"></div>
			<div class="col-md-4">

				<form method="GET" action="<?php echo admin_url(); ?>admin.php?page=software-list">
					<div class="form-row">
						<p class="search-box">
							
						<div class="col-md-10 my-1">
							<label class="screen-reader-text" for="user-search-input">Search Files:</label>
							<input class="form-control mb-2" type="search" id="user-search-input" name="s" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? $_GET['s'] : '' ?>">
						</div>
						<div class="col-auto my-1">
							<input type="submit" id="search-submit" class="button btn btn-primary mb-2" value="Search Files"> <input type="hidden" value="software-list" name="page">
						</div>
							
						</p>
					</div>
				</form>
			</div>	
		</div>

				<br class="clear">




		<div class="row">
				<form  method="POST">
				 	<div class="panel" style="margin:0 20px;">
						<table class="table widefat fixed" cellspacing="0">
							<thead class="thead-dark" style="font-weight: bold; text-transform: uppercase;">
								<tr>
									<?php foreach ($columns as $slug => $column): ?>
										<th class="manage-column column-columnname sortable <?php echo get_sort_classes($slug); ?>" scope="col" data-state="false" data-col="<?php echo $column; ?>">
											<a style="color: #fff;" href="<?php echo get_admin_url(); ?>admin.php?page=codesoftware-software&<?php echo fill_query_string(array('orderby' => $slug, 'order' => $direction)) ?>"><span><?php echo $column; ?></span><span class="sorting-indicator"></span></a>
									 	</th>
									<?php endforeach;?>
									<th class="manage-column column-columnname" scope="col" style="width: 60px;">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($results as $key => $result): ?>
									<tr <?php echo $key % 2 == 0 ? 'class="alternate"' : ''; ?> data-row="<?php echo $result->id; ?>"  scope="row">
										<td class="column-columnname">
											<a class="" style="color: #000;font-size: 1.2em;font-weight: bold;text-transform: uppercase;" href="<?php echo site_url('/wp-admin/admin.php?page=software-list&software_id=' . $result->id . '&detail', $scheme = null) ?>">
											<?php echo stripslashes($result->name) ?>
											</a>
										</td>
										<td class="column-columnname"><?php echo stripslashes($result->created_at) ?></td>
										<td class="column-columnname"><?php echo $result->software_version; ?></td>
										<td>[software software_id="<?php echo $result->id; ?>"]</td>

										<td class="column-columnname">
						          <a class="btn btn-primary btn-sm" style="color: #fff;" href="<?php echo site_url('/wp-admin/admin.php?page=software-list&software_id=' . $result->id . '&detail', $scheme = null) ?>">Edit</a>
										</td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</div>
				</form>
		</div>


	</div>
</div>
<script>
	jQuery(document).ready(function($){
		$('#current-page-selector').on('keyup', function(e){
			if( e.keyCode == 13 ){
				$(this).closest('form').submit();
			}
		});

		$('.tablenav-pages-navspan').on('click', function(e){
			var paged = $(this).data('page');
			$(this).closest('form').append('<input type="hidden" name="paged" value="'+paged+'" />');
			$(this).closest('form').submit();
		});


		$('table th.manage-column').on('click', function(){
			var state = $(this).data('state');
			var col = $(this).data('col');
			if(!state){ $(this).closest('form').append('<input type="hidden" name="order" value="asc" /><input type="hidden" name="orderby" value="'+col+'" />') }
			if(state=='asc'){ $(this).closest('form').append('<input type="hidden" name="order" value="desc" /><input type="hidden" name="orderby" value="'+col+'" />') }
			if(state=='desc'){ $(this).closest('form').append('<input type="hidden" name="order" value="asc" /><input type="hidden" name="orderby" value="'+col+'" />') }
		});
	})
</script>