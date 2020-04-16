<?php

namespace WPMVC;

class DocumentModel extends Model {

	public function createSet($data){

		global $wpdb;
		$inserted = $wpdb->insert($wpdb->prefix.'docs', array(
			'product' => $data['product'],
			'industry' => $data['industry'],
			'language' => $data['language'],
			'name' => $data['name'],
		));


		return $wpdb->insert_id;
	}

}