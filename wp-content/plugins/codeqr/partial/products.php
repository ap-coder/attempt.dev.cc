<?php 
global $wpdb;

// We are going to start a query
$sql = "SELECT SQL_CALC_FOUND_ROWS *, ps.post_title as product_name
			  FROM {$wpdb->prefix}posts as ps WHERE 1=1";

$criteria = array(
	'ps.post_title'
);


include 'sort-logic-product.php'; ?>
<style>
	.tablenav .tablenav-pages a, .tablenav-pages-navspan {
	    display: inline-block;
	    min-width: 17px;
	    border: 1px solid #ccc;
	    padding: 3px 5px 7px;
	    background: #e5e5e5;
	    font-size: 16px;
	    line-height: 1;
	    font-weight: 400;
	    text-align: center;
	}
	.tablenav-pages-navspan {
	    height: 16px;
	    border-color: #ddd;
	    background: #f7f7f7;
	    color: #a0a5aa;
	}
</style>
<div class="wrap">
<h1 class="wp-heading-inline">Manage Product</h1>

<hr />

<form method="GET" action="<?php echo admin_url(); ?>admin.php?page=codeqr-products">
	<p class="search-box">
		<label class="screen-reader-text" for="user-search-input">Search Products:</label>
		<input type="search" id="user-search-input" name="s" value="<?php echo (isset($_GET['s']) && strlen($_GET['s']) > 0) ? $_GET['s'] : '' ?>">
		<input type="submit" id="search-submit" class="button" value="Search Products">
		<input type="hidden" value="codeqr-products" name="page">
	</p>
</form>


<div class="tablenav-pages one-page">
	<form action="<?php echo qrroute('client/product'); ?> method="POST">		
		
		<?php 
			$string = '';
	    $string .= '<input type="hidden" name="per_page" value="'.(isset($_GET['per_page']) ? $_GET['per_page'] : '25').'">';
	    
	    $string .= '<input type="hidden" name="paged" value="'.(isset($_GET['paged']) ? $_GET['paged'] : '1').'">';

	    $string .= '<input type="hidden" name="s" value="'.(isset($_GET['s']) ? $_GET['s'] : '').'">';
	    echo $string;
		 ?>
		
		<span class="pagination-links">
			<span class="tablenav-pages-navspan" aria-hidden="true" data-page="1">«</span>
			<span class="tablenav-pages-navspan" aria-hidden="true" data-page="<?php echo $current_page != 1 ? $current_page - 1 : $current_page; ?>">&#8249;</span>
			<span class="paging-input">
				<label for="current-page-selector" class="screen-reader-text">Current Page</label>
				<input class="current-page" id="current-page-selector" type="text" name="paged" value="<?php echo $current_page;?>" size="1" aria-describedby="table-paging">
				<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo $pages;?></span></span>
			</span>
				<span class="tablenav-pages-navspan" aria-hidden="true" data-page="<?php echo $current_page < $pages ? $current_page + 1 : $current_page; ?>">&#8250;</span>
				<span class="tablenav-pages-navspan" aria-hidden="true" data-page="<?php echo $pages;?>">»</span>
		</span>
	</form>
</div>

<br class="clear">
<form action="<?php route('submission/sort')?>" method="POST">
	
	<table class="widefat fixed" cellspacing="0">
		<thead>
			<tr>
				<?php foreach($columns as $slug => $column):?>
				<th class="manage-column column-columnname sortable <?php echo get_sort_classes($slug); ?>" scope="col" data-state="false" data-col="<?php echo $column; ?>">
					<a href="<?php echo get_admin_url();?>admin.php?page=codeqr-products&<?php echo fill_query_string(array('orderby'=>$slug, 'order'=>$direction))?>"><span><?php echo $column; ?></span><span class="sorting-indicator"></span></a>
				 </th>
				<?php endforeach;?>
				<th class="manage-column column-columnname" scope="col" style="width: 60px;">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($results as $key=>$result): ?>
				<tr <?php echo $key % 2 == 0 ? 'class="alternate"':''; ?> data-row="<?php echo $result->ID;?>">
					<td class="column-columnname"><?php echo stripslashes($result->product_name) ?></td>
					<td>[codeqr product_id="<?php echo $result->ID;?>"]</td>
					<td class="column-columnname">
	          <a style="color: #00a0d2;" href="<?php echo site_url( '/wp-admin/admin.php?page=codeqr-products&product_id='.$result->ID, $scheme = null )?>">View</a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>

</form>
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