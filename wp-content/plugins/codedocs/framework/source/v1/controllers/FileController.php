<?php

namespace WPMVC;

class FileController extends Controller{

	public function __construct(){

		parent::__construct();
	}

	public function searchPOST(){
		$data = $this->clean_query($_POST);
		wp_redirect(get_admin_url().'admin.php?page=sls-files&'.http_build_query($data));exit;
	}

	public function contractUploadPOST(){

		foreach($_FILES['file']['size'] as $key => $size){

				global $wpdb;

				$user = $wpdb->get_row($sql);

				$uploads_folder = wp_upload_dir();
				
				$uploads_folder = $uploads_folder['basedir']. '/contractfiles';

				if( ! file_exists($uploads_folder) ){
					mkdir($uploads_folder);
				}
				
				$pi = pathinfo($_FILES['file']['name'][$key]);

				$contract_file = sanitize_title( $pi['filename'] );

				$file = $contract_file. '-' . time().'-'.rand(1,100000).'.'.$pi['extension'];

				$destination =  $uploads_folder . '/';

				$moved = move_uploaded_file($_FILES['file']['tmp_name'][$key], $destination . $file);

				if( $moved ){
					
					// Save the image to FileModel

					$Files = new FileModel;

					$data = array(
						'folder'=> $destination,
						'filename' => $file,
						'nicename' => $pi['filename'],
						'type' => 'contract'
					);

					$inserted = $Files->contractSave($data);

				}
		}
	}

