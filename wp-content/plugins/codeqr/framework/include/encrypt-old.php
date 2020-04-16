<?php
if(isset($_POST['data'])) {
	
	session_start();
	
	$_SESSION['pin'] = $_POST['pin'];
	
	$temp_filename = tempnam("/srv/www/encrypt", 'PIN-');
	

	# WHAT DOES THIS THING DO?
	# ------------------------------------------------------------------
	# 1. Create file with special string format H$L( $pin_number )
	# 2. Run Python against that file and create a new file: foobar-main
	# 3. Dump that string into variable
	# ------------------------------------------------------------------

	/*
		SET PIN
	 -----------------------------------------*/
	file_put_contents($temp_filename, "H\$L(".$_POST['pin'].")");
	exec('/srv/www/encrypt/signer -q '.$temp_filename.' '.$temp_filename.'-main');
	$setpin = file_get_contents($temp_filename.'-main');

	/*
		NEW DATA 
	 -----------------------------------------*/
	file_put_contents($temp_filename, $_POST['data']);
	exec('/srv/www/encrypt/signer -q '.$temp_filename.' '.$temp_filename.'-main');
	$newdata = substr(file_get_contents($temp_filename.'-main'), 0, strpos(file_get_contents($temp_filename.'-main'), ')')).")";

	/*
		RESET PIN 
	 -----------------------------------------*/
	file_put_contents($temp_filename, "H\$U(".$_POST['pin'].")");
	exec('/srv/www/encrypt/signer -q '.$temp_filename.' '.$temp_filename.'-main');
	$resetpin = file_get_contents($temp_filename.'-main');

	
	echo $newdata."|".$setpin."|".$resetpin;
	
	unlink($temp_filename);
	unlink($temp_filename.'-main');
}