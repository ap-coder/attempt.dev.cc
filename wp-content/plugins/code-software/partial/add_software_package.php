<style>
	<?php include 'styles.php'; ?>
</style>

<?php
	if (($_POST)) {
	    	// dd($_POST, $_FILES);
	}
?>

<div class="bootstrap-wrapper">
	<div class="wrap">
		<h2><?php esc_html_e( 'Add Software' ); ?></h2>
		<hr>
        
        
        <?php //$postto = include dirname(__DIR__) . '/partial/output.php'; ?>
        <!-- wp-content/plugins/code-software/partial/output.php -->
        
		<form method="POST" action="<?php  echo swroute('file/upload'); ?>" enctype="multipart/form-data">
 
			<div class="card" style="max-width: 80%">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="filename">Software Name</label></th>
							<td>
								<input class="form-control" type="text" name="name" id="name" value="<?php echo isset($software) && issset($software->name) ? $software->name :''; ?>">
							</td>
							<td><?php // echo isset($errors)&&isset($errors['name'])?$errors['name']:'';?></td>
						</tr>

						<tr>
							<th><label for="filename">Software Version</label></th>
							<td>
								<input class="form-control" type="text" name="software_version" id="software_version" value="<?php echo isset($software) && issset($software->software_version) ? $software->software_version :''; ?>">
								<small>Please format this just like you would in git. ex 1.0.0 becomes 1-0-0</small>
							</td>
							<td><?php  echo isset($errors)&& isset($errors['software_version'])?$errors['software_version']:'';?></td>
						</tr>


						<tr>
							<th> <label for="filename">Short Description</label> </th>
							<td>
								<textarea class="form-control" name="sdesc" id="sdesc" cols="30" rows="10"><?php echo isset($software) && issset($software->sdesc) ? $software->sdesc :''; ?></textarea>
							</td>
							<td><?php //echo isset($errors)&&isset($errors['sdesc'])?$errors['sdesc']:'';?></td>
						</tr>

						<tr>
							<th><label for="filename">Long Description</label></th>
							<td>
								<?php
									$settings = array(
									    'teeny' => true,
									    'textarea_rows' => 15,
									    'tabindex' => 1,
		                  				'media_buttons' => false  /* JCM 1/31/2020 */
									);
									wp_editor('', 'ldesc', $settings);
								?>
							</td>
							<td><?php //echo isset($errors)&&isset($errors['ldesc'])?$errors['ldesc']:'';?></td>
						</tr>

						<tr>
							<th> <label for="product">Product</label> </th>
							<td colspan="2"> 
								<input class="form-control" id="products" placeholder="Search Products" />
							</td>
						</tr>


						<tr>
							<th> &nbsp; </th>
							<td valign="top" style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;">
								<h5>Available Products</h5>
								<hr>
								<ul id="product-list list-group" style="max-height: 400px; min-height: 300px; overflow-y: scroll;">
									<?php 
										// Products
										$args = array(
											'post_type'   => 'avada_portfolio',
											'post_status' => 'publish'
										);	
										$query = new WP_Query( $args );
										$products = $query->posts;
										foreach($products as $product):
										?>

									    <!-- <li data-product_id="<?php echo $product->ID; ?>">
									    	<div class="btn btn-select right"><i class="fa fa-plus"></i></div>
									    	<span class="none"><?php echo $product->post_title; ?></span> 
									    </li> -->

									    <li class="list-item d-flex justify-content-between align-items-center" data-product_id="<?php echo $product->ID; ?>">
										    <span style="text-transform: capitalize; font-family: Georgia; font-size: 1.2em;"><?php echo $product->post_title; ?></span> 
									    	<button style="margin-right:10px;"  type="button" class="btn-add btn btn-primary float-right"><i class="fa fa-plus"></i></button>
									    </li>
								  <?php endforeach; ?>
								</ul>
							</td>
							<td style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd; vertical-align:top">
								<h5>Associated Products</h5>
								<hr>
								<ul id="selected-products" class="simple_with_drop list-group"  style="max-height: 400px; min-height: 300px; overflow-y: scroll;"></ul>
							</td>
						</tr>

						<tr>
							<th> <label for="product">Upload</label> </th>
							<td colspan="2" style="text-align: right; ">
								<input type="file" name="file">
							</td>
							<td>&nbsp;</td>
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
								<div class="message-box">
										<?php if( isset($errors) ): ?>
											<?php echo '<pre>'.print_r($errors, 1).'</pre>';?>
										<?php endif;?>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
        
        

		<!-- <h1>ADD SOFTWARE PACKAGE</h1> -->

	</div><!-- .wrap -->
</div>
 
 

<script>

    window.onload = function(){

		let random = Math.floor(Math.random() * 100);
		let num = random;
		// let count = 1;
		document.querySelector("#name").value = "Product " + num;
		document.querySelector("#software_version").value = "1.0." + num;
		document.querySelector("textarea#sdesc").value = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
		document.querySelector("textarea#ldesc").value = "adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur. adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur.";

    }

	function make_array(string){

		var array = new Array();
		jQuery('body').find(string).each(function(i, t)
		{
			array.push(jQuery(t).val());
		}
		);

		return array;
	}

	jQuery(document).ready(function($){

		var $products = $('#product-list li');

		$('#products').keyup(function() {
		  	var re = new RegExp($(this).val(), "i"); // "i" means it's case-insensitive
		  	$products.show().filter(function() 
		  	{
		      	return !re.test($(this).text());
		  	}
		  	).hide();
		});

		$('.btn-add').on('click', function(){
			var label = $(this).closest('li').find('span').text();
			var product_id = $(this).closest('li').data('product_id');
			$(this).closest('li').hide();
			// $('#selected-products').append('<li class="list-item d-flex justify-content-between align-items-center"><span class="right btn btn-unselect"><i class="fa fa-minus"></i></span><span class="none">'+label+'</span><input type="hidden" name="products[]" class="product" value="'+product_id+'" /></li>')
			$('#selected-products').append('<li class="list-item d-flex justify-content-between align-items-center"><span style="text-transform: capitalize; font-family: Georgia; font-size: 1.2em;">'+label+'</span><input type="hidden" name="products[]" class="product" value="'+product_id+'" /> <button style="margin-right:10px;" type="button" class="btn-unselect btn btn-danger float-right"><i class="fa fa-minus"></i></button></li>')

		});

		$('#selected-products').on('click', '.btn-unselect', function(){
			var product_id = $(this).closest('li').find('input').val();
			console.log($('li[data-product_id="'+product_id+'"]'));
			$('li[data-product_id="'+product_id+'"]').show();
			$(this).closest('li').remove();
		});
	});

</script>
 
 				    