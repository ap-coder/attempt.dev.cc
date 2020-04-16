<?php


namespace SOFTWARE;

class FileController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function searchPOST()
    {
        $data = $this->clean_query($_POST);

        wp_redirect(get_admin_url() . 'admin.php?page=sls-files&' . http_build_query($data));
        exit;
    }

    //	public function softwareUploadPOST() {
    //
    //		foreach ( $_FILES['file']['size'] as $key => $size ) {
    //
    //			global $wpdb;
    //
    //			$user           = $wpdb->get_row( $sql );
    //			$uploads_folder = wp_upload_dir();
    //			$uploads_folder = $uploads_folder['basedir'] . '/softwarefiles';
    //
    //			if ( ! file_exists( $uploads_folder ) ) {
    //				mkdir( $uploads_folder );
    //			}
    //
    //			$pi            = pathinfo( $_FILES['file']['name'][ $key ] );
    //			$contract_file = sanitize_title_with_dashes( $pi['filename'] );
    //			$file          = $contract_file . '-' . time() . '-' . rand( 1, 100000 ) . '.' . $pi['extension'];
    //			$destination   = $uploads_folder . '/';
    //			$moved         = move_uploaded_file( $_FILES['file']['tmp_name'][ $key ], $destination . $file );
    //
    //
    //			if ( $moved ) {
    //
    //				// Save the image to FileModel
    //				$Files = new FileModel;
    //
    //				$data = array(
    //					'folder'   => $destination,
    //					'filename' => $file,
    //					'nicename' => $pi['filename'],
    //					'type'     => 'software'
    //				);
    //				// d($data);
    //				$inserted = $Files->contractSave( $data );
    //
    //			}
    //
    //		}
    //
    //	}

    public function uploadPOST()
    {
        $errors = $this->validate();
        $date = new \DateTime();
        if (isset($_FILES) && ! empty($_FILES)) {
            if (! empty($errors)) {
                $this->response->redirect(admin_url() . 'admin.php?page=software-add', array( 'errors' => $errors ), false);
            }
            // Create a set
            $uploads_dir = wp_upload_dir();
            $path        = $uploads_dir['basedir'];
            $uploads_folder = $path . '/product_software';

            if (! file_exists($uploads_folder))  @mkdir($uploads_folder, 0777, true);
            $software_name           = sanitize_title_with_dashes($_POST['name']);
            $software_version        = $_POST['software_version'];
            $inline_software_version = sanitize_title_with_dashes($software_version);
            $storedpath = $uploads_folder . '/' . $software_name;
            $software_path = $uploads_folder . '/' . $software_name;

            if (! file_exists($software_path)) @mkdir($software_path, 0777, true);
            
            // 		$path_nfo = pathinfo($_FILES['file']);
            // 		$extension = $pi['extension'];
            // 		$filename = $pi['filename'];
            // 		$today = $date->format('m-d-Y');
            // 		dd( $extension , $filename, $today );

            $pi = pathinfo($_FILES['file']['name']);
            
            $file = sanitize_title_with_dashes($pi['filename']) . '-v' . $inline_software_version . '-' . $date->format('m-d-Y') . '.' . $pi['extension'];
            
            $destination = $storedpath . '/' . $file;
            $moved = move_uploaded_file($_FILES['file']['tmp_name'], $destination);
            if ($moved) {
                $Files = new FileModel;
                $data = array(
                    'name'             => $_POST['name'],
                    'sdesc'            => $_POST['sdesc'],
                    'ldesc'            => $_POST['ldesc'],
                    'folder'           => '/wp-content/uploads/product_software/' . $software_name,
                    'filename'         => $file,
                    'nicename'         => $pi['filename'],
                    'product'          => implode(',', $_POST['products']),
                    'software_version' => $_POST['software_version'],
                );

                $inserted = $Files->save($data);

                if (isset($_POST['products']) && ! empty($_POST['products'])) {
                    global $wpdb;
                    $wpdb->query("
						DELETE FROM {$wpdb->prefix}software
						WHERE `software` = {$inserted}
					");

                    // Save the image to FileModel
                    foreach ($_POST['products'] as $product) {
                        $wpdb->insert($wpdb->prefix . 'swprodsoftware', array(
                            'product_id' => $product,
                            'software'   => $inserted
                        ), array( '%d' ));
                    }
                }
                wp_redirect(admin_url() . 'admin.php?page=software-list&software_id=' . $inserted);
                exit;
            }
        } else {
            $this->response->redirect(admin_url() . 'admin.php?page=software-add', array( 'file' => 'Please provide a file' ), false);
        }
    }
    
    public function deletePOST()
    {
        $Files  = new FileModel;
        $result = $Files->delete($_POST['file']);
        if ($result) {
            $this->response->success('Your file was deleted successfully');
        } else {
            $this->response->fail($result, array());
        }
    }

    public function downloadGET()
    {
        $file   = $this->request->params[0];
        $Files  = new FileModel;
        $pass   = false;
        $result = null;
        $result = $Files->retrieveFile($file);
        // check if the user is the file owner
        $upload_folder = wp_upload_dir();
        $path          = $result->folder . '/' . $result->filename;
        // $path = $upload_folder['basedir'] . '/software/software/'. $result->filename;

        if (file_exists($path) && is_readable($path)) {
            // get the file size and send the http headers
            $size = filesize($path);
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . $size);
            header('Content-Disposition: attachment; filename=' . $result->filename);
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

    public function saveNamePOST()
    {
        global $wpdb;
        $updated = $wpdb->update(
            $wpdb->prefix . 'software',
            array( 'nicename' => $_POST['name'] ),
            array( 'id' => $_POST['file_id'] ),
            array( '%s' ),
            array( '%d' )
        );
        if (! $updated) {
            $this->response->fail('unable to update filename');
        } else {
            $this->response->success('updated');
        }
    }

    private function clean_query($data)
    {
        foreach ($data as $key => $value) {
            if (empty($data[ $key ])) {
                unset($data[ $key ]);
            }
        }

        return $data;
    }

    // public function createSetPOST() {
    // 	$Documents = new DocumentModel;
    // 	$set_id    = $Documents->createSet( $_POST );
    // 	wp_redirect( admin_url( 'admin.php?page=codesoftware-software&set_id=' ) . $set_id, $status = 302 );
    // 	exit;
    // }


    protected function validate()
    {
        $error = array();
        if (isset($_POST['name']) && strlen($_POST['name']) < 1) {
            $error['fname'] = 'Provide a nickname.';
        }
        if (isset($_POST['sdesc']) && strlen($_POST['sdesc']) < 1) {
            $error['sdesc'] = 'Provide a short description.';
        }
        if (isset($_POST['ldesc']) && strlen($_POST['ldesc']) < 1) {
            $error['ldesc'] = 'Provide a long description.';
        }
        if (isset($_POST['software_version']) && strlen($_POST['software_version']) < 1) {
            $error['software_version'] = 'Provide a software_version.';
        }
        if (isset($_POST['folder']) && strlen($_POST['folder']) < 1) {
            $error['folder'] = 'Provide a folder.';
        }
        if (isset($_POST['filename']) && strlen($_POST['filename']) < 1) {
            $error['filename'] = 'Provide a filename.';
        }
        if (isset($_POST['nicename']) && (strlen($_POST['nicename']) == 0 || strlen($_POST['nicename']) > 5)) {
            $error['nicename'] = 'Provide a human readable name.';
        }

        return $error;
    }
}
