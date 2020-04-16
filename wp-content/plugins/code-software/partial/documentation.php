<?php
		global $wpdb;
		$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}docs WHERE `product` = ".$atts['product_id']."");
		$langs = $wpdb->get_results("SELECT `language` FROM {$wpdb->prefix}docs WHERE product = {$atts['product_id']}");
		if( $row ){
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE `set` = {$row->id}");
			$langs = wp_list_pluck( $langs, 'language' ); ?>
			<p style="text-align: right;"><select name="language" id="language" style="width: 120px;height: 29px;">
			<?php foreach( $langs as $lang): ?>
				<option value="<?php echo $lang; ?>"><?php echo $lang; ?></option>
			<?php endforeach; ?>
			</select></p>
			<div id="documents">
			<?php foreach($results as $result): ?>
				<div class="docset"><a href="<?php echo get_bloginfo('url').'/portal/file/download/'.$result->id; ?>"><?php echo $result->nicename; ?></a></div>
			<?php endforeach; ?>
			</div>
		<?php } else { ?>
			<h4>There is not software attributed to this product.</h4>
		<?php }
		?>
		<script>
			jQuery(document).ready(function($){
				$('select#language').on('change', function(){
					var select = this;
					$.ajax({
						url : '<?php echo route('file/changeLanguageDocs')?>',
						type: 'GET',
						dataType: 'html',
						data : {
							language : $(select).val(),
							product : <?php echo $atts['product_id'];?>
						},
						beforeSend: function(){						
							$('#documents').html('');
						},
						success: function(d){
							$('#documents').html(d);
						},
						complete: function(){
						}
					})
				})
			});
		</script>