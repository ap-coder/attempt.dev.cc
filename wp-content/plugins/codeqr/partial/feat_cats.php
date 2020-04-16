<style>
	<?php include 'styles.php';?>
</style>
<div class="wrap">
	<h2><?php esc_html_e( 'Feature Categories' ); ?></h2>
		
	<form method="POST" action="<?=get_admin_url();?>admin.php?page=codeqr-feat_cats">

		<div class="card">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="input-text">Add Feature Categories</label>
						</th>
						<td>
							<div class="flex-row">
								<input type="text" id="feature_category" placeholder="" value="" />
								<div class="btn add"><i class="fa fa-plus"></i></div>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label for="input-text">Supported Feature Categories</label>
						</th>
						<td>
							<div id="feature_categories-list">
								
							<?php 
							if( isset($data) && isset($data['feature_categories']) && !empty($data['feature_categories']) ) 
							foreach( $data['feature_categories'] as $feature_categories ): ?>
							<div class="client-file" data-id="<?php echo $feature_categories->id; ?>">
								<div class="right">
									<a class="btn edit" style="cursor: pointer;"><i class="fa fa-edit"></i></a>
									<a class="btn save"  style="cursor: pointer; display: none;"><i class="fa fa-save"></i></a>
									<a class="btn delete" style="cursor: pointer;"><i class="fa fa-close"></i></a>
								</div>
								<div class="none filename">
									<span class="name">
										<?php echo $feature_categories->name; ?>										
									</span>
									<input style="display: none" type="text" name="feature_categories[]" value="<?php echo $feature_categories->name; ?>">
								</div>
							</div>
							<?php endforeach; ?>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label for="input-time">&nbsp;</label>
						</th>
						<td>
							<input type="submit" name="submit" value="Save" class="button" /><br /><br />
						</td>
					</tr>
				</tbody>
			</table>
		</div>


	</form>

</div><!-- .wrap -->
<script>
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

			$(this).closest('.client-file').find('.filename input').show();
			$(this).closest('.client-file').find('.filename span.name').hide();
			//$(name).hide();
		});

		$('.client-file').on('click', '.btn.save', function(){

			var row = $(this).closest('.client-file');
			var input = $(row).find('input');
			var name = $(row).find('span.name');
			var save_button = $(row).find('.btn.save');
			var edit_button = $(row).find('.btn.edit');

			$.ajax({
				url : '<?php echo qrroute('feature/saveName');?>',
				data : {
					feat_cat_id : $(row).data('id'),
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

			var r = confirm("Are you sure you want to delete?");

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

		$('#feature_categories-list').on('click','.btn.delete', function(){

			$(this).closest('.client-file').remove();
		});

		$('.btn.add').on('click', function(){
			var category = $('#feature_category').val();
			$('#feature_categories-list').append('\
					<div class="client-file">\
						<div class="right">\
							<a class="btn delete"><i class="fa fa-close"></i></a>\
						</div>\
						<div class="none filename"><span class="name"><input style="display: none" type="text" name="feature_categories[]" value="'+category+'">'+category+'</span></div>\
					</div>\
				');
		});

	})
</script>
