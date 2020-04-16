<style>
	<?php  include 'styles.php'; ?>
	.js .tmce-active .wp-editor-area { color: #000; }
</style>
<?php 

	global $wpdb;

	$package = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}software WHERE id={$_GET['software_id']}");
	//$inds = explode(',', $package->industry);
  	if ( is_object($package) )
	$prods		= explode(',', $package->product);

// dd($package);


?>
<div class="bootstrap-wrapper">
	<div class="wrap">
		<h2><?php esc_html_e( 'EDIT Software' ); ?></h2>
		<hr>
		<div class="wp-list-table widefat fixed striped posts">	
			<form method="POST" action="<?= get_admin_url();?>admin.php?page=software-list&software_id=<?php echo $_GET['software_id']?>">
				<div class="card" style="max-width: 80%; overflow-x:auto;">
					<table class="form-table" style="width: 100%;">
						<tbody>
							<tr>
								<th>
									<label for="filename">Software Name</label>
								</th>
								<td>
									<input class="form-control" type="text" name="name" id="name" value="<?php  if ( is_object($package) ) { echo $package->name; } ?>">
								</td>
								<td>&nbsp;</td>
							</tr>

							<tr>
								<th><label for="filename">Software Version</label></th>
								<td>
									<input class="form-control" type="text" name="software_version" id="software_version" value="<?php echo ( is_object($package) ) && isset($package->software_version) ? $package->software_version :''; ?>">
									<small>Please format this just like you would in git. ex 1.0.0 becomes 1-0-0</small>
								</td>
								<td><?php  echo isset($errors)&& isset($errors['software_version'])?$errors['software_version']:'';?></td>
							</tr>

							<tr>
								<th>
									<label for="filename">Short Description</label>
								</th>
								<td>
									<textarea class="form-control" name="sdesc" id="sdesc" cols="30" rows="10"><?php  if ( is_object($package) ) { echo $package->sdesc; } ?></textarea>
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<th>
									<label for="filename">Long Description</label>
								</th>
								<td>
									<!-- <textarea name="ldesc" id="ldesc" cols="30" rows="10"><?php  if ( is_object($package) ) { echo $package->ldesc; } ?></textarea> -->
									<?php
										$settings = array(
										    'teeny' => true,
										    'textarea_rows' => 15,
										    'tabindex' => 1,
			                  				'media_buttons' => false  /* JCM 1/31/2020 */
										);
		                				$editor_content = ( is_object($package) ) ? stripslashes($package->ldesc) : '';

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
									<input class="form-control" id="products" placeholder="Search Products" />
								</td>
							</tr>
							<tr>
								<th>
									&nbsp;
								</th>
								<td valign="top" style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;"> 
									<h5>Available Products</h5>
									<hr>
									<ul id="product-list list-group" style="max-height: 400px; min-height: 300px; overflow-y: scroll;">
										<?php 
											global $wpdb;
											$result = $wpdb->get_results("SELECT `product_id` FROM `{$wpdb->prefix}swprodsoftware` WHERE `software` = {$_GET['software_id']}");
											$assoc_prods = wp_list_pluck( $result, 'product_id' );
											$products = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type='avada_portfolio' AND post_status = 'publish'");
											foreach($products as $product): ?>
									 
										    <li class="list-item d-flex justify-content-between align-items-center"  data-product_id="<?php echo $product->ID; ?>" style="<?php echo in_array($product->ID, $assoc_prods) ? 'display:none' : ''?>">
										    	<span style="text-transform: capitalize; font-family: Georgia; font-size: 1.2em;"><?php echo $product->post_title; ?></span> 
									    	<button style="margin-right:10px;"  type="button" class="btn-add btn btn-primary float-right"><i class="fa fa-plus"></i></button> 
										    </li>
									  <?php endforeach; ?>
									</ul>
								</td>
								<td style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;vertical-align:top">
									<h5>Associated Products</h5>
									<hr>
									<ul id="selected-products" class="simple_with_drop list-group"  style="max-height: 400px; min-height: 300px; overflow-y: scroll;"> 
								 
										<?php 
											$result = $wpdb->get_results("SELECT `product_id` FROM `{$wpdb->prefix}swprodsoftware` WHERE `software` = {$_GET['software_id']}");
											$assoc_prods = wp_list_pluck( $result, 'product_id' );
											$products = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type='avada_portfolio' AND post_status = 'publish'");
											foreach( $products as $product ): 
											if( !in_array($product->ID, $assoc_prods) ) continue; ?>

												<li class="list-item d-flex justify-content-between align-items-center">
													<span style="text-transform: capitalize; font-family: Georgia; font-size: 1.2em;"><?php echo $product->post_title; ?></span>
													<input type="hidden" name="products[]" class="product" value="<?php echo $product->ID; ?>" /> 
													<button style="margin-right:10px;" type="button" class="btn-unselect btn btn-danger float-right"><i class="fa fa-minus"></i></button>
												</li>
										<?php endforeach; ?>
									</ul>
								</td>
							</tr>
					


					<tr>
						<th colspan="3">



							<div id="client-files">

								<?php 

								$file_sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}software WHERE `id` = %d", $_GET['software_id']);

								$file = $wpdb->get_row($file_sql); ?>

								<div class="client-file" data-id="<?php if ( is_object($file) ) { echo $file->id; } ?>">
										
									<div class="flex-row">
											<div class="none filename">
												<span class="name">
												<?php if ( is_object($file) ) { echo $file->filename; } ?>	
												</span>
												<input style="display: none;" type="text" class="form-control" value="<?php  if ( is_object($file) ) { echo $file->filename; } ?>">
											</div>
											<div style="margin-left: auto;">
												<a class="btn edit"><i class="fa fa-edit"></i></a>
												<a class="btn save" style="display: none;"><i class="fa fa-save"></i></a>
                        <?php
                        if ( is_object($file) ) {
                        ?>
							<a class="btn download" href="<?php echo swroute('download/'.$file->id); ?>"><i class="fa fa-download"></i></a>
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
									<a style="cursor: pointer; color: #fff;" class="btn btn-remove delete-doc">
										Delete <i class="fa fa-trash"> </i>
									</a>
									<input style="cursor: pointer; color: #fff; background-color: #68bd7a;" class="btn" type="submit" name="submit" value="Save">
									<?php if( isset($status) ) echo '<p>'.$status.'</p>'; ?>
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
		</div>
	 <h1>EDIT SOFTWARE PACKAGE</h1>
	</div><!-- .wrap -->
</div>
<script>

// $(document).ready(function () {
 //  	$('.custom-file-input').on('change', function() { 
	//    	let fileName = $(this).val().split('\\').pop(); 
	//    	$(this).next('.custom-file-label').addClass("selected").html(fileName); 
	// });
// });


// document.querySelector('.custom-file-input').addEventListener('change',function(e){
//   var fileName = document.getElementById("#inputGroupFile01").files[0].name;
//   var nextSibling = e.target.nextElementSibling
//   nextSibling.innerText = fileName
// });

// $('.custom-file-input').on('change',function(){
//     var fileName = $(this).val();
// })

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
				url : '<?php echo swroute('file/savename');?>',
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

		$('.delete-package').on('click', function(){

			var el = $(this);

			var r = confirm('Are you sure you want to delete this software package? Any language variants of this document will be lost.');

			if( r == true ){

				$.ajax({
					url : '<?php echo swroute('file/delete');?>',
					data : {
						file : <?php echo $_GET['software_id'];?>
					},
					type: 'POST',
					dataType: 'json',
					success: function(d){

						if(d.status == 'success'){
							window.location.href="<?php echo admin_url(); ?>admin.php?page=codesoftware-software";
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

