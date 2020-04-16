<style>
	<?php  include 'styles.php'; ?>
	.client-file .filename {
    height: auto;
}
</style>

<?php 
	global $wpdb;
	$doc = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}files WHERE id={$_GET['doc_id']}");

	$inds = explode(',', $doc->industry);
	$prods		= explode(',', $doc->product);

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Documentation Set' ); ?></h2>
	<hr>

	<form method="POST" action="<?=get_admin_url();?>admin.php?page=codedocs-docs&doc_id=<?php echo $_GET['doc_id']?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="doc_id" value="<?php echo $_GET['doc_id']; ?>">
		<div class="card" style="max-width: 800px">
			<table class="form-table">
				<tbody>

					<tr>
						<th>
							<label for="filename">Set Name</label>
						</th>
						<td>
							<input type="text" name="name" id="name" value="<?php echo $doc->nicename; ?>">
						</td>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<th>
							<label for="industry">Industry</label>
						</th>
						<td>
							<?php 

							// Industries
							$industries = get_terms( array(
							    'taxonomy' => 'portfolio_category',
							    'hide_empty' => false,
							) );


							foreach( $industries as $key => $industry ): ?>
								<p>
									<label for="<?php echo 'industry-'.$key; ?>">
										<input id="<?php echo 'industry-'.$key; ?>" class="industry" type="checkbox" name="industry[]" <?php echo $industry->term_id && in_array($industry->term_id, $inds) ? ' checked="checked" ' : ''; ?> value="<?php echo $industry->term_id ?>"><?php echo $industry->name; ?>
									</label>
								</p>
							<?php endforeach;?>
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
									foreach($products as $product): ?>
								    <li data-product_id="<?php echo $product->ID; ?>">
								    	<div class="btn btn-select right"><i class="fa fa-plus"></i></div>
								    	<span class="none"><?php echo $product->post_title; ?></span> 
								    </li>
							  <?php endforeach; ?>
							</ul>
						</td>

						<td  style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd; vertical-align:top">

							<h5>Associated Products</h5>
							<ul id="selected-products" class="simple_with_drop">
								<?php 
								// Products
								$args = array(
									'post_type'   => 'avada_portfolio',
									'post_status' => 'publish',
									'post__in' => $prods
								);
								
								$query = new WP_Query( $args );
								$products = $query->posts;
								foreach( $products as $product ): ?>
									<li class="product-<?php echo $product->ID?>"><span class="btn btn-unselect right"><i class="fa fa-minus"></i></span>
											<span class="none"><?php echo $product->post_title; ?></span>
											<input type="hidden" name="products[]" class="product" value="<?php echo $product->ID; ?>" />
									</li>
								<?php endforeach; ?>
							</ul>
						</td>

					</tr>

					<tr>
						<th colspan="3">
							<label>Language Variants</label>
							<div id="client-files">

								<?php 

								$file_sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}files WHERE `parent_id` = %d", $_GET['doc_id']);

								$files = $wpdb->get_results($file_sql);

								foreach($files as $file):?>
									<div class="client-file" data-id="<?php echo $file->id; ?>">
										
										<div class="flex-row">
											<div class="none filename"><span class="name"><?php echo $file->nicename; ?></span><input style="display: none;" type="text" class="form-control" value="<?php echo $file->nicename;?>"></div>
											<div style="margin-left: auto;">
												<a class="btn edit"><i class="fa fa-edit"></i></a>
												<a class="btn save" style="display: none;"><i class="fa fa-save"></i></a>
												<a class="btn download" href="<?php echo route('file/download/'.$file->id); ?>"><i class="fa fa-download"></i></a>
												<a class="btn delete"><i class="fa fa-close"></i></a>
											</div>
										</div>
										<div style="font-size: 0.8em; color: #555; ">
											Language: <?php echo $file->language.' - '; ?><?php echo 'Uploaded: '.date('m/d/Y', strtotime($file->created_at));?>
										</div>
									</div>
								<?php endforeach;?>
							</div>

						</th>
					</tr>

					<tr>
						<th>
							<label for="product">Upload</label>
						</th>
						<td colspan="2" style="text-align: right; ">
							<div class="btn add" id="add-file" style="cursor: pointer;">Add File <i style="font-size: 0.80em;" class="fa fa-plus"></i></div>
						</td>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<th colspan="3">
							
							
							<div id="upload-container">
								

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

