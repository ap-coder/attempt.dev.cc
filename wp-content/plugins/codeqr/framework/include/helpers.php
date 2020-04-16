<?php

if( !function_exists('fill_query_string')){
  function fill_query_string($array = array()){
    foreach($_GET as $key =>$value){
      if( empty($_GET[$key]) )
        unset($_GET[$key]);
    }

    $data = array_merge($_GET, $array);
    $data = array_unique($data);
    return http_build_query($data);
  }
}

if( !function_exists('add_query_client') ){
  function add_query_client(){
    $string = '';

    $string .= '<input type="hidden" name="per_page" value="'.(isset($_GET['per_page']) ? $_GET['per_page'] : '25').'">';
    
    $string .= '<input type="hidden" name="paged" value="'.(isset($_GET['paged']) ? $_GET['paged'] : '1').'">';

    $string .= '<input type="hidden" name="s" value="'.(isset($_GET['s']) ? $_GET['s'] : '').'">';

    return $string;
  }
}

if( !function_exists('get_sort_classes')){
  function get_sort_classes($column){

    if( isset($_GET['orderby']) && !empty($_GET['orderby']) && $_GET['orderby'] == $column ){
      if( $_GET['order'] == 'asc' ){
      return 'asc sorted';
      }
        return 'desc sorted';
    }
  }
}

if( !function_exists('qrroute') ){
  function qrroute($path){

    return site_url() . '/'.QRPATHNAME.'/' .ltrim($path, '/');
  }
}

if( !function_exists('secure_route') ){
  function secure_route($path){
    $nonce = SECURITY_NONCE;
    return site_url() . '/'.QRPATHNAME.'/' .ltrim($path, '/').'?_wpnonce='.$nonce;
  }
}

if( !function_exists('array2csv') ){
  function array2csv($array){
    if (count($array) == 0) {
      return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, array_keys(reset($array)));
    foreach ($array as $row) {
       fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
  }
}

if( !function_exists('download_headers') ){
  function download_headers($filename) {
      // disable caching
      $now = gmdate("D, d M Y H:i:s");
      header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
      header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
      header("Last-Modified: {$now} GMT");

      // force download  
      header("Content-Type: application/force-download");
      header("Content-Type: application/octet-stream");
      header("Content-Type: application/download");

      // disposition / encoding on response body
      header("Content-Disposition: attachment;filename={$filename}");
      header("Content-Transfer-Encoding: binary");
  }
}

if( !function_exists('qrgetController') ){
  function qrgetController(){

    global $wp;

    if(isset($wp->query_vars[QRROUTE])){

      $path = explode( '/', trim( $wp->query_vars[QRROUTE], '/' ) );

      $controller = 'index';

      if( count($path) ){

        if( isset($path[0]) ) {
          
          $controller = $path[0];
          
        }
      }

      return $controller;
    }

    return false;
  }
}

if( !function_exists('qrgetMethod') ){
  function qrgetMethod(){

    global $wp;

    if( isset($wp->query_vars[QRROUTE]) ){

      $path = explode( '/', trim( $wp->query_vars[QRROUTE], '/' ) );
        
      $action   = 'index';

      if( count( $path ) ) {
        
        if( isset($path[1]) ) {
          
          $action = $path[1];
          
        }
      }

      return $action;

    } return false;
  }
}

if( !function_exists('qrparseUrl') ){
  function qrparseUrl(){

    global $wp;

    $url = explode( '/', trim( $wp->query_vars[QRROUTE], '/' ) );

    unset($url[array_search(qrgetController(), $url)]);

    foreach($url as $key=>$param)
      if($param == qrgetMethod())
        unset($url[$key]);
    
    $url = array_values($url);

    return $url;
  }
}



if( !function_exists('wpdb_update_in') ){
  function wpdb_update_in( $table, $data, $where, $format = NULL, $where_format = NULL ) {

      global $wpdb;

      $table = esc_sql( $table );
      if( ! is_string( $table ) ) {
          return FALSE;
      }


      $i          = 0;
      $q          = "UPDATE " . $table . " SET ";
      $format     = array_values( (array) $format );
      $escaped    = array();

      foreach( (array) $data as $key => $value ) {
          $f         = isset( $format[$i] ) && in_array( $format[$i], array( '%s', '%d' ), TRUE ) ? $format[$i] : '%s';
          $escaped[] = esc_sql( $key ) . " = " . $wpdb->prepare( $f, $value );
          $i++;
      }

      $q         .= implode( $escaped, ', ' );
      $where      = (array) $where;
      $where_keys = array_keys( $where );
      $where_val  = (array) array_shift( $where );
      $q         .= " WHERE " . esc_sql( array_shift( $where_keys ) ) . ' IN (';


      if( ! in_array( $where_format, array('%s', '%d'), TRUE ) ) {
          $where_format = '%s';
      }

      $escaped = array();

      foreach( $where_val as $val ) {
          $escaped[] = $wpdb->prepare( $where_format, $val );
      }

      $q .= implode( $escaped, ', ' ) . ')';

      $wpdb->query( $q );
      return true;
  }
}

function getSessionErrors(){

  $errors = array();

  if( session_id() && isset($_SESSION['errors']) ){

    $errors = $_SESSION['errors'];
    
    unset($_SESSION['errors']);
  }
  
  return $errors;
}

function getSessionStatus(){

  $errors = array();

  if( session_id() && isset($_SESSION['status']) ){

    $errors = $_SESSION['status'];
    
    unset($_SESSION['status']);
  }
  
  return $errors;

}