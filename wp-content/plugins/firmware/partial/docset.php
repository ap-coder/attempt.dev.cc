<style>
	<?php  include 'styles.php'; ?>
	.js .tmce-active .wp-editor-area {
    color: #000;
}
</style>

<?php 



	global $wpdb;

	$doc = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}firmware WHERE id={$_GET['firmware_id']}");    
	//$inds = explode(',', $doc->industry);
  if ( is_object($doc) )
	  $prods		= explode(',', $doc->product);

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Firmware' ); ?></h2>
	<hr>

	<form method="POST" action="<?=get_admin_url();?>admin.php?page=firmware-list&firmware_id=<?php echo $_GET['firmware_id']?>">

		<div class="card" style="max-width: 800px">
			<table class="form-table">
				<tbody>

					<tr>
						<th>
							<label for="filename">Firmware Name</label>
						</th>
						<td>
							<input type="text" name="name" id="name" value="<?php  if ( is_object($doc) ) { echo $doc->name; } ?>">
						</td>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<th>
							<label for="filename">Short Description</label>
						</th>
						<td>
							<textarea name="sdesc" id="sdesc" cols="30" rows="10"><?php  if ( is_object($doc) ) { echo $doc->sdesc; } ?></textarea>
						</td>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<th>
							<label for="filename">Long Description</label>
						</th>
						<td>
							<!-- <textarea name="ldesc" id="ldesc" cols="30" rows="10"><?php  if ( is_object($doc) ) { echo $doc->ldesc; } ?></textarea> -->
							<?php
								$settings = array(
							    'teeny' => true,
							    'textarea_rows' => 15,
							    'tabindex' => 1,
                  'media_buttons' => false  /* JCM 1/31/2020 */
								);
                $editor_content = ( is_object($doc) ) ? stripslashes($doc->ldesc) : '';
								wp_editor($editor_content, 'ldesc', $settings);
							?>
						</td>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<th>
							<label for="product">Product</label>
						</th>
						<td colspan="2">
							<input id="products" placeholder="Search Products" /></td>
					</tr>


					<tr>

						<th>

							&nbsp;
						</th>

						<td valign="top" style="    width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;"> <h5>Available Products</h5>
							<ul id="product-list" style="max-height: 400px; min-height: 300px; overflow-y: scroll; vertical-align:top; padding-right: 10px;">
								<?php 
									global $wpdb;
									$result = $wpdb->get_results("SELECT `product_id` FROM `{$wpdb->prefix}fwprodfirmware` WHERE `firmware` = {$_GET['firmware_id']}");

									$assoc_prods = wp_list_pluck( $result, 'product_id' );

									$products = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type='avada_portfolio' AND post_status = 'publish'");
									foreach($products as $product): ?>
								    <li data-product_id="<?php echo $product->ID; ?>" style="<?php echo in_array($product->ID, $assoc_prods) ? 'display:none' : ''?>">
								    	<div class="btn btn-select right"><i class="fa fa-plus"></i></div>
								    	<span class="none"><?php echo $product->post_title; ?></span> 
								    </li>
							  <?php endforeach; ?>
							</ul>
						</td>

						<td style="    width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;vertical-align:top">

							<h5>Associated Products</h5>
							<ul id="selected-products" class="simple_with_drop">
								<?php 

									$result = $wpdb->get_results("SELECT `product_id` FROM `{$wpdb->prefix}fwprodfirmware` WHERE `firmware` = {$_GET['firmware_id']}");

									$assoc_prods = wp_list_pluck( $result, 'product_id' );

									$products = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type='avada_portfolio' AND post_status = 'publish'");
									
									foreach( $products as $product ): 

									if( !in_array($product->ID, $assoc_prods) ) continue; ?>

										<li>

											<span class="right btn btn-unselect"> <i class="fa fa-minus"> </i> </span>

											<span class="none"><?php echo $product->post_title; ?></span>

											<input type="hidden" name="products[]" class="product" value="<?php echo $product->ID; ?>">

										</li>
										
								<?php endforeach; ?>
							</ul>
						</td>

					</tr>

					<tr>
						<th colspan="3">
							<div id="client-files">

								<?php 

								$file_sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}firmware WHERE `id` = %d", $_GET['firmware_id']);

								$file = $wpdb->get_row($file_sql); ?>

								<div class="client-file" data-id="<?php if ( is_object($file) ) { echo $file->id; } ?>">
										
									<div class="flex-row">
											<div class="none filename"><span class="name"><?php if ( is_object($file) ) { echo $file->nicename; } ?></span><input style="display: none;" type="text" class="form-control" value="<?php  if ( is_object($file) ) { echo $file->nicename; } ?>"></div>
											<div style="margin-left: auto;">
												<a class="btn edit"><i class="fa fa-edit"></i></a>
												<a class="btn save" style="display: none;"><i class="fa fa-save"></i></a>
                        <?php
                        if ( is_object($file) ) {
                        ?>
												<a class="btn download" href="<?php echo route('file/download/'.$file->id); ?>"><i class="fa fa-download"></i></a>
                        <?php
                        }
                        ?>
											</div>
										</div>
										<div style="font-size: 0.8em; color: #555; ">
											<?php  if ( is_object($file) ) { echo 'Uploaded: '.date('m/d/Y', strtotime($file->created_at)); } ?>
										</div>
									</div>
							</div>

						</th>
					</tr>
					
					<tr>
						<th>
						</th>
						<td colspan="2" style="text-align: right;">
							<a style="cursor: pointer; color: #aa0000; float: left; " class="delete-doc">Delete</a>
							<input type="submit" name="submit" value="Save">
							<?php if( isset($status) )
								echo '<p>'.$status.'</p>';
							?>
						</td>						
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td colspan="2">
							<div class="message-box"></div>
						</td>
					</tr>

				</tbody>
			</table>
		</div>


	</form>

