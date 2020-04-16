<?php

//error_reporting(0);

$param = null;/*

parse_str($_REQUEST['param'], $param);

$_REQUEST = array_merge($_REQUEST, (array)$param);*/

//$_REQUEST['filename'] = date('Y-m-d_H-i-s');

require_once('datamatrix.inc.php');

//$data = $_REQUEST['data_url_type'].':'.$_REQUEST['data_url'];

$data = null;

switch ($_REQUEST['matrix_type']) {

	case 'DATE':
		$data = $_REQUEST['date'];
	break;

  case 'TEXT':
  	$data = $_REQUEST['data_text'];
  break;
  
  case 'URL':
    $data = $_REQUEST['data_url_type'].':'.$_REQUEST['data_url'];
  break;
  
  case 'TEL':
    $data = 'TEL:'.$_REQUEST['data_tel'];
  break;
  
  case 'SMS':
    $data = 'SMSTO:'.$_REQUEST['data_sms_to'].':'.$_REQUEST['data_sms_msg'];
  break;
  
  case 'MAILTO':
    $data = 'MAILTO:'.$_REQUEST['data_email'];
  break;
  
  case 'MATMSG':
    $data = 'MATMSG:TO:'.$_REQUEST['matmsg-email-address'].';SUB:'.$_REQUEST['matmsg-subject'].';BODY:'.$_REQUEST['matmsg-body'].';;';
  break;
  
  case 'VCARD':
    //Embed VCARD data in QR code as-is
    if ($_REQUEST['vcard-type'] == 'vcard') {
      $data = 'BEGIN:VCARD'.
      "\n".'FN:'.$_REQUEST['data_vcard_first_name'].' '.$_REQUEST['data_vcard_last_name'].
      "\n".'N:'.$_REQUEST['data_vcard_last_name'].';'.$_REQUEST['data_vcard_first_name'].
      "\n".'TITLE:'.$_REQUEST['data_vcard_honorific'].
      "\n".'TEL:'.$_REQUEST['data_vcard_tel_number'].
      "\n".'TEL;WORK:'.$_REQUEST['data_vcard_tel_number'].
      "\n".'TEL;FAX:'.$_REQUEST['data_vcard_fax_number'].
      "\n".'EMAIL:'.$_REQUEST['data_vcard_email'].
      "\n".'ORG:'.$_REQUEST['data_vcard_org'].
      "\n".'ADR:;;'.$_REQUEST['data_vcard_street'].';'.$_REQUEST['data_vcard_city'].';'.$_REQUEST['data_vcard_state'].';'.$_REQUEST['data_vcard_postcode'].';'.$_REQUEST['data_vcard_country'].
      "\n".'URL:'.$_REQUEST['data_vcard_url_type'].':'.$_REQUEST['data_vcard_url'].
      "\n".'VERSION:2.1'.
      "\n".'END:VCARD';
    } else {
      $data = 'MEMCARD:N:'.$_REQUEST['data_vcard_last_name'].','.$_REQUEST['data_vcard_first_name'].';'.'TEL:'.$_REQUEST['data_vcard_tel_number'].';EMAIL:'.$_REQUEST['data_vcard_email'].';NOTE:'.$_REQUEST['data_vcard_org'].';ADDR:,,'.$_REQUEST['data_vcard_street'].','.$_REQUEST['data_vcard_city'].','.$_REQUEST['data_vcard_state'].','.$_REQUEST['data_vcard_postcode'].','.$_REQUEST['data_vcard_country'].';URL:'.$_REQUEST['data_vcard_url_type'].$_REQUEST['data_vcard_url'].';;';
    }
    //Link to VCF file for download
    //Still required to program that for download vcard from the client side.
  break;
  
  case 'VCALENDAR':
    $eventstartdate = str_replace(':', '', str_replace('-', '', req('vcalendar-start-date-time'))).'Z';
    $eventenddate   = str_replace(':', '', str_replace('-', '', req('vcalendar-end-date-time'))).'Z';
    $data           = 'BEGIN:VCALENDAR'
    ."\n".'VERSION:2.0'
    ."\n".'BEGIN:VEVENT'
    ."\n".'SUMMARY:'.req('vcalendar-event-name')
    ."\n".'LOCATION;VALUE=URL:http://maps.google.com/maps?q='.req('vcalendar-map-latitude').'%2C'.req('vcalendar-map-longitude').'%28'.req('vcalendar-map-address').'%29'
    ."\n".'DTSTART:'.$eventstartdate
    ."\n".'DTEND:'.$eventenddate
    ."\n".'END:VEVENT'
    ."\n".'BEGIN:VCALENDAR';
  break;
  
  case "MAPS":
    $data = 'http://maps.google.com/maps?q='.$_REQUEST['map_latitude'].'%2C'.$_REQUEST['map_longitude'];
  break;
  
  case "WIFI":
    $data = "WIFI:T:".req('wifi_network_type').";S:".req('wifi_ssid').';P:'.req('wifi_password').';;';
  break;
  
  case "PAYPAL_BUY":
    $data = 
    "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=".req('paypal-buy-account-email-address')
    .'&item_number='.req('paypal-buy-item-product-code')
    .'&item_name='.req('paypal-buy-item-description')
    .'&amount='.req('paypal-buy-item-value')
    .'&currency_code='.req('paypal-buy-currency-code');
  break;
  
  case "SOCIAL_NETWORK":
    $data = $_REQUEST['data_social_network_url_type'].":".$_REQUEST['data_social_network_url'];
  break;
  
  case "TWITTER_STATUS":
    $data = "http://twitter.com/home?status=".$_REQUEST['data_tweet'];
  break;
  
  case "LINKEDIN_SHARE":
    $data = "http://www.linkedin.com/shareArticle?mini=true&url=".$_REQUEST['data_linkedin_type'].":".$_REQUEST['data_linkedin'];
  break;
  
  case "FOURSQUARE":
    $data = "http://foursquare.com/mobile/venue/".$_REQUEST['foursqureID'];
  break;
  
  case "ITUNES_LINK":
    $data = req("itunes_link_url")."?partnerId=partnerId&siteID=siteId";
  break;
  
  case "YOUTUBE":
    $parts    = explode('=', $_REQUEST['data_youtube']);
    $video_id = $parts[1];
    
    if ($_REQUEST['youtube-type'] == 'mobile')
      $data = "http://m.youtube.com/watch?v=".$video_id;
    else
      $data = "http://www.youtube.com/watch?v=".$video_id;
  break;
}

