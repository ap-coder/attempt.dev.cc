<?php
namespace FIRMWARE;

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
		
		$date = new \DateTime();

		$errors = $this->validate();
		if( isset($_FILES) && !empty($_FILES) ){
			if( !empty($errors) ){
				$this->response->redirect(admin_url().'admin.php?page=firmware-add', array('errors'=>$errors), false);
			}
			// Create a set
			$uploads_folder = wp_upload_dir();
			$uploads_folder = $uploads_folder['basedir']. '/firmware';
			if( ! file_exists($uploads_folder) ){
				mkdir($uploads_folder);
			}
			/**
			 * TODO REMOVE THIS BECUASE ITS CAUSING /FIRMWARE/FIRMWARE AS THE PATHWAY
			 */
			if( ! file_exists($uploads_folder . '/firmware') ){
				mkdir($uploads_folder . '/firmware');
			}

			$pi = pathinfo($_FILES['file']['name']);

			$file = sanitize_title_with_dashes( $pi['filename']. '-' . $date->format( 'm-d-Y' ) . '.'.$pi['extension']);
			$destination =  $uploads_folder . '/firmware/'  . $file;
			$moved = move_uploaded_file($_FILES['file']['tmp_name'], $destination);
			if( $moved ){
				$Files = new FileModel;
				$data = array(
					'name' => $_POST['name'],
					'sdesc' => $_POST['sdesc'],
					'ldesc' => $_POST['ldesc'],
					'folder'=> $uploads_folder . '/firmware/',
					'filename' => $file,
					'nicename' => $pi['filename'],
					'product' => implode(',', $_POST['products']),
				);
				$inserted = $Files->save($data);
				if( isset($_POST['products']) && !empty($_POST['products']) ){
					global $wpdb;
					$wpdb->query("
						DELETE FROM {$wpdb->prefix}firmware
						WHERE `firmware` = {$inserted}
					");
					// Save the image to FileModel 
					foreach( $_POST['products'] as $product) {
						$wpdb->insert($wpdb->prefix.'fwprodfirmware', array('product_id'=>$product, 'firmware'=>$inserted), array('%d'));	
					} 
				}
				wp_redirect( admin_url() . 'admin.php?page=firmware-list&firmware_id='.$inserted); exit;
			}
		} else {
			$this->response->redirect(admin_url().'admin.php?page=firmware-add',array('file'=>'Please provide a file'), false);
		}
	}
	
	public function upload2POST(){
		$user_id = $_REQUEST['user_id'];
		// Create a set
		$firmware_id = $_POST['firmware_id'];
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
						'industry' => $_POST['industry'],
						'product' => $_POST['product'],
						'language' => 'English'
					);
					if( isset($_POST['firmware_id']) ){
						$data['parent_id'] = $_POST['firmware_id'];
					}
					$inserted = $Files->save($data);
					global $wpdb;
					$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}files WHERE id ='{$inserted}'");
					$this->response->success('File uploaded successfully', array('file_id'=>$inserted, 'file'=>$row));
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
		$path = $upload_folder['basedir'] . '/firmware/firmware/'. $result->filename;
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
		$updated = $wpdb->update($wpdb->prefix.'firmware', 
			array('nicename'=>$_POST['name']), 
			array('id'=>$_POST['file_id']), array('%s'), array('%d'));
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
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}files WHERE `product` LIKE '%".$_GET['product']."%' AND parent_id IS NULL");
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
		} else if( $_GET['firmware_id'] ){
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
			echo '<div class="docset"><a href="'.get_bloginfo('url').'/portal/file/download/'.$result->id.'">'.$result->nicename.'</a></div>';
		endforeach;
		echo '</div>';
	}
	protected function validate(){
		$error = array();
		if( isset($_POST['name']) && strlen($_POST['name']) < 1 ){
			$error['fname'] = 'Provide a nickname.';
		}
		if( isset($_POST['sdesc']) && strlen($_POST['sdesc']) < 1 ){
			$error['sdesc'] = 'Provide a short description.';
		}
		if( isset($_POST['ldesc']) && strlen($_POST['ldesc']) < 1 ){
			$error['ldesc'] = 'Provide a long description.';
		}
		/*if( isset($_POST['folder']) && strlen($_POST['folder']) < 1 ){
			$error['folder'] = 'Provide a folder.';
		}
		if( isset($_POST['filename']) && strlen($_POST['filename']) < 1 ){
			$error['filename'] = 'Provide a filename.';
		}
		if( isset($_POST['nicename']) && (strlen($_POST['nicename']) == 0 || strlen($_POST['nicename']) > 5) ){
			$error['nicename'] = 'Provide a human readable name.';
		}*/
		return $error;
	}
}