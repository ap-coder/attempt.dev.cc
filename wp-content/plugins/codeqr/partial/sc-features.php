<?php
if( isset($category_name) ){

	echo '<h3 style="color: #353535; ">Category: '.$category_name.'</h3>';
}

if( isset($_GET['feature_category']) && $_GET['feature_category'] == 'clock_configurator' ){ ?>

		<div class="codeqr_item">

			<h4 style="font-weight: normal"> Set Real Time Clock (RTC) </h4>

			<p><strong>Description: </strong> <code> Return the RTC time. </code>	</p>

			<div id="qrimage"></div>

		</div>

		<script>
			jQuery(document).ready(function($){
				var unix_timestamp = Math.floor(Date.now() / 1000);
				$('#qrimage').html('<p><img src="<?php echo qrroute('print/timestamp')?>?date='+unix_timestamp+'" alt="" /></p>');
			});
		</script>

<?php } elseif( isset($_GET['feature_category']) && $_GET['feature_category'] == 'configurator' ) {

	ob_start();
	include 'configurator.php';
	echo ob_get_clean();

} else {

	// get all the features
	foreach($features as $feature):

		if( isset( $_GET['feature_category'] ) ){

			$feature_category = $_GET['feature_category'];

			$test = $wpdb->get_row("SELECT FIND_IN_SET('{$feature_category}',categories) as matched, cfc.* FROM wp_codeqr_feat_cats as cfc WHERE feature_id = {$feature->id}");

			if( !$test->matched ) continue;
		}

		?>

		<div class="codeqr_item">

			<h4 style="font-weight: normal"><?php echo stripslashes($feature->feature_name); ?></h4>

			<p><strong>Description: </strong> <code><?php echo $feature->description; ?></code>	</p>

			<p><strong>Code: </strong><code> <?php echo $feature->feature_code; ?></code>	</p>

			<div id="qrimage"><p><img src="<?php echo qrroute('print/image').'?code='.$feature->feature_code; ?>" alt=""></p></div>

		</div>

	<?php endforeach; 

}?>