$shape       = DMAT_AUTO;

$encoding    = ENCODING_AUTO;

$modulewidth = 5;

if ($_REQUEST["matrix_size"] != ""){

  $modulewidth = $_REQUEST["matrix_size"];
}

$quietzone = 5;

$color0 = 'white';

$color1 = 'black';

$colorq = 'white';

$outputfile = '';

if ($_REQUEST["fc"] != ""){

  $color1 = "#".$_REQUEST["fc"];
}

if ($_REQUEST["bc"] != ""){

  $color0 = "#".$_REQUEST["bc"];
}

if($_REQUEST['converted'] == 1) {
	$fn = 'H-'.$_REQUEST['filename'].'.jpg';
	//if(isset($_SESSION['filename'])) { unlink(plugin_dir_path( __DIR__ ) . 'dm_code/download/'.$_SESSION['filename']); }
	$_SESSION['filename'] = 'H-'.$_REQUEST['filename'].'.jpg'; 
}
	
if($_REQUEST['converted'] == 2) {
	$fn = 'H-'.$_REQUEST['filename'].'2.jpg';
	//if(isset($_SESSION['filename2'])) { unlink(plugin_dir_path( __DIR__ ) . 'dm_code/download/'.$_SESSION['filename2']); }
	$_SESSION['filename2'] = 'H-'.$_REQUEST['filename'].'2.jpg'; 
}
	
if($_REQUEST['converted'] == 3) {
	$fn = 'H-'.$_REQUEST['filename'].'3.jpg';
	//if(isset($_SESSION['filename3'])) { unlink(plugin_dir_path( __DIR__ ) . 'dm_code/download/'.$_SESSION['filename3']); }
	$_SESSION['filename3'] = 'H-'.$_REQUEST['filename'].'3.jpg'; 
}

$outputfile          = plugin_dir_path( __DIR__ )."dm_code/download/{$fn}";

$fn_high_res         = "dm_high_res.jpg";

$outputfile_high_res = "download/{$fn_high_res}";

// Create and set parameters for the encoder
$DatamatrixFactory = new DatamatrixFactory;

$encoder = $DatamatrixFactory->Create($shape);

$encoder->SetEncoding($encoding);

// Create the image backend (default)
$DatamatrixBackendFactory = new DatamatrixBackendFactory;

$backend = $DatamatrixBackendFactory->Create($encoder);

// By default the module width is 2 pixel so we increase it a bit
$backend->SetModuleWidth($modulewidth);

// Set Quiet zone
$backend->SetQuietZone($quietzone);

// Set other than default colors (one, zero, quiet zone/background)
$backend->SetColor($color1, $color0, $colorq);

// Create the barcode from the given data string and write to output file
try {

  if ($_REQUEST["matrix_block_style"] == "circle"){

    $backend->Stroke($data, $outputfile, true);

  } else {

    $backend->Stroke($data, $outputfile, false);
  }
    
  $modulewidth = 40;
  
  $encoder = $DatamatrixFactory->Create($shape);
  
  $encoder->SetEncoding($encoding);
  
  // Create the image backend (default)
  $backend = $DatamatrixBackendFactory->Create($encoder);
  
  // By default the module width is 2 pixel so we increase it a bit
  $backend->SetModuleWidth($modulewidth);
  
  // Set Quiet zone
  $backend->SetQuietZone($quietzone);
  
  // Set other than default colors (one, zero, quiet zone/background)
  $backend->SetColor($color1, $color0, $colorq);

  header('Content-Type: image/jpg');  

  readfile($outputfile);
}

catch (Exception $e) {

  $errstr = $e->GetMessage();

  echo "Datamatrix error message: $errstr\n";
}