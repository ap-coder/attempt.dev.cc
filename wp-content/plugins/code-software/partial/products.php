<?php 
global $wpdb;

// We are going to start a query
$sql = "SELECT SQL_CALC_FOUND_ROWS *, ps.post_title as product_name
			  FROM {$wpdb->prefix}posts as ps WHERE 1=1";

$criteria = array(
	'ps.post_title'
);

include 'sort-logic-product.php'; ?>

<div class="bootstrap-wrapper">

	<div class="wrap">
	<h1 class="wp-heading-inline">Manage Product Software</h1>

	<hr />


<div class="row">
	<div class="col-md-4">
		<div class="tablenav-pages one-page">
		<form action="<?php //echo route('submission/page')?>" method="POST">		
			
			<?php echo add_query_client();?>
			
			<span class="pagination-links">
				<span class="tablenav-pages-navspan" aria-hidden="true" data-page="1">«</span>
				<span class="tablenav-pages-navspan" aria-hidden="true" data-page="<?php echo $current_page != 1 ? $current_page - 1 : $current_page; ?>">‹</span>
				<span class="paging-input">
					<label for="current-page-selector" class="screen-reader-text">Current Page</label>
					<input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $current_page;?>" size="1" aria-describedby="table-paging">
					<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $pages;?></span></span>
				</span>
					<span class="tablenav-pages-navspan" aria-hidden="true" data-page="<?php echo $current_page < $pages ? $current_page + 1 : $current_page; ?>">›</span>
					<span class="tablenav-pages-navspan" aria-hidden="true" data-page="<?php echo $pages;?>">»</span>
			</span>
		</form>
	</div>
</div>
<div class="col-md-4"></div>
<div class="col-md-4">
	<form method="GET" action="<?php echo admin_url(); ?>admin.php?page=software-products" class="inline-form">
		<div class="row">

		    <div class="col">
		      	<input class="form-control" type="search" id="user-search-input" name="s" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? $_GET['s'] : '' ?>">
		    </div>
		    <div class="col">
		      	<input type="submit" id="search-submit" class="button btn btn-primary mb-2" value="Search Files">
		      	<input type="hidden" value="codesoftware-products" name="page">
		    </div>
		</div>
	</form>	

</div>
</div>





	<br class="clear">
  <form action="<?php //route('submission/sort')?>" method="POST">
	<div class="panel" style="margin:0 20px;">	
		<table class="table widefat fixed" cellspacing="0">
			<thead class="thead-dark" style="font-weight: bold; text-transform: uppercase;">
				<tr>
					<?php foreach($columns as $slug => $column):?>
					<th class="manage-column column-columnname sortable <?php echo get_sort_classes($slug); ?>" scope="col" data-state="false" data-col="<?php echo $column; ?>">
						<a style="color: #fff;" href="<?php echo get_admin_url();?>admin.php?page=software-products&<?php echo fill_query_string(array('orderby'=>$slug, 'order'=>$direction))?>"><span><?php echo $column; ?></span><span class="sorting-indicator"></span></a>
					 </th>
					<?php endforeach;?>
					<th class="manage-column column-columnname" scope="col" style="width: 60px;">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($results as $key=>$result):?>
					<tr <?php echo $key % 2 == 0 ? 'class="alternate"':''; ?> data-row="<?php if ( is_object($result) ) { echo $result->ID; } ?>" scope="row">
						<td class="column-columnname">
							<a style="color: #000;font-size: 1.2em;font-weight: bold;text-transform: uppercase;" href="<?php echo site_url( '/wp-admin/admin.php?page=software-products&product_id='.$result->ID, $scheme = null )?>">
								<?php echo stripslashes($result->product_name) ?>
							</a>
						</td>
						<td>[software product_id="<?php echo $result->ID;?>"]</td>
						<td class="column-columnname">
		          <a class="btn btn-primary btn-sm" style="color: #fff; margin-right:10px;" href="<?php echo site_url( '/wp-admin/admin.php?page=software-products&product_id='.$result->ID, $scheme = null )?>">View</a>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		</div>
	</form>
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