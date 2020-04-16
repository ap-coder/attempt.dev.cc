<?php

# Displays a usage message for the program.
#------------------------------------------------------------------------------------------------------------------------------
function displayUsage($msg = null) {
	global $programName;
	
	if ($msg) {
		print(PHP_EOL);
		print('*** ' . $msg . PHP_EOL);
		print(PHP_EOL);
	}

	print('Usage: ' . $programName . ' <cmd>' . PHP_EOL);
	print(PHP_EOL);
	print('cmd: CFLKXLK<pin> | CFLKXUK<pin> | H$L(<pin>) | H$U(<pin>)' . PHP_EOL);
	print('  CFLKXLK : 82x-family Lock command' . PHP_EOL);
	print('  CFLKXUK : 82x-family Unlock command' . PHP_EOL);
	print('  H$L(..) : 8x-family Lock command' . PHP_EOL);
	print('  H$U(..) : 8x-family Unlock command' . PHP_EOL);
	print('  <pin>   : PIN with 4-8 decimal digits' . PHP_EOL);
	print(PHP_EOL);
	print('EXAMPLE: ' . PHP_EOL);
	print($programName . ' CFLKXLK123456' . PHP_EOL);
	print('CFLKXLKuGt16b373e9d6408c911a0d483813c890d40e7a4f65@uC' . PHP_EOL);
	print(PHP_EOL);
	print('encryptSigner Version 0.1' . PHP_EOL);
	print(PHP_EOL);
} 

# Compares command string to lock/unlock command mnemonics and if recognized, returns the relevant command's information;
# otherwise, returns None.
#------------------------------------------------------------------------------------------------------------------------------
function determineCommand($command) {
	// global $allCommandsInfo;


	# Global variables/constants
	$programName			= 'encryptSigner.php';

	# PIN length range limits
	$pinMinLength		= 4;
	$pinMaxLength		= 8;

	# 8x-family constants
	$family8x			= '8x';
	$lock8xCmd			= 'H$L';
	$unlock8xCmd	    = 'H$U';
	$start8xDelimiter	= '(';
	$end8xDelimiter		= ')';
	# 8x command info      Mnemonic     Start Delimiter   End Delimiter   Family
	$lock8x				= [$lock8xCmd,  $start8xDelimiter, $end8xDelimiter, $family8x];
	$unlock8x			= [$unlock8xCmd, $start8xDelimiter, $end8xDelimiter, $family8x];

	# 82x-family constants
	$family82x			= '82x';
	$lock82xCmd			= 'CFLKXLK';
	$unlock82xCmd		= 'CFLKXUK';
	$start82xDelimiter	= null;
	$end82xDelimiter		= null;
	# 82x command info     Mnemonic      Start Delimiter    End Delimiter    Family
	$lock82x			= [$lock82xCmd,   $start82xDelimiter, $end82xDelimiter, $family82x];
	$unlock82x			= [$unlock82xCmd, $start82xDelimiter, $end82xDelimiter, $family82x];

	# Combined command information used for command parsing and manipulation (tuple of tuples)
	$allCommandsInfo		= [ $lock82x, $unlock82x, $lock8x, $unlock8x ];


	$command = trim($command);
	foreach ($allCommandsInfo as $cmdInfo) {
		$cmdMnemonic = $cmdInfo[0];
		$startDelimiter = $cmdInfo[1];
		if (!$startDelimiter) {
			if (substr_compare($cmdMnemonic, $command, 0, strlen($cmdMnemonic)) == 0) {
				return $cmdInfo;
			}
		} else if (substr_compare($cmdMnemonic . $startDelimiter, $command, 0, strlen($cmdMnemonic) + strlen($startDelimiter)) == 0) {
			return $cmdInfo;
		}
	}
	return null;
}

# Extracts a command's PIN after skipping over the command's mnemonic and, if present, using the PIN's delimiters.
#------------------------------------------------------------------------------------------------------------------------------
function extractPin($cmd, $cmdInfo) {
	$cmdMnemonic = $cmdInfo[0];
	$startDelimiter = $cmdInfo[1];
	$endDelimiter = $cmdInfo[2];
	$cmdFamily = $cmdInfo[3];
	
	if ($startDelimiter == null) {
		$pin = substr($cmd, strlen($cmdMnemonic));
	} else {
		$endIndex = strpos($cmd, $endDelimiter, strlen($cmdMnemonic) + strlen($startDelimiter));
		if ($endIndex == -1) {
			return [null, $cmdFamily . '-family command (' . $cmdMnemonic . ') does not end with the expected delimiter "' . $endDelimiter . '"'];
		}
		$pin = substr($cmd, strlen($cmdMnemonic) + strlen($startDelimiter), $endIndex - strlen($cmdMnemonic) - strlen($startDelimiter));
	}
		
	return [$pin, null];
}

