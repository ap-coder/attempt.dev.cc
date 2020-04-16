<?php

namespace CODEQR;

use Urlcrypt\Urlcrypt;
class UserModel extends Model
{

	private $public_meta;

	private $public_fields;

	public function __construct(){
		$this->public_meta = array(
			'first_name', 'last_name'
		);
	}

	public function getUsers($data = array()){

		global $wpdb;
		
		$meta = array();

		foreach($this->public_meta as $key){

			$meta[] = "
				(SELECT IFNULL ((SELECT `meta_value` FROM {$wpdb->prefix}usermeta as um
				WHERE um.user_id = u.ID AND meta_key = '{$key}' LIMIT 1 ), NULL )) as {$key}";
		}

		$meta = implode(',',$meta);

		$admins = get_users( array('role'=>'Administrator') );

		$admins = wp_list_pluck($admins,'data');

		$admins = wp_list_pluck($admins, 'ID');		  	            

		$admin_ids = implode(',', $admins);

		$and = array(); $join = ''; $x=1;
		foreach( $data as $key => $value){
			$join .= "JOIN {$wpdb->prefix}usermeta as um{$x} ON um{$x}.user_id = u.ID \r\n";
			$and[] = " (um{$x}.meta_value = '{$value}' AND um{$x}.meta_key = '".strtolower($key)."') ";
		$x++;
		}

		$and = count($and) ? implode(' AND ', $and) : ' 1=1';

		$sql = "
			SELECT 
				u.ID,
				u.user_login, 
				u.user_email, 
				u.user_registered, 
				u.display_name,
				u.user_nicename,
				{$meta}
			FROM {$wpdb->prefix}users as u
			{$join}
			WHERE u.ID != ".get_current_user_id()." AND u.ID NOT IN ({$admin_ids})
		";

		$sql .= ' AND ' . $and;

		$results = $wpdb->get_results($sql);

		return $results;
	}

	public function getPasswordResetToken($email){
		
		return Urlcrypt::encode($email);;
	}

	public function fetchPasswordResetToken($token, $email = NULL){

		global $wpdb;
		
		$user = $wpdb->get_row("SELECT * FROM wp_users WHERE ID = (SELECT user_id FROM wp_usermeta WHERE meta_key = 'reset_password' AND meta_value = '{$token}')");
		
		if( $user ){

			if(!is_null($email) && ($email == $user->user_email)){
				return $token;
			}

			return $token;
		}

		return false;
	}

}