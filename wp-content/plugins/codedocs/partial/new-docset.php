<style>

	<?php include 'styles.php';?>
</style>
<div class="wrap">
	<h2><?php esc_html_e( 'New Document' ); ?></h2>
	<hr>
	<div class="card">
		<form action="<?php echo route('file/upload');?>" method="POST" enctype="multipart/form-data">
			<table class="form-table">
				<tbody>

					<tr>
						<th>
							<label for="product">Upload</label>
						</th>
						<td>
								
								<p><input type="file" name="file"></p>

						</td>
					</tr>

					<tr>
						<th>
							&nbsp;
						</th>
						<td>
								<input type="submit" value="Upload" name="submit">
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>

</div><!-- .wrap -->

<?php 

/*$products = wp_list_pluck( $query->posts, 'post_title');

	$products = implode("','", $products);

	$products = "['{$products}']";*/

	
?>

<script>
	

	/*var myDropzone = new Dropzone("div#dropzone", { 
		url: "<?php echo route('file/upload')?>",
	  uploadMultiple: false,
	  timeout: 9999999,	
    init: function() {
        this.on("success", function(file, response) {
            console.log(response);
            //window.location.href="<?php echo admin_url( 'admin.php?page=codedocs-docs' )?>"+'&doc_id='+response.data.file_id
        });
    }

	});

	myDropzone.on('error', function(){
		console.log('fire');
	});

	myDropzone.on('uploadprogress', function(e){
		console.log(e);
	});

	jQuery(document).ready(function($){

		$('.client-file').on('click', '.btn.edit', function(){
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
			var row = $(this).closest('.client-file');
			var file = $(row).data('id');
			var input = $(row).find('input');
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
						$(save_button).hide();
						$(edit_button).show();

						$(input).hide();
						$(name).show();

						$(name).text($(input).val());
						$(name).append('<span class="confirm">Saved!</span>');
						setTimeout(function(){
							$(name).find('.confirm').remove();
						},1000)
					}
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

	});


	*/

</script>