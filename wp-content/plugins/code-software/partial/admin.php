<style>
	<?php include 'styles.php';?>
</style>
<div class="wrap">
	<h2><?php esc_html_e( 'Code Docs' ); ?></h2>
	<form method="POST" action="<?=get_admin_url();?>admin.php?page=codesoftware-settings">
		<div class="card">
			<h1>Languages</h1>
			<hr>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="input-text">Add Supported Langage</label>
						</th>
						<td>
							<div class="flex-row">
								<input type="text" name="language" placeholder="" value="" />
								<div class="btn add"><i class="fa fa-plus"></i></div>
							</div>
						</td>
					</tr>
					<tr>
						<th>
							<label for="input-text">Supported Langages</label>
						</th>
						<td>
							<div id="language-list">
							<?php if( isset($options['language']) && !empty($options['language']) ) foreach( $options['language'] as $language ):?>
							<div class="client-file">
								<div class="right">
									<a class="btn delete"><i class="fa fa-close"></i></a>
								</div>
								<div class="none filename"><span class="name"><input style="display: none" type="text" name="language[]" value="<?php echo $language; ?>"><?php echo $language; ?></span></div>
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
							<input type="submit" name="publish" value="Save" class="button" /><br /><br />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div><!-- .wrap -->
<script>
	jQuery(document).ready(function($){
		$('#language-list').on('click','.btn.delete', function(){
			$(this).closest('.client-file').remove();
		});
		$('.btn.add').on('click', function(){
			var lang = $('input[name="language"]').val();
			$('input[name="language"]').val('');
			$('#language-list').append('\
					<div class="client-file">\
						<div class="right">\
							<a class="btn delete"><i class="fa fa-close"></i></a>\
						</div>\
						<div class="none filename"><span class="name"><input style="display: none" type="text" name="language[]" value="'+lang+'">'+lang+'</span></div>\
					</div>\
				');
		});
	})
</script>
