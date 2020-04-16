<style>
	<?php  include 'styles.php'; ?>
</style>
<?php 
	global $wpdb;
	$product = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts WHERE ID={$_GET['product_id']}");
?>
<div class="wrap">
	<h2><?php esc_html_e( 'Manage Product Software' ); ?></h2>
	<hr>
	<form method="POST" action="<?=get_admin_url();?>admin.php?page=software-products&product_id=<?php echo $_GET['product_id']?>">
		<div class="card" style="max-width: 800px">
			<table class="form-table">
				<tbody>
					<tr>
						<th colspan="2">
							<h3>Product: <?php echo $product->post_title; ?></h3>
						</th>
					</tr>
					<tr>
						<th>
							<label for="filename">Search Available Software</label>
						</th>
						<td colspan="2">
							<input id="documents" placeholder="Search Software">
						</td>
					</tr>
					<tr>
						<th>
							&nbsp;
						</th>
						<td valign="top" style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;">
							<h5>Available Software</h5>
							<ul id="product-list" style="max-height: 400px; min-height: 300px; overflow-y: scroll; vertical-align:top; padding-right: 10px;">
								<?php 
									global $wpdb;
									$result = $wpdb->get_results("SELECT `software` FROM `{$wpdb->prefix}swprodsoftware` WHERE `product_id` = {$_GET['product_id']}");
									$assoc_firm = wp_list_pluck( $result, 'software' );
									$documents = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}software");
									foreach($documents as $doc): 
										if( in_array($doc->id, $assoc_firm) ) continue; ?>
								    <li data-software_id="<?php echo $doc->id; ?>">
								    	<div class="right btn btn-select"><i class="fa fa-plus"></i></div>
								    	<span class="none"><?php echo $doc->name; ?></span> 
								    </li>
							  <?php endforeach; ?>
							</ul>
						</td>
						<td style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;vertical-align:top">
							<?php $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}swsort WHERE `sort_type`='product' AND `type_id`= ".$_GET['product_id']); ?>
							<h5>Associated Software</h5>
							<ul id="selected-products" class="simple_with_drop">
								<?php 
								// Products								
								$result = $wpdb->get_results("SELECT `software` FROM `{$wpdb->prefix}swprodsoftware` WHERE `product_id` = {$_GET['product_id']}");
								$assoc_firm = wp_list_pluck( $result, 'software' );
								$documents = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}software");
								foreach( $documents as $doc ): if( !in_array($doc->id, $assoc_firm) ) continue; ?>
									<li>
										<div class="left" style="padding-right: 5px;"><span><i class="fa fa-bars"></i></span></div>
										<div class="none">
											<div class="right btn btn-unselect"><i class="fa fa-minus"></i></div>
											<span class="none"><?php echo $doc->name; ?></span>
											<input type="hidden" name="documents[]" value="<?php echo $doc->id; ?>" />
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						</td>
					</tr>
					<tr>
						<th>
						</th>
						<td style="text-align: right;">
							<input type="submit" name="submit" value="Submit">
							<?php if( isset($status) )
								echo '<p>'.$status.'</p>';
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div><!-- .wrap -->
<script>
	jQuery(document).ready(function($){
		$(".simple_with_drop").sortable({
  		handle: 'i.fa-bars'
  	});
		var $products = $('#product-list li');
		$('#documents').keyup(function() {
		  var re = new RegExp($(this).val(), "i"); // "i" means it's case-insensitive
		  $products.show().filter(function() {
		      return !re.test($(this).text());
		  }).hide();
		});
		$('.btn.btn-select').on('click', function(){
			var label = $(this).closest('li').find('span').text();
			var software_id = $(this).closest('li').data('software_id');
			$('#selected-products').append('<li>\
										<div class="left" style="padding-right: 5px;"><span><i class="fa fa-bars"></i></span></div><div class="none"><span class="btn btn-unselect"><i class="fa fa-minus"></i></span>'+label+'<input type="hidden" name="documents[]" value="'+software_id+'" /></div></li>')
		});
		$('#selected-products').on('click', '.btn-unselect', function(){
			$(this).closest('li').remove();
		});
	});
</script>
