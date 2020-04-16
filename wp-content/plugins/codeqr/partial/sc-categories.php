<?php 

	$href = get_permalink( $post->ID ).'?feature_category=';

	if( $rtc_included ){

		echo '<div class="codeqr_item"><a href="?feature_category=clock_configurator">Clock Configurator</a></div>';
	}

	if( strrpos($post_name, '5000') > 0 ){

		echo '<div class="codeqr_item"><a href="?feature_category=configurator">CR5000AV Configurator</a></div>';
	}

	foreach( $all_the_categories2 as $atc ):

		echo '<div class="codeqr_item"><a href="'.$href.$atc->slug.'">'.$atc->name.'</a></div>';
		
	endforeach;