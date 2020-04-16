<div class="product-result">
	
	<div class="product-result-content">
		<a href="<?php echo get_permalink( $post->ID ); ?>/?product=<?php echo $pst->ID;?>">
			<div class="thumbnail"><?php echo get_the_post_thumbnail($pst->ID, array(300,300, true)); ?></div>
			<div class="title"><p><strong><?php echo $model_number; ?></strong></p></div>
		</a>
		
	</div>

</div>