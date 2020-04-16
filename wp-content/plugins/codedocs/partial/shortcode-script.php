
	<script>
		jQuery(document).ready(function($){
			$('select#language').on('change', function(){

				var select = this;

				var object = {
					language : $(select).val(),
					parent_id : $('#parent_id').val()
				};

				<?php if( isset($atts['product_id']) && strlen($atts['product_id']) > 0 ){
						$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}files` WHERE `product` LIKE '%".$atts['product_id']."%'");
						$langs = $wpdb->get_results("SELECT `language` FROM `{$wpdb->prefix}files` WHERE `product` LIKE '%".$atts['product_id']."%' GROUP BY `language`"); ?>

						object.product = <?php echo $atts['product_id'];?>;
				<?php } elseif( isset($atts['industry_id']) && strlen($atts['industry_id']) > 0 ){ 
						$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}files` WHERE `industry` LIKE '%".$atts['industry_id']."%'");
						$langs = $wpdb->get_results("SELECT `language` FROM `{$wpdb->prefix}files` WHERE `industry` LIKE '%".$atts['industry_id']."%' GROUP BY `language`"); ?>
					
						object.industry = <?php echo $atts['industry_id'];?>;
				<?php } elseif( isset($atts['doc_id']) && strlen($atts['doc_id']) > 0 ){
						$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}files` WHERE `id` = {$row->id}");
						$langs = $wpdb->get_results("SELECT `language` FROM `{$wpdb->prefix}files` WHERE `id` = {$row->id} GROUP BY `language`"); ?>

						object.doc = <?php echo $atts['doc_id']; ?>
				<?php } ?>

				$.ajax({
					url : '<?php echo route('file/changeLanguageDocs')?>',
					type: 'GET',
					dataType: 'html',
					data : object,
					beforeSend: function(){						
						$('#documents').html('');
					},
					success: function(d){
						$('#documents').html(d);
					},
					complete: function(){

					}
				});
			});
		});
	</script>