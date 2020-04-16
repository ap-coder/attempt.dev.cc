<?php 

	$assoc_products_ids = array();

	if( isset($_GET['feature_id']) ){

	$assoc_products = $wpdb->get_results("
		SELECT DISTINCT(post_name), cpf.product_id, p.post_title, p.post_name
	 	FROM wp_codeqr_product_features as cpf
		JOIN {$wpdb->prefix}posts as p ON p.ID = cpf.product_id
		WHERE p.post_type ='avada_portfolio' AND FIND_IN_SET({$_GET['feature_id']},`features`) > 0
	");

	$assoc_products_arr = wp_list_pluck( $assoc_products, 'product_id' );

	$assoc_products_ids = implode(',', $assoc_products_arr);

}?>


<tr>

	<th> &nbsp; </th>

	<td valign="top" style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;">

		<h5 class="assoc">Available Products</h5>

		<ul id="product-list" style="max-height: 400px; min-height: 300px; overflow-y: scroll; vertical-align:top; padding-right: 10px;">

			<?php 

				global $wpdb;

				$products = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE post_type = 'avada_portfolio' AND post_status = 'publish'");

				foreach($products as $doc): ?>

			    <li <?php echo isset( $assoc_products_arr) && is_array( $assoc_products_arr ) && in_array($doc->ID, $assoc_products_arr) ? 'style="display: none;"' : '';?> data-product_id="<?php echo $doc->ID; ?>">
			    	<div class="right btn btn-select"><i class="fa fa-plus"></i></div>
			    	<span class="none"><?php echo $doc->post_title; ?></span> 
			    </li>

		  <?php endforeach; ?>

		</ul>

	</td>

	<td style="width: 50%; background-color: #f9f9f9; box-shadow: 0px 0px 3px 0px inset #ddd;vertical-align:top">

		<h5 class="assoc">Associated Products</h5>


		<ul id="selected-products" class="simple_with_drop">

			<?php if( isset($_GET['feature_id']) && $assoc_products ):

				foreach( $assoc_products as $product ): ?>

					<li>
						<div class="left" style="padding-right: 5px;"><span><i class="fa fa-bars"></i></span></div>
						<div class="none">
							<div class="right btn btn-unselect"><i class="fa fa-minus"></i></div>
							<span class="none"><?php echo $product->post_title; ?></span>
							<input type="hidden" name="products[]" value="<?php echo $product->product_id; ?>" />
						</div>
					</li>

			<?php endforeach; endif; ?>

		</ul>

	</td>

</tr>
