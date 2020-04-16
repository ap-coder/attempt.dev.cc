<?php

namespace CODEQR;

class FeatcatsModel extends Model {

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

		$results = $wpdb->get_results("SELECT * FROM `wp_codeqr_categories` ORDER BY name ASC");

		if(!empty($wpdb->last_error)){

			return $wpdb->last_error;

		} else {
			
			return $results;
		}
	}

	public function save($feature_id, $categories){

		if( is_array($categories) ){
			$categories = implode(',', $categories);
		}

		global $wpdb;
		
		$result = $wpdb->get_row("SELECT * FROM wp_codeqr_feat_cats WHERE feature_id = {$feature_id}");
		
		if( $result ){
			
			$wpdb->update('wp_codeqr_feat_cats', array('categories'=>$categories ), array('feature_id'=>$feature_id), array('%s'), array('%d'));

		} else {
			
			$wpdb->insert('wp_codeqr_feat_cats', array('categories'=>$categories , 'feature_id'=>$feature_id), array('%s', '%d') );
		}

		if(!empty($wpdb->last_error)){

			return $wpdb->last_error;

		} else {
			
			return $wpdb->rows_affected;
		}
	}

	public function update($feature_id, $categories){

		global $wpdb;

		if( is_array($categories) ){

			$wpdb->query("DELETE FROM `wp_codeqr_feat_cats` WHERE `feature_id` = {$feature_id}");

			foreach( $categories as $cat ){

				$wpdb->insert('wp_codeqr_feat_cats', array('categories'=>$cat, 'feature_id'=>$feature_id ), array('%s', '%d'));
			}
		} else {

			$wpdb->query("DELETE FROM `wp_codeqr_feat_cats` WHERE `feature_id`={$feature_id}");
		}

		if(!empty($wpdb->last_error)){

			return $wpdb->last_error;

		} else {
			
			return $wpdb->rows_affected;
		}
	}
	public function getCatsByFeature($feature_id){
		
		global $wpdb;
		
		$result = $wpdb->get_var("SELECT categories FROM wp_codeqr_feat_cats WHERE feature_id = {$feature_id}");
		
		$result = strtolower($result);

		if( !empty($wpdb->last_error) ){
			return $wpdb->last_error;
		} else {
			return trim( $result, ",");
		}
	}
}