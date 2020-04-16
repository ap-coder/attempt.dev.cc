<style>
	<?php  include 'styles.php'; ?>
</style>

<div class="wpbody-content">
	<div class="wrap">
		<h2><?php esc_html_e( 'Add Software' ); ?></h2>
		<hr>

		<form method="POST" action="<?php echo swroute('file/upload'); ?>" enctype="multipart/form-data">

			<div class="card" style="max-width: 80%">
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="filename">Software Name</label></th>
							<td><input type="text" name="name" id="name" value="<?php echo isset($software) && issset($software->name) ? $software->name :''; ?>"></td>
							<td><?php // echo isset($errors)&&isset($errors['name'])?$errors['name']:'';?></td>
						</tr>

						<tr>
							<th> <label for="filename">Short Description</label> </th>
							<td>
								<textarea name="sdesc" id="sdesc" cols="30" rows="10"><?php echo isset($software) && issset($software->sdesc) ? $software->sdesc :''; ?></textarea>
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
							<td colspan="2"> <input id="products" placeholder="Search Products" /></td>
						</tr>


						<tr>
							<th> &nbsp; </th>
							<td valign="top" style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;">
								<h5>Available Products</h5>
								<ul id="product-list" style="max-height: 400px; min-height: 300px; overflow-y: scroll; vertical-align:top; padding-right: 10px;">
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
									    <li data-product_id="<?php echo $product->ID; ?>">
									    	<div class="btn btn-select right"><i class="fa fa-plus"></i></div>
									    	<span class="none"><?php echo $product->post_title; ?></span> 
									    </li>
								  <?php endforeach; ?>
								</ul>
							</td>
							<td style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd; vertical-align:top">
								<h5>Associated Products</h5>
								<ul id="selected-products" class="simple_with_drop"></ul>
							</td>
						</tr>

						<tr>
							<th>
								<label for="product">Upload</label>
							</th>
							<td colspan="2" style="text-align: right; ">
								<input type="file" name="file">
							</td>
							<td>&nbsp;</td>
						</tr>


						<tr>
							<th>
							</th>
							<td colspan="2" style="text-align: right;">
								<a style="cursor: pointer; color: #aa0000; float: left;" class="delete-doc">Delete</a>
								<input type="submit" name="submit" value="Save">
								<?php if( isset($status) )
									echo '<p>'.$status.'</p>';
								?>
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
	</div><!-- .wrap -->
</div>
<script>
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