</div><!-- .wrap -->

<script>

	function make_array(string){

		var array = new Array();
		jQuery('body').find(string).each(function(i, t){
			array.push(jQuery(t).val());
		});

		return array;
	}

	jQuery(document).ready(function($){


		$('.client-file').on('click', '.btn.edit', function(){

			$(this).closest('.client-file').find('.file-language').slideDown();

			var row = $(this).closest('.client-file');
			var file = $(row).data('id');
			var input = $(row).find('input');
			var name = $(row).find('span.name');
			var save_button = $(row).find('.btn.save');
			var edit_button = $(row).find('.btn.edit');

			$(save_button).show();
			$(edit_button).hide();

			$(input).show();
			$(name).hide();
		});

		$('.client-file').on('click', '.btn.save', function(){

			$('.file-language').slideUp();

			var row = $(this).closest('.client-file');
			var file = $(row).data('id');
			var input = $(row).find('input');
			var select = $(row).find('select');
			var name = $(row).find('span.name');
			var save_button = $(row).find('.btn.save');
			var edit_button = $(row).find('.btn.edit');


			$.ajax({
				url : '<?php echo fwroute('file/savename');?>',
				data : {
					file_id : file,
					language: $(select).val(),
					name : $(input).val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(d){
					console.log(d);

					if(d.status == 'success'){
						$(name).append('<span class="confirm">Saved!</span>');
						setTimeout(function(){
							$(name).find('.confirm').remove();
						},1000)
					}

					$(save_button).hide();
					$(edit_button).show();

					$(input).hide();
					$(name).show();

					$(name).text($(input).val());
				},
				complete: function(d){
					console.log('complete')
				}
			});
		});

		$('.delete-doc').on('click', function(){

			var el = $(this);

			var r = confirm('Are you sure you want to delete this docset? Any language variants of this document will be lost.');

			if( r == true ){

				$.ajax({
					url : '<?php echo fwroute('file/delete');?>',
					data : {
						file : <?php echo $_GET['firmware_id'];?>
					},
					type: 'POST',
					dataType: 'json',
					success: function(d){

						if(d.status == 'success'){
							window.location.href="<?php echo admin_url(); ?>admin.php?page=codedocs-docs";
						} else {
							$('.message-box').html('<div class="alert alert-warning">'+d.message+'</div>')
						}
					},
					complete: function(){

					}
				});
			}
		});

		var $products = $('#product-list li');

		$('#products').keyup(function() {
		  var re = new RegExp($(this).val(), "i"); // "i" means it's case-insensitive
		  $products.show().filter(function() {
		      return !re.test($(this).text());
		  }).hide();
		});

		$('.btn.btn-select').on('click', function(){
			var label = $(this).closest('li').find('span').text();
			var product_id = $(this).closest('li').data('product_id');
			$(this).closest('li').hide();
			$('#selected-products').append('<li><span class="right btn btn-unselect"><i class="fa fa-minus"></i></span><span class="none">'+label+'</span><input type="hidden" name="products[]" class="product" value="'+product_id+'" /></li>')
		});

		$('#selected-products').on('click', '.btn-unselect', function(){
			var product_id = $(this).closest('li').find('input').val();
			console.log($('li[data-product_id="'+product_id+'"]'));
			$('li[data-product_id="'+product_id+'"]').show();
			$(this).closest('li').remove();
		});
	});

</script>

