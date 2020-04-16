<?php 
#------------------------------------------------------------------------------------------------------------------------------
#	encryptSigner.py
#
#	This Python program encrypts and signs 8x- and 82x-family configuration "lock" and "unlock" commands given
#	on the command line or in a .crb or .crccs file, respectively.
#
#	encryptSigner Use Cases from Confluence "encryptSigner" 
#	======================================================================================================================
#	 1. 82x-family lock command .crccs file		- Encrypt 8x-family lock command's pin and sign command set in input .crb
#												  file.
#												  Lock command must be first command in input file!
#												  Write signed command set to output .crb file with encrypted/signed PIN.
#	 2. 82x-family unlock command .crccs file	- Encrypt 8x-family unlock pin and sign command in input .crb  file.
#												  Unlock command must be only command in input file!
#												  Write signed command to output .crb file with encrypted/signed PIN.
#	 3. 82x-family lock command .crccs file		- Encrypt 82x-family lock command's pin and sign command set in input 
#												  .crccs file.
#												  Lock command must be first command in input file!
#												  Write signed command set to output .crccs file with encrypted/signed 
#												  PIN.
#	 4. 82x-family unlock command .crccs file	- Encrypt 82x-family unlock pin and sign command in input .crccs file.
#												  Unlock command must be only command in input file!
#												  Write signed command to output .crccs file with encrypted/signed PIN.
#	 9. Individual 8x-family lock command		- Encrypt 8x-family lock command pin and sign lock command.
#												  Display signed lock command to user.
#	10. Individual 8x-family unlock command		- Encrypt 8x-family unlock command pin and sign unlock command.
#												  Display signed unlock command to user.
#	11. Individual 82x-family lock command		- Encrypt 82x-family lock command pin and sign lock command.
#												  Display signed lock command to user.
#	12. Individual 82x-family unlock command	- Encrypt 82x-family unlock command pin and sign unlock command.
#												  Display signed unlock command to user.
#
#	Implementation priority order is use cases 11 and 12 first, then 9 and 10, after which the program is released to AEs.
#	Later, if ever, is use cases 3 and 4, followed by 1 and 2.
#
#	Note: This program has methods that return data and/or an error message on detection of an error condition.  These methods
#		  return the data, if available, and the error message, if available, as a tuple.  In all situations, the calling
#		  method should check for the error message first (errorMsg is not None) to perform any error handling, and then check
#		  and/or use the data if there is no error message.  It is possible for both the data and error message to not be None.
#
#		  All error messages are designed for human consumption and the program is architected in such a manner as to pass the
#		  error messages up the call stack where they can be displayed in the appropriate context.  In this manner, methods can
#		  indicate errors and calling (or higher) methods can determine whether and how to handle errors detected at a lower
#		  level.
#------------------------------------------------------------------------------------------------------------------------------
#
#------------------------------------------------------------------------------------------------------------------------------

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



# Extracts 82x commands from input .crccs file and returns a single string containing 82x commands separated by semicolons and
# no error message (commands, None); otherwise, on exception returns (None, errorMsg).
#------------------------------------------------------------------------------------------------------------------------------
#def extract82xCommandsFromCrccsFile(fileName):
#	cmdList = list();
#	try:	# Open and read file one line at a time
#		for crccsLine in open(fileName):
#			# Skip comments and empty lines; anything else is a command
#			crLine = crccsLine.split('//')[0].strip()
#			if len(crLine) > 0:	# Command found; build command string
#				cmdList.append(crLine)
#	except Exception as err:	# File does not exist, etc.
#		return None, 'Opening 82x-commands input file (' + fileName + '); Error: ' + str(err)
#	else:	# File exists and MIGHT have data; semicolon separates the commands
#		return ';'.join(cmdList), None


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
	global $allCommandsInfo;
	
	$cmd = trim($command);
	foreach ($allCommandsInfo as $cmdInfo) {
		$cmdMnemonic = $cmdInfo[0];
		$startDelimiter = $cmdInfo[1];
		if (!$startDelimiter) {
			if (substr_compare($cmdMnemonic, $cmd, 0, strlen($cmdMnemonic)) == 0) {
				return $cmdInfo;
			}
		} else if (substr_compare($cmdMnemonic . $startDelimiter, $cmd, 0, strlen($cmdMnemonic) + strlen($startDelimiter)) == 0) {
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

# Main driver of program
#------------------------------------------------------------------------------------------------------------------------------
$progIndex	= 0;
$cmdIndex	= 1;
$maxParms	= 1;

#	inputName	= None
#	outputName	= None
#	logging		= True
	
$programName	= $argv[$progIndex];
$nArgs			= count($argv) - 1;

# Check for invalid parameter counts
if ($nArgs == 0) {
	displayUsage('No parameters given');
	return 1;
}
if ($nArgs > $maxParms) {
	displayUsage('Too many parameters (' . $nArgs . ') given');
	return 1;
}
# Pull parameters into meaningful variables
$cmd = $argv[$cmdIndex];

# Sign the individual command
$result = signCommand($cmd);
$signedCmd = $result[0];
$errorMsg = $result[1];
if ($errorMsg != null) {
	displayUsage($errorMsg);
	return 2;
}
print($signedCmd);
return 0;

?>