<div id="upload-file-template" style="display: none;">
	

	<div class="upload-file">
		<div class="btn close"><i class="fa fa-remove"></i></div>
		<table class="file-upload-table">
			<tr>
				<th>File</th>
				<td><input type="file" name="file[]"></td>
			</tr>
			<tr>
				<th>
					<label for="language">Language</label>
				</th>
				<td>
					<select name="language[]" id="language">
						<option value=""> -- Select -- </option>
						<?php foreach($options['language'] as $lang): ?>
							<option value="<?php echo $lang?>" ><?php echo $lang; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>

	</div>

</div>

<script>

	function make_array(string){

		var array = new Array();
		jQuery('body').find(string).each(function(i, t){
			array.push(jQuery(t).val());
		});

		return array;
	}
	/*
	var myDropzone = new Dropzone("div#dropzone", { 
		url: "<?php echo route('file/upload2')?>",
		params: {
			industry : make_array('input.industry:checked'),
			product  : make_array('input.product'),
			doc_id : <?php echo $_GET['doc_id']; ?>
		},
	  uploadMultiple: true,
	  timeout: 9999999,	
    init: function() {
        this.on("success", function(file, response) {
            console.log(response);
            window.location.reload();
        })
    }
	});

	myDropzone.on('error', function(){

		console.log('fire');
	});

	myDropzone.on('uploadprogress', function(e){

		console.log(e);
	});*/

	jQuery(document).ready(function($){

		$('#add-file').on('click', function(){
			console.log('fire');
			var upload_file_template = $('#upload-file-template').html();
			$(upload_file_template).appendTo('#upload-container')
		});

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

		$('#upload-container').on('click', '.btn.close', function(){
			$(this).closest('.upload-file').remove();
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
				url : '<?php echo route('file/savename');?>',
				data : {
					file_id : file,
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

		$('.client-file').on('click', '.btn.delete', function(){

			var el = $(this);

			var r = confirm("Are you user you want to delete!");

			if (r == true) {
		    $.ajax({
					url : '<?php echo route('file/delete');?>',
					data : {
						file : $(el).closest('.client-file').data('id')
					},
					type: 'POST',
					dataType: 'json',
					success: function(d){
						console.log(d);

						if(d.status == 'success'){
							$(el).closest('.client-file').remove();
						} else {
							$('.message-box').html('<div class="alert alert-warning">'+d.message+'</div>')
						}
					},
					complete: function(d){
						console.log('complete')
					}
				});
			}
		});

		$('.delete-doc').on('click', function(){

			var el = $(this);

			var r = confirm('Are you sure you want to delete this docset? Any language variants of this document will be lost.');

			if( r == true ){

				$.ajax({
					url : '<?php echo route('file/delete');?>',
					data : {
						file : <?php echo $_GET['doc_id'];?>
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
			if( !$('#selected-products').find('li.product-'+product_id).length ){

				$('#selected-products').append('<li class="product-'+product_id+'">\
					<span class="right btn btn-unselect"><i class="fa fa-minus"></i></span>\
					<span class="none">'+label+'</span>\
					<input type="hidden" name="products[]" class="product" value="'+product_id+'" />\
					</li>');

				$(this).closest('li').hide();
			}
		});

		$('#selected-products').on('click', '.btn-unselect', function(){
			var product_id = $(this).closest('li').find('input').val();
			$('#product-list li[data-product_id='+product_id+']').show();
			$(this).closest('li').remove();
		});
	});

</script>

