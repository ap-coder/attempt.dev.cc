<?php

namespace CODEQR;

class ProdFeatsModel extends Model {

	public function updateFeatCats($data){

		global $wpdb;

		$wpdb->query('TRUNCATE wp_codeqr_categories;');

		$updated_array = array();

		foreach( $data['feature_categories'] as $feat_cat){
			$update_array = array('name'=>$feat_cat, 'slug'=>sanitize_title($feat_cat));
			$inserted = $wpdb->insert('wp_codeqr_categories', $update_array);
		}

		if(!empty($wpdb->last_error)){

			return $wpdb->last_error;

		} else {
			
			return $wpdb->insert_id;
		}
	}

	public function listFeatCats(){

		global $wpdb;

		$results = $wpdb->get_results("SELECT * FROM `wp_codeqr_categories`");

		if(!empty($wpdb->last_error)){

			return $wpdb->last_error;

		} else {
			
			return $results;
		}
	}

	public function save($proposed_products, $proposed_features ){

		global $wpdb;

		// we are editing from the feature view
		if( !is_array($proposed_products) ){
			$proposed_products = trim($proposed_products, ',');
			$proposed_products = explode(',', $proposed_products);
		}

		// in this case proposed_features an int
		$saved_products = $wpdb->get_results("SELECT product_id, id FROM wp_codeqr_product_features WHERE FIND_IN_SET($proposed_features,features) > 0 ");

		// saved products is all the products that have the proposed_feature in it's `features` row 
		if( !empty($saved_products) ){

			$missing = array();

			foreach( $saved_products as $sp ){

				if( !in_array($sp->product_id, $proposed_products) ){

					$missing[] = $sp;
				}
			}

			foreach($missing as $missing_product){

				$missed_product = $wpdb->get_row("SELECT * FROM wp_codeqr_product_features WHERE product_id = {$missing_product->product_id}");
				
				$missing_products_features = explode(',', $missed_product->features);

				if (($key = array_search($proposed_features, $missing_products_features)) !== false) {
				  unset($missing_products_features[$key]);
				}

				$missing_products_features = implode(',', $missing_products_features);

				// update the missing product to not include the feature_id
				$wpdb->update("wp_codeqr_product_features", array('features'=>$missing_products_features), array('product_id'=>$missing_product->product_id), array('%s'), array('%d'));
				
			}

			foreach($proposed_products as $product){

				$new_product = $wpdb->get_row("SELECT * FROM wp_codeqr_product_features WHERE product_id = {$product}");
				
				$new_product_features = explode(',', $new_product->features);
				$new_product_features[] = $proposed_features;
				$new_product_features = array_unique($new_product_features);
				$new_product_features = implode(',', $new_product_features);

				// update the missing product to not include the feature_id
				$wpdb->update("wp_codeqr_product_features", array('features'=>$new_product_features), array('id'=>$new_product->id),  array('%s'), array('%d'));
				
				
			}


		} else {

				foreach( $proposed_products as $pp ){

					$wpdb->insert('wp_codeqr_product_features', array('features'=>$proposed_features, 'product_id'=>$pp), array('%s', '%d'));
				}
		}

		// we are editing from the product view

	}

	public function getCatsByFeature($feature_id){
		
		global $wpdb;
		
		$result = $wpdb->get_var("SELECT categories FROM wp_codeqr_feat_cats WHERE feature_id = {$feature_id}");
		
		if( !empty($wpdb->last_error) ){
			return $wpdb->last_error;
		} else {
			return $result;
		}
	}
}