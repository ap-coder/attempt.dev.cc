<?php

namespace CODEQR;

class FeatureModel extends Model {

	public function save($save_data){

		global $wpdb;
		
		$insert_data = array(
			'feature_name'=> $save_data['feature_name'],
			'feature_code'=> $save_data['feature_code'],
			'description' => $save_data['description']
		);

		if( isset($save_data['template_part']) ){

			$insert_data['template'] = serialize($save_data['template_part']);
		}

		$wpdb->insert('wp_codeqr_features', $insert_data );

		if(!empty($wpdb->last_error)){

			return $wpdb->last_error;

		} else {
			
			return $wpdb->insert_id;
		}
	}

	public function updateRow($sent_data){
		
		global $wpdb;
		
		$update_data = array(
			'feature_name' => $sent_data['feature_name'],
			'feature_code' => $sent_data['feature_code'],
			'description' => $sent_data['description']
		);

		$wpdb->update('wp_codeqr_features', $update_data, array('id'=>$sent_data['feature_id']), array('%s', '%s', '%s'));

		if(!empty($wpdb->last_error)){

			return $wpdb->last_error;

		} else {
			
			return $wpdb->insert_id;
		}
	}

	public function get($feature_id = NULL){

		global $wpdb;

		$where = '';
		if( $feature_id ){
			$where = " WHERE id = {$feature_id}";
		}

		$result = $wpdb->get_results("SELECT * FROM `wp_codeqr_features` {$where}");
		return $result;
	}
}