# Validates a command's PIN.  If PIN is invalid, returns an error message string; otherwise, returns None.
#------------------------------------------------------------------------------------------------------------------------------
function validatePin($pin) {
	global $pinMinLength, $pinMaxLength;

	# PIN length range limits
	$pinMinLength		= 4;
	$pinMaxLength		= 8;
	
	if (strlen($pin) < $pinMinLength || strlen($pin) > $pinMaxLength) {
		return 'Pin (' . $pin . ') must be ' . $pinMinLength . '-' . $pinMaxLength . ' decimal digits';
	}
	if (!is_numeric($pin)) {
		return 'Pin (' . $pin . ') contains one or more non-decimal characters';
	}	
	return null;
}

# Extracts and validates a command's PIN.
#------------------------------------------------------------------------------------------------------------------------------
function extractValidPin($cmd, $cmdInfo) {
	$result = extractPin($cmd, $cmdInfo);
	$pin = $result[0];
	$errorMsg = $result[1];
	if ($errorMsg == null) {
		$errorMsg = validatePin($pin);
	}
	return [$pin, $errorMsg];
}

# Uses a Cipher-Block-Chaining (CBC) algorithm to encrypt the PIN.  Note the encrypted PIN has the same length as the PIN.
# See https://en.wikipedia.org/wiki/Block_cipher_mode_of_operation#Cipher_Block_Chaining_(CBC) for more information.
#------------------------------------------------------------------------------------------------------------------------------
function encryptPin($cleartextPin) {
	$initVector = 0x44;
	$bytePin    = unpack('C*', $cleartextPin);
	for ($i = 1; $i <= count($bytePin); $i++) {
		$initVector    ^= $bytePin[$i];
		$bytePin[$i] = $initVector;
	}

	return call_user_func_array("pack", array_merge(array("C*"), $bytePin));
}

# Reconstructs command with encrypted PIN
#------------------------------------------------------------------------------------------------------------------------------
function makeEncryptedPinCommand($cmdInfo, $pin)
{
	$cmdMnemonic = $cmdInfo[0];
	$startDelimiter = $cmdInfo[1];
	$endDelimiter = $cmdInfo[2];
	$encryptedPin = encryptPin($pin);
	if ($startDelimiter == null)
		return $cmdMnemonic . $encryptedPin;
	return $cmdMnemonic . $startDelimiter . $encryptedPin . $endDelimiter;
}

# Computes SHA-1 hash of command
#------------------------------------------------------------------------------------------------------------------------------
function hashCommand($encryptedCmd)
{
	return sha1($encryptedCmd);
}

# Inserts hash into middle of encrypted PIN in command
#------------------------------------------------------------------------------------------------------------------------------
function insertHashIntoCommand($encryptedCmd, $pin, $hash, $cmdInfo)
{
	$cmdMnemonic = $cmdInfo[0];
	$startDelimiter = $cmdInfo[1];
	$index = strlen($cmdMnemonic) + floor(strlen($pin) / 2);
	if ($startDelimiter != null)
		$index += strlen($startDelimiter);
	return substr($encryptedCmd, 0, $index) . $hash . substr($encryptedCmd, $index);
}
	
# Validates the lock/unlock command, extracts and validates the PIN, encrypts the PIN, computes a SHA-1 hash of the command
# with its encrypted PIN, inserts the hash into the middle of the command's encrypted PIN, and returns it as a signed command.
#------------------------------------------------------------------------------------------------------------------------------
function signCommand($cmd)
{
	# Determine command type
	// $cmdInfo = determineCommand($cmd);
	$cmdInfo = determineCommand($cmd);
	if ($cmdInfo == null)
		return [null, 'Invalid command (' . $cmd . ') given'];
	# Extract and validate command's PIN
	$result = extractValidPin($cmd, $cmdInfo);
	$pin = $result[0];
	$errorMsg = $result[1];
	if ($errorMsg)
		return [null, $errorMsg];
	# Compute secure hash of command with encrypted PIN and insert hash into middle of encrypted PIN
	$encryptedCmd = makeEncryptedPinCommand($cmdInfo, $pin);
	$hash         = hashCommand($encryptedCmd);
	$signedCmd    = insertHashIntoCommand($encryptedCmd, $pin, $hash, $cmdInfo);
	return [$signedCmd, null];
}



if(isset($_POST['data'])) {

	
	if(!session_id())
	session_start();
	
	$_SESSION['pin'] = $_POST['pin'];

	$pin = intval($_POST['pin']);

	$cmd = 'H$L('.$pin.')';
	$cmd = str_replace('jQuery', '$', $_POST['data']);

	// # Sign the individual command
	$result = signCommand($cmd);

	// Encoding array in JSON format
	echo json_encode($result);

}