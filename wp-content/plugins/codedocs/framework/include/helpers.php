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

    if( isset($_GET['per_page'])):
    $string .= '<input type="hidden" name="per_page" value="'.$_GET['per_page'].'">';
    endif;
    
    if( isset($_GET['paged'])):
    $string .= '<input type="hidden" name="paged" value="'.$_GET['paged'].'">';
    endif;
    
    if( isset($_GET['s'])):
    $string .= '<input type="hidden" name="s" value="'.$_GET['s'].'">';
    endif;    

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
function route($path){

  return site_url() . '/'.PATHNAME.'/' .ltrim($path, '/');
}

function secure_route($path){
  $nonce = SECURITY_NONCE;
  return site_url() . '/'.PATHNAME.'/' .ltrim($path, '/').'?_wpnonce='.$nonce;
}

function array_isolate(&$arr, $key)
{
    $ret = $arr[$key];
    unset($arr[$key]);
    return $ret;
}

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

function getController(){

  global $wp;

  if(isset($wp->query_vars[ROUTE])){

    $path = explode( '/', trim( $wp->query_vars[ROUTE], '/' ) );

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

function getMethod(){

  global $wp;

  if( isset($wp->query_vars[ROUTE]) ){

    $path = explode( '/', trim( $wp->query_vars[ROUTE], '/' ) );
      
    $action   = 'index';

    if( count( $path ) ) {
      
      if( isset($path[1]) ) {
        
        $action = $path[1];
        
      }
    }

    return $action;

  } return false;
}

function parseUrl(){

  global $wp;

  $url = explode( '/', trim( $wp->query_vars[ROUTE], '/' ) );

  unset($url[array_search(getController(), $url)]);

  foreach($url as $key=>$param)
    if($param == getMethod())
      unset($url[$key]);
  
  $url = array_values($url);

  return $url;
}

function sanitize_slug($slug){
  
  $slug = explode('-', $slug);
  
  $arr = array();
  
  foreach($slug as $s){

    $arr[] = ucfirst($s);

  }
  
  return implode(' ', $arr);
}

function reset_permalinks(){

  global $wp_rewrite;

  //Write the rule
  $wp_rewrite->set_permalink_structure('/%postname%/');

  //Set the option
  update_option( "rewrite_rules", FALSE ); 

  //Flush the rules and tell it to write htaccess
  $wp_rewrite->flush_rules( true );
}

function fifty_states(){
  return array('Alabama' => 'AL',
  'Alaska' =>  'AK',
  'Arizona' => 'AZ',
  'Arkansas' =>  'AR',
  'California' =>  'CA',
  'Colorado' =>  'CO',
  'Connecticut' => 'CT',
  'Delaware' =>  'DE',
  'Florida' => 'FL',
  'Georgia' => 'GA',
  'Hawaii' =>  'HI',
  'Idaho' => 'ID',
  'Illinois' =>  'IL',
  'Indiana' => 'IN',
  'Iowa' =>  'IA',
  'Kansas' =>  'KS',
  'Kentucky' =>  'KY',
  'Louisiana' => 'LA',
  'Maine' => 'ME',
  'Maryland' =>  'MD',
  'Massachusetts' => 'MA',
  'Michigan' =>  'MI',
  'Minnesota' => 'MN',
  'Mississippi' => 'MS',
  'Missouri' =>  'MO',
  'Montana' => 'MT',
  'Nebraska' =>  'NE',
  'Nevada' =>  'NV',
  'New Hampshire' => 'NH',
  'New Jersey'  => 'NJ',
  'New Mexico'  => 'NM',
  'New York'  => 'NY',
  'North Carolina' =>  'NC',
  'North Dakota' =>  'ND',
  'Ohio'  => 'OH',
  'Oklahoma'  => 'OK',
  'Oregon'  => 'OR',
  'Pennsylvania'  => 'PA',
  'Rhode Island'  => 'RI',
  'South Carolina'  => 'SC',
  'South Dakota'  => 'SD',
  'Tennessee' => 'TN',
  'Texas' => 'TX',
  'Utah'  => 'UT',
  'Vermont' => 'VT',
  'Virginia'  => 'VA',
  'Washington'  => 'WA',
  'West Virginia' => 'WV',
  'Wisconsin' => 'WI',
  'Wyoming' => 'WY');
}

function get_client_ip(){
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}

function form_control($type, $name, $map, $mode = 'normal', $array = array()){
  $string = '';
  $value = isset($map) ? ( isset($map[$name]) ? $map[$name] : '') : '';
  $nameattr = $mode == 'normal' ? 'name="'.$name.'"' : 'data-'.$mode.'="'.$name.'"';
  switch ($type) {

    case 'email':
    case 'number':
    case 'text':
      $string .= '<input type="'.$type.'" '.$nameattr.' value="'.$value.'">';
      break;

    case 'select':
      $string .= '<select '.$nameattr.' id="'.$name.'">';
      foreach($array as $key=>$arr){
        $selected = $key == $value ? 'selected="selected"' : '';
        $string .= '<option value="'.$arr.'" '.$selected.'>'.$key.'</option>';
      }
      $string .= '</select>';

    case 'textarea':
      $string .= '<textarea '.$nameattr.' id="'.$name.'">'.$value.'</textarea>';
      break;

    default:
      $string .= '';
      break;
  }
  return $string;
}

function image_url($subpath){

  echo site_url() . '/images/'.$subpath;
}

function get_full_name($user_id =NULL){

  if(is_null($user_id)){
    $user_id = get_current_user_id();
  }

  global $wpdb;

  $name = $wpdb->get_row("
      SELECT
          m1.meta_value AS firstname,
          m2.meta_value AS lastname
      FROM wp_users u1
      JOIN wp_usermeta m1 ON (m1.user_id = u1.id AND m1.meta_key = 'first_name')
      JOIN wp_usermeta m2 ON (m2.user_id = u1.id AND m2.meta_key = 'last_name')
      WHERE u1.ID = {$user_id}
  ");

  return $name;
}

function array_strain($data, $keys){

    $ret = array();
    foreach( $data as $key => $value){

        if( in_array($key, $keys) ){
          $ret[$key] = $value;
        }
    }
    return $ret;
}

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