	public function uploadPOST(){

		if( empty($_FILES) ){
			wp_redirect( admin_url().'admin.php?page=codedocs-doc-add', $status = 302 );exit;
		}

		$user_id = $_REQUEST['user_id'];

		// Create a set
		$doc_id = $_POST['doc_id'];

		$uploads_folder = wp_upload_dir();
		$uploads_folder = $uploads_folder['basedir']. '/userfiles';

		if( ! file_exists($uploads_folder) ){
			mkdir($uploads_folder);
		}

		if( ! file_exists($uploads_folder . '/doc_sets') ){
			mkdir($uploads_folder . '/doc_sets');
		}
		
		$pi = pathinfo($_FILES['file']['name']);

		$file = $pi['filename']. '-' . time().'-'.rand(1,100000).'.'.$pi['extension'];

		$destination =  $uploads_folder . '/doc_sets/'  . $file;

		$moved = move_uploaded_file($_FILES['file']['tmp_name'], $destination);

		if( $moved ){
			
			// Save the image to FileModel

			$Files = new FileModel;

			$data = array(
				'folder'=> $uploads_folder . '/doc_sets/',
				'filename' => $file,
				'nicename' => $pi['filename'],
				'industry' => $_POST['industry'],
				'product' => $_POST['product'],
				'language' => 'English'
			);

			if( isset($_POST['doc_id']) ){
				$data['parent_id'] = $_POST['doc_id'];
			}

			$inserted = $Files->save($data);

			global $wpdb;
			$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}files WHERE id ='{$inserted}'");

			wp_redirect( admin_url() . 'admin.php?page=codedocs-docs&doc_id='.$inserted); exit;
		}
	}

	public function upload2POST(){
		$this->upload2();
	}

	public function searchFileTableGET(){


		if( is_user_logged_in() && current_user_can( 'manage_options' ) ){


			global $wpdb;

			if( ! $wpdb->get_var("SHOW COLUMNS FROM `{$wpdb->prefix}files` LIKE 'parent_id'") ){
				$wpdb->query("ALTER TABLE `{$wpdb->prefix}files` ADD `parent_id` int(11) DEFAULT NULL");
				echo '<pre>'.print_r('FILE TABLE UPDATED WITH PARENT_ID COLUMN', 1).'</pre>';
				echo '<pre>'.print_r('---------------------------------------------------------------------', 1).'</pre>';
				echo '<pre>'.print_r('---------------------------------------------------------------------', 1).'</pre>';
			}

			global $wpdb;
			$result = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}files");
			echo '<pre>'.print_r($wpdb, 1).'</pre>';

		} else {

		  die('Nothing to see here');
		}

	}

	public function testGET(){

			global $wpdb;
		
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE FIND_IN_SET(13900,product) > 0");
		
			foreach( $results as $result ){
				$products = explode(',',$result->product);
				if( ($key = array_search($_GET['product_id'], $products)) !== FALSE ){
					unset($products[$key]);
				}

				$products = implode(',',$products);
				$wpdb->update($wpdb->files.'files', array('product'=>$products), array('id'=>$result->id), array('%s'), array('%d'));
			}

			die('fire');
	}

	public function upload2(){

		// Create a set
		$doc_id = $_POST['doc_id'];

		if( !empty($_FILES) )
		foreach($_FILES['file']['size'] as $key => $size){

				$uploads_folder = wp_upload_dir();

				$uploads_folder = $uploads_folder['basedir']. '/userfiles';

				if( ! file_exists($uploads_folder) ){
					mkdir($uploads_folder);
				}

				if( ! file_exists($uploads_folder . '/doc_sets') ){
					mkdir($uploads_folder . '/doc_sets');
				}
				
				$pi = pathinfo($_FILES['file']['name'][$key]);

				$file = $pi['filename']. '-' . time().'-'.rand(1,100000).'.'.$pi['extension'];

				$destination =  $uploads_folder . '/doc_sets/'  . $file;

				$moved = move_uploaded_file($_FILES['file']['tmp_name'][$key], $destination);

				if( $moved ){
					
					// Save the image to FileModel

					$Files = new FileModel;

					$data = array(
						'folder'=> $uploads_folder . '/doc_sets/',
						'filename' => $file,
						'nicename' => $pi['filename'],
						'industry' => implode(',', array_unique($_POST['industry'])), 
						'product'=> implode(',', array_unique($_POST['products'])),
						'language' => $_POST['language'][$key]
					);

					if( isset($_POST['doc_id']) ){
						$data['parent_id'] = $_POST['doc_id'];
					}

					$inserted = $Files->save($data);

					global $wpdb;
					$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}files WHERE id ='{$inserted}'");

					//$this->response->success('File uploaded successfully', array('file_id'=>$inserted, 'file'=>$row));
				}
		}
	}

	public function deletePOST(){

		$Files = new FileModel;

		$result = $Files->delete($_POST['file']);

		if($result){
			$this->response->success('Your file was deleted successfully');
		} else {
			$this->response->fail($result, array());
		}
	}

	public function downloadGET(){

		$file = $this->request->params[0];

		$Files = new FileModel;
		
		$pass = false;

		$result = NULL;

		$result = $Files->retrieveFile($file);

		// check if the user is the file owner

		$upload_folder = wp_upload_dir();

		$path = $upload_folder['basedir'] . '/userfiles/doc_sets/'. $result->filename;

		if (file_exists($path) && is_readable($path)) {
			// get the file size and send the http headers
			$size = filesize($path);
			header('Content-Type: application/octet-stream');
			header('Content-Length: '.$size);
			header('Content-Disposition: attachment; filename='.$result->filename);
			header('Content-Transfer-Encoding: binary');
			// open the file in binary read-only mode
			// display the error message if file can't be opened
			$file = @ fopen($path, 'rb');
			
			if ($file) {
				// stream the file and exit the script when complete
				fpassthru($file);
				exit;

			} else {
				$this->response->denied('The file you are looking for cannot be found', 404);
			}

		} else {
			$this->response->denied('The file you are looking for cannot be found', 404);
		}
	}

	public function saveNamePOST(){

		global $wpdb;

		$updated = $wpdb->update($wpdb->prefix.'files', 
			array('nicename'=>$_POST['name']), 
			array('id'=>$_POST['file_id']), 
			array('%s'), array('%d'));


		if( !$updated ){
			$this->response->fail('unable to update filename');
		} else {
			$this->response->success('updated');
		}
	}

	private function clean_query($data){
		foreach($data as $key =>$value){
			if( empty($data[$key]) )
				unset($data[$key]);
		}
		return $data;
	}

	public function createSetPOST(){
		$Documents = new DocumentModel;
		$set_id = $Documents->createSet($_POST);
		wp_redirect( admin_url( 'admin.php?page=codedocs-docsets&set_id=' ).$set_id, $status = 302 );exit;
	}

	public function changeLanguageDocsGET(){

		global $wpdb;

		if( $_GET['product'] ){

			$lang_query = '';
			if( isset($_GET['language']) ){
				$lang_query = " AND language = '{$_GET['language']}'";
			}

			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE FIND_IN_SET({$_GET['product']},product) > 0 ".$lang_query);

			foreach($results as $key => $result){

				if( isset($_GET['language']) ){

					$lang_match = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}files WHERE parent_id = {$result->id} AND language = '".$_GET['language']."'");

					// no language match
					if( $lang_match ){
						$results[$key] = $lang_match;
					// language match
					} 
				}
			}
		}
		elseif( $_GET['industry'] ){

			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE `industry` LIKE '%".$_GET['industry']."%' AND parent_id IS NULL");

			foreach($results as $key => $result){

				if( isset($_GET['language']) ){

					$lang_match = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}files WHERE parent_id = {$result->id} AND language = '".$_GET['language']."'");

					// no language match
					if( $lang_match ){
						$results[$key] = $lang_match;
					// language match
					} 
				}
			}
		} else if( $_GET['doc_id'] ){

			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE `id` = '{$_GET['doc']}' AND parent_id IS NULL");

			foreach($results as $key => $result){

				if( isset($_GET['language']) ){

					$lang_match = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}files WHERE parent_id = {$result->id} AND language = '".$_GET['language']."'");

					// no language match
					if( $lang_match ){
						$results[$key] = $lang_match;
					// language match
					} 
				}
			}
		}

		echo '<div id="documents">';
		foreach($results as $result):
			echo '<div class="docset"><a href="'.get_bloginfo('url').'/portal/file/download/'.$result->id.'">'.$result->nicename.' <span style="font-size: 0.9em">['.$result->language.']</span></a></div>';
		endforeach;
		echo '</div>';

	}